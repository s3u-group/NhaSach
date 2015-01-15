<?php
namespace KenhPhanPhoi\View\Helper;

use Zend\View\Helper\AbstractHelper;

class  GetSoPhieuNhapVaPhieuNhapMoiNhat extends AbstractHelper{

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

        $repository = $entityManager->getRepository('HangHoa\Entity\PhieuNhap');
        $queryBuilder = $repository->createQueryBuilder('hd');
        $queryBuilder->add('where','hd.idDoiTac='.$idDoiTac.' ORDER BY hd.ngayNhap, hd.idPhieuNhap DESC');
        $query = $queryBuilder->getQuery();
        $hoaDons = $query->execute();
       
       
        $soHoaDon=count($hoaDons);
        if($hoaDons)
        {
            $hoaDonMoiNhat=$hoaDons[0]->getMaPhieuNhap();
            $idHoaDon=$hoaDons[0]->getIdPhieuNhap();
        }
        else
        {
            $hoaDonMoiNhat='';
            $idHoaDon=0;
        }
        
        $response=$this->searchCongNoNhaCungCap($idDoiTac);
        $noCuoiKi=$response['noCuoiKi'];

        $array=array(
            'soHoaDon'=>$soHoaDon,
            'hoaDonMoiNhat'=>$hoaDonMoiNhat,
            'idHoaDon'=>$idHoaDon,
            'noCuoiKi'=>$noCuoiKi,
        );
        return $array;
		
	}

     public function searchCongNoNhaCungCap($idDoiTac)
      {
        
          if($idDoiTac)
          {
            $entityManager=$this->getEntityManager();
            $query = $entityManager->createQuery('SELECT pc FROM HangHoa\Entity\DoiTac ncc, CongNo\Entity\CongNo cn, CongNo\Entity\PhieuChi pc  WHERE  ncc.idDoiTac=cn.idDoiTac and cn.idCongNo=pc.idCongNo and ncc.idDoiTac= :idDoiTac ORDER BY pc.ngayThanhToan DESC, pc.idPhieuChi DESC');
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

              // lấy ngày đăng ký làm ngày đầu kỳ
              $ngayDauKi=$dT->getNgayDangKy()->format('Y-m-d');
            }

            // lấy nợ phát sinh hoaDon
            $query=$entityManager->createQuery('SELECT pn FROM HangHoa\Entity\PhieuNhap pn WHERE pn.status=0 and pn.idDoiTac= :idDoiTac');
            $query->setParameter('idDoiTac',$idDoiTac);// % đặt ở dưới này thì được đặt ở trên bị lỗi
            $phieuNhaps=$query->getResult();

            
            $noPhatSinh=0;
            foreach ($phieuNhaps as $phieuNhap) {
              foreach ($phieuNhap->getCtPhieuNhaps() as $ctPhieuNhap) {
                $noPhatSinh+=(float)$ctPhieuNhap->getGiaNhap()*(float)$ctPhieuNhap->getSoLuong();
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