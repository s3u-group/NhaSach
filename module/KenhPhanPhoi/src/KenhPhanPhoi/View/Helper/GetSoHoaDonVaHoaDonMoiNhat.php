<?php
namespace KenhPhanPhoi\View\Helper;

use Zend\View\Helper\AbstractHelper;

class  GetSoHoaDonVaHoaDonMoiNhat extends AbstractHelper{

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

	
	public function __invoke($idDoiTac){
        $array=array();
        $entityManager=$this->getEntityManager();

        $repository = $entityManager->getRepository('HangHoa\Entity\HoaDon');
        $queryBuilder = $repository->createQueryBuilder('hd');
        $queryBuilder->add('where','hd.idDoiTac='.$idDoiTac.' ORDER BY hd.ngayXuat, hd.idHoaDon DESC');
        $query = $queryBuilder->getQuery();
        $hoaDons = $query->execute();
       
       
        $soHoaDon=count($hoaDons);
        if($hoaDons)
        {
            $hoaDonMoiNhat=$hoaDons[0]->getMaHoaDon();
            $idHoaDon=$hoaDons[0]->getIdHoaDon();
            
        }
        else
        {
            $hoaDonMoiNhat='';
            $idHoaDon=0;
        }
        
        $response=$this->searchCongNoKhachHang($idDoiTac);
        $noCuoiKi=$response['noCuoiKi'];

        $array=array(
            'soHoaDon'=>$soHoaDon,
            'hoaDonMoiNhat'=>$hoaDonMoiNhat,
            'idHoaDon'=>$idHoaDon,
            'noCuoiKi'=>$noCuoiKi,
        );
        return $array;
		
	}

     public function searchCongNoKhachHang($idDoiTac)
      {
        

          $response=array();
       
          if($idDoiTac)
          {
            $entityManager=$this->getEntityManager();
            $query = $entityManager->createQuery('SELECT pt FROM HangHoa\Entity\DoiTac kh, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuThu pt  WHERE kh.idDoiTac=cn.idDoiTac and cn.idCongNo=pt.idCongNo and kh.idDoiTac= :idDoiTac ORDER BY pt.ngayThanhToan DESC, pt.idPhieuThu DESC');
            $query->setParameter('idDoiTac',$idDoiTac);
            
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

              // lấy ngày đăng ký làm ngày đầu kỳ
              $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
            }

            // lấy nợ phát sinh hoaDon
            $query=$entityManager->createQuery('SELECT hd FROM HangHoa\Entity\HoaDon hd WHERE hd.status=0 and hd.idDoiTac= :idDoiTac');
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
            $response=array(
              'ngayDauKi'=>$ngayDauKi,
              'noDauKi'=>$noDauKi,
              'noPhatSinh'=>$noPhatSinh,
              'noCuoiKi'=>$noCuoiKi,
            );
          }
        return $response;
      }
  }
?>