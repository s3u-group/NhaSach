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
use ZfcUser\Options\UserServiceOptionsInterface;
use Zend\Crypt\Password\Bcrypt;

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
        return new ViewModel();
    }

     public function getOptions()
    {
        if (!$this->options instanceof UserServiceOptionsInterface) {
            $this->setOptions($this->getServiceManager()->get('zfcuser_module_options'));
        }
        return $this->options;
    }

    /*public function taoTaiKhoanAction()
    {
    	$this->layout('layout/giaodien');
    	$entityManager=$this->getEntityManager();

    	$form=new TaoTaiKhoanForm($entityManager);   
    	$systemUser=new SystemUser();
        $form->bind($systemUser);
        $request=$this->getRequest();
        if($request->isPost())
        {
            var_dump('post');
            $form->setData($request->getPost());
            if($form->isValid())
            {
                if($request->getPost()['passwordVerify']==$systemUser->getPassword())
                {
                    // $bcrypt = new Bcrypt;
                    // $bcrypt->setCost($this->getOptions()->getPasswordCost());
                    // $systemUser->setPassword($bcrypt->create($user->getPassword()));

                    die(var_dump('thanh'));
                    $entityManager->persist($systemUser);
                    $entityManager->flush();
                }
                else
                {
                    return array(
                        'form'=>$form,
                        'ktMatKhau'=>1,
                    );
                }
            }
            else
            {
                var_dump($form->getMessages());
            }

        }
    	return array(
    		'form'=>$form,
            'ktMatKhau'=>0,
    	);
    }*/
}
