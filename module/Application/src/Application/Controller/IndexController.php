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
use Application\Form\ResetPasswordForm;
use Zend\Crypt\Password\Bcrypt;


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

    public function quanLyTaiKhoanAction()
    {
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();

       // kiểm tra nếu là tài khoản admin thì reset lại kho cho admin đó
       // kiểm tra đăng nhập
      if($this->zfcUserAuthentication()->hasIdentity()&&$this->zfcUserAuthentication()->getIdentity()->getId()==1)
      { 
        $listUsers=$entityManager->getRepository('Application\Entity\SystemUser')->findAll();
        return array(
          'listUsers'=>$listUsers,
        );
      }
      else{
        $this->flashMessenger()->addErrorMessage('Xin lỗi, bạn không có quyền truy cập tài nguyên này!');
        return $this->redirect()->toRoute('hang_hoa/crud');
      }

    }  

    public function resetPasswordAction()
    {
      $id = (int) $this->params()->fromRoute('id', 0);
      if (!$id) {
          return $this->redirect()->toRoute('application/default',array('controller'=>'index','action'=>'quan-ly-tai-khoan'));
      } 
      $this->layout('layout/giaodien');
      $entityManager=$this->getEntityManager();
       // kiểm tra nếu là tài khoản admin thì reset lại kho cho admin đó
       // kiểm tra đăng nhập
      if($this->zfcUserAuthentication()->hasIdentity()&&$this->zfcUserAuthentication()->getIdentity()->getId()==1)
      { 
        $user=$entityManager->getRepository('Application\Entity\SystemUser')->find($id);
        $form= new ResetPasswordForm($entityManager);
        $form->bind($user);
        $request=$this->getRequest();      
        if($request->isPost())
        {        
          $form->setData($request->getPost());
          if($form->isValid())
          {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost(14);
            $password = $user->getPassword();            
            $user->setPassword ($bcrypt->create($password));
            $entityManager->flush();
            $this->flashMessenger()->addSuccessMessage('Reset mật khẩu thành công!');
            return $this->redirect()->toRoute('application/default',array('controller'=>'index','action'=>'quan-ly-tai-khoan'));
          }
          else{
            $this->flashMessenger()->addSuccessMessage('Reset mật khẩu thất bại!');
            return $this->redirect()->toRoute('application/default',array('controller'=>'index','action'=>'quan-ly-tai-khoan'));
          }
        }          
        return array(
          'form'=>$form,
          'user'=>$user,
        );
        
      }
      else{
        $this->flashMessenger()->addErrorMessage('Xin lỗi, bạn không có quyền truy cập tài nguyên này!');
        return $this->redirect()->toRoute('hang_hoa/crud');
      }

    }   
}
