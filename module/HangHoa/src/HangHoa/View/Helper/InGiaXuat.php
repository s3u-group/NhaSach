<?php
namespace HangHoa\View\Helper;

use Zend\View\Helper\AbstractHelper;

class  InGiaXuat extends AbstractHelper{

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

	
	public function __invoke($idSanPham, $idKenhPhanPhoi){
        $array=array();
        $entityManager=$this->getEntityManager();

        $repository = $entityManager->getRepository('HangHoa\Entity\GiaXuat');
        $queryBuilder = $repository->createQueryBuilder('gx');
        $queryBuilder->add('where','gx.idSanPham='.$idSanPham.' and gx.idKenhPhanPhoi='.$idKenhPhanPhoi);
        $query = $queryBuilder->getQuery();
        $giaXuats = $query->execute();
        if($giaXuats)
        {
          return $giaXuats[0]->getGiaXuat();
        }
        else
        {
          return null;
        }
        
       
	}
}
?>