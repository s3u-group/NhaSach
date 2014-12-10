<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Entity\SystemUser;
use Application\Form\SystemUserFieldset;
use Application\Form\TaoTaiKhoanForm;

class IndexController extends AbstractActionController
{
	private $entityManager;
    protected $options;
  
  	public function getEntityManager()
  	{
	     if(!$this->entityManager)
	     {
	      $this->entityManager=$this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
	     }
	     return $this->entityManager;
  	}

    public function indexAction()
    {
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();

     
      // kiểm tra đăng nhập
      if($this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('hang_hoa');
      }
      return new ViewModel();
    }
    
    public function loginAction()
    {

      // kiểm tra đăng nhập
      if($this->zfcUserAuthentication()->hasIdentity())
      {
        return $this->redirect()->toRoute('hang_hoa');
      }

      $this->layout('layout/giaodien');
       return $this->forward()->dispatch('zfcuser', array(
           'action' => 'login'
       ));
    } 

    public function logoutAction()
    {
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();

       // kiểm tra nếu là tài khoản admin thì reset lại kho cho admin đó
       // kiểm tra đăng nhập
      if($this->zfcUserAuthentication()->hasIdentity()&&$this->zfcUserAuthentication()->getIdentity()->getId()==1)
      { 
        $admin=$entityManager->getRepository('Application\Entity\SystemUser')->find(1);
        if($admin)
        {
          $admin->setKho(1);
          $entityManager->flush();          
        }
      }
      return $this->redirect()->toRoute('zfcuser/logout',array('action'=>'logout'));
    }   
}
