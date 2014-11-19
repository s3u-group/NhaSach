<?php namespace KenhPhanPhoi\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
 use Zend\ServiceManager\ServiceManager;
 
 class IndexController extends AbstractActionController
 {
 	private $entityManager;
  
  public function getEntityManager()
  {
     if(!$this->entityManager)
     {
      //$this->entityManager=$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
     }
     return $this->entityManager;
  }
  
 	public function indexAction()
 	{
    	$this->layout('layout/giaodien');
 	}

 	public function donHangAction()
 	{
    	$this->layout('layout/giaodien');
 	}

 	public function doanhThuAction()
 	{
    	$this->layout('layout/giaodien');
 	}

 	public function themKhachHangAction()
 	{
    	$this->layout('layout/giaodien');
 	}
 }
?>