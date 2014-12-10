<?php
namespace Kho\View\Helper;

use Zend\View\Helper\AbstractHelper;

class  GetKho extends AbstractHelper{

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

	
	public function __invoke(){
        $array=array();
        $entityManager=$this->getEntityManager();

        $khos = $entityManager->getRepository('Kho\Entity\Kho')->findAll();
        
        foreach ($khos as $kho) {
            $array[$kho->getIdKho()]=$kho->getTenKho();
        }      
 
        return $array;
		
	}
}
?>