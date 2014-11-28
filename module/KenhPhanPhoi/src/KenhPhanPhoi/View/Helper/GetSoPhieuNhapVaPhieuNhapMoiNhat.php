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
        
        $array=array(
            'soHoaDon'=>$soHoaDon,
            'hoaDonMoiNhat'=>$hoaDonMoiNhat,
            'idHoaDon'=>$idHoaDon,
        );
        return $array;
		
	}
}
?>