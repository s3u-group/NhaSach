<?php
namespace Kho\View\Helper;

use Zend\View\Helper\AbstractHelper;

class  GetTenKho extends AbstractHelper{

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

        $kho = $entityManager->getRepository('Kho\Entity\Kho')->find($idKho);        
 
        return $kho;
		
	}
}
?>