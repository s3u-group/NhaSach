<?php
namespace CongNo\View\Helper;

use Zend\View\Helper\AbstractHelper;

class  GetCongNoQuaHan extends AbstractHelper{

	private $entityManager; 
    
	public function getEntityManager()
    {       
        if(!$this->entityManager)
         {
          $this->entityManager=$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
         }
         return $this->entityManager;
    }
	
	public function setEntityManager($entityManager)
	{
		$this->entityManager=$entityManager;
	}

	
	public function __invoke($idKho){
      $entityManager=$this->getEntityManager();


      // lấy những đối tác thuộc loại khách hàng có công nợ với hệ thống
      
      /*$query=$entityManager->createQuery('SELECT distinct dt.idDoiTac FROM CongNo\Entity\CongNo cn, HangHoa\Entity\DoiTac dt WHERE cn.idDoiTac=dt.idDoiTac and dt.loaiDoiTac=45');
      $doiTacs=$query->getResult();*/
      $query=$entityManager->createQuery('SELECT distinct dt FROM HangHoa\Entity\DoiTac dt WHERE dt.kho='.$idKho.' and dt.loaiDoiTac=45');
      $doiTacs=$query->getResult();

      // duyệt qua từng đối tác là khách hàng lấy ra những dòng công nợ của từng khách hàng và sắp xếp sao cho công nợ gần có ngày xuất phiếu thu (ngày thanh toán) gần ngày hiện tại nhất nằm ở trên
      $response=array();
      foreach ($doiTacs as $doiTac) 
      {   
        //$idDoiTac=$doiTac['idDoiTac'];  
        $idDoiTac=$doiTac->getIdDoiTac();
        if($idDoiTac)
        {
          $thongTinDoiTac=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);
          
          $entityManager=$this->getEntityManager();

          $query = $entityManager->createQuery('SELECT pt FROM HangHoa\Entity\DoiTac kh, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuThu pt  WHERE kh.kho='.$idKho.' and kh.idDoiTac=cn.idDoiTac and cn.idCongNo=pt.idCongNo and pt.kho='.$idKho.' and kh.idDoiTac= :idDoiTac ORDER BY pt.ngayThanhToan DESC, pt.idPhieuThu DESC');        

          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $congNos = $query->getResult(); // array of CmsArticle objects 

          // nếu đã có công nợ trước với hệ thống
          if($congNos)
          {
            $ngayDauKi=$congNos[0]->getNgayThanhToan()->format('Y-m-d');
            $noDauKi=$congNos[0]->getIdCongNo()->getDuNo();
            
          }
          else// khách hàng mới tạo chưa có công nợ với hệ thống lần nào
          {
            // nợ đầu kỳ
            $noDauKi=0;
            $dT=$entityManager->getRepository('HangHoa\Entity\DoiTac')->find($idDoiTac);

            // kiểm tra đối tác này có từng mua hàng ngày nào chưa
            $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.idDoiTac='.$idDoiTac.' and hd.status=0 ORDER BY hd.idHoaDon');
            $hoaDonDauTienCuaDoiTac=$query->getResult();
            if($hoaDonDauTienCuaDoiTac)
            {
                // lấy ngày đăng ký làm ngày đầu kỳ
                $ngayDauKi=$hoaDonDauTienCuaDoiTac[0]->getNgayXuat()->format('Y-m-d');
            }
            else
            {
                // lấy ngày đăng ký làm ngày đầu kỳ
                $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
            }
            
          }

          // lấy nợ phát sinh hoaDon
          $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.kho='.$idKho.' and hd.status=0 and hd.idDoiTac= :idDoiTac');
          $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
          $hoaDons=$query->getResult();

          
          $noPhatSinh=0;
          foreach ($hoaDons as $hoaDon) {
            foreach ($hoaDon->getCtHoaDons() as $ctHoaDon) {
              $noPhatSinh+=(float)$ctHoaDon->getGia()*(float)$ctHoaDon->getSoLuong();
            }
          }

          // tính nợ cuối kỳ
          $noCuoiKi=(float)$noDauKi+(float)$noPhatSinh;

          // tính ngày quá hạn
          $ngayHienTai=date('Y-m-d');
          $datediff = abs(strtotime($ngayDauKi) - strtotime($ngayHienTai));
          $soNgayQuaHan=floor($datediff/(60*60*24));
          
          // nếu có nợ hệ thống và ngày quá hạn hơn 30 thì thông báo
          if($noCuoiKi>0&&$soNgayQuaHan>=30)
          {
            $response[]=array(
                'idKenhPhanPhoi'=>$thongTinDoiTac->getIdKenhPhanPhoi()->getTermTaxonomyId(),
                'idDoiTac'=>$idDoiTac,
                'hoTenDoiTac'=>$thongTinDoiTac->getHoTen(),
                'ngayDauKi'=>$ngayDauKi,
                'noDauKi'=>$noDauKi,
                'noPhatSinh'=>$noPhatSinh,
                'noCuoiKi'=>$noCuoiKi,
                'soNgayQuaHan'=>$soNgayQuaHan,
              );
          }
              
        }
      }

     /* $taxonomyKenhPhanPhoi=$this->TaxonomyFunction();
      $kenhPhanPhois=$taxonomyKenhPhanPhoi->getListChildTaxonomy('kenh-phan-phoi');// đưa vào taxonomy dạng slug
      */
      return array('response'=>$response/*, 'kenhPhanPhois'=>$kenhPhanPhois*/);
		
	}
}
?>