<?php
	namespace Kho\Controller;

	 use Zend\Mvc\Controller\AbstractActionController;
	 use Zend\View\Model\ViewModel;
	 use Kho\Entity\Kho;
	 use Kho\Form\ThemKhoForm;
	
	 class IndexController extends AbstractActionController
	 {
	 	private $entityManager;

	 	public function getEntityManager()
	    {
	    // kiểm tra đăng nhập
	     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
	     {
	       return $this->redirect()->toRoute('application');
	     }

	      if(!$this->entityManager)
	      {
	       $this->entityManager=$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
	      }
	      return $this->entityManager;
	    }
	 	
	     public function indexAction()
	     {	
	     	 // kiểm tra đăng nhập
		     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
		     {
		       return $this->redirect()->toRoute('application');
		     }
 
	     	$this->layout('layout/giaodien');  
	     	$entityManager=$this->getEntityManager();

	     	$khos=$entityManager->getRepository('Kho\Entity\Kho')->findAll();
	     	return array(
	     		'khos'=>$khos,
	     	);
	     }


	     public function themKhoConAction()
	     {	 
	     	// kiểm tra đăng nhập
		     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
		     {
		       return $this->redirect()->toRoute('application');
		     }
	     	$this->layout('layout/giaodien');  
	     	$entityManager=$this->getEntityManager();
	     	$form=new ThemKhoForm($entityManager);
	     	$kho=new Kho();
	     	$form->bind($kho);

	     	$khos=$entityManager->getRepository('Kho\Entity\Kho')->findAll();

	     	$request=$this->getRequest();
	     	if($request->isPost())
	     	{
	     		$form->setData($request->getPost());
	     		if($form->isValid())
	     		{
	     			$query=$entityManager->createQuery('SELECT k FROM Kho\Entity\Kho k WHERE k.tenKho=\''.$kho->getTenKho().'\'');
	     			$kiemTraTonTai=$query->getResult();
	     			if($kiemTraTonTai)
	     			{
	     				return array(
				     		'form'=>$form,
				     		'khos'=>$khos,
				     		'ktKhoTonTai'=>1,
				     	);
	     			}
	     			$entityManager->persist($kho);
	     			$entityManager->flush();
	     			$this->flashMessenger()->addSuccessMessage('Thêm chi nhánh mới thành công');
	     			return $this->redirect()->toRoute('kho/crud',array('action'=>'index'));
	     		}	     		
	     	}

	     	
	     	return array(
	     		'form'=>$form,
	     		'khos'=>$khos,
	     		'ktKhoTonTai'=>0,
	     	);
	     }


	     public function suaChiNhanhAction()
	     {	 
	     	// kiểm tra đăng nhập
		     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
		     {
		       return $this->redirect()->toRoute('application');
		     }

		     // lấy id
	         $id = (int) $this->params()->fromRoute('id', 0);
		     if (!$id) {
		           return $this->redirect()->toRoute('kho/crud');
		     } 
		     // tạo layout 
	     	$this->layout('layout/giaodien');  
	     	$entityManager=$this->getEntityManager();

	     	// tạo form
	     	$form=new ThemKhoForm($entityManager);
	     	$kho=$entityManager->getRepository('Kho\Entity\Kho')->find($id);


	     	if(!$kho)
	     	{
	     		$this->flashMessenger()->addErrorMessage('Không tìm thấy chi nhánh này! Vui lòng kiểm tra lại!');
	     		return $this->redirect()->toRoute('kho/crud',array('action'=>'index'));
	     	}
	     	$idKhoTruoc=$kho->getIdKho();
	     	$form->bind($kho);
	     	

	     	$request=$this->getRequest();
	     	if($request->isPost())
	     	{
	     		$form->setData($request->getPost());
	     		if($form->isValid())
	     		{
	     			$query=$entityManager->createQuery('SELECT k FROM Kho\Entity\Kho k WHERE k.tenKho=\''.$kho->getTenKho().'\'');
	     			$kiemTraTonTai=$query->getResult();	     			
	     			if(!$kiemTraTonTai||($kiemTraTonTai&&$kiemTraTonTai[0]->getIdKho()==$idKhoTruoc))
	     			{
		     			$entityManager->flush();
		     			$this->flashMessenger()->addSuccessMessage('Cập nhật thành công');
		     			return $this->redirect()->toRoute('kho/crud',array('action'=>'index'));
	     			}
	     			else
	     			{
	     				return array(
				     		'form'=>$form,
				     		'kho'=>$kho,
				     		'ktKhoTonTai'=>1,
				     	);
	     			}	     			
	     		}
	     		else{     			

	     			$this->flashMessenger()->addErrorMessage('Cập nhật thất bại');
	     			return $this->redirect()->toRoute('kho/crud',array('action'=>'index'));
	     		}	     		
	     	}

	     	return array(
	     		'form'=>$form,
	     		'kho'=>$kho,
	     		'ktKhoTonTai'=>0,
	     	);
	     }


	     public function xemKhoAction()
	     {
	     	// kiểm tra đăng nhập
		     if(!$this->zfcUserAuthentication()->hasIdentity()||$this->zfcUserAuthentication()->getIdentity()->getId()!=1)
		     {
		       return $this->redirect()->toRoute('application');
		     }

		    $id = (int) $this->params()->fromRoute('id', 0);
		    if (!$id) 
		    {
		        return $this->redirect()->toRoute('hang_hoa/crud');
		    }  
	     	$this->layout('layout/giaodien');
	     	$entityManager=$this->getEntityManager();

	     	$admin=$entityManager->getRepository('Application\Entity\SystemUser')->find(1);
	     	if($admin)
	     	{
	     		$admin->setKho($id);
	     		$entityManager->flush();
	     		
	     	}
	     	return $this->redirect()->toRoute('hang_hoa/crud');


	     }

	        
	 }
?>