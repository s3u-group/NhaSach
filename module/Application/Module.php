<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // khúc này phải có

        $services = $e->getApplication()->getServiceManager();       
        $zfcServiceEvents = $services->get('zfcuser_user_service')->getEventManager();


        // Store the field
        $zfcServiceEvents->attach('register', function($e) use($services) {
            $user=$e->getParam('user');//lấy người dùng hiện tại đang đăng ký ở event
            $em=$services->get('Doctrine\ORM\EntityManager');// lệnh kết nôi doctrine orm
            $defaultUserRole=$em->getRepository('Application\Entity\Role')// kết nối tới file Role trong danh mục
                                ->findOneBy(array('roleId'=>'nguoi-dung'));// lấy lấy 1 dòng có roleId có tên là người dùng
            $user->addRole($defaultUserRole);//
            
        });

        //  form register
        $events = $e->getApplication()->getEventManager()->getSharedManager();

        $events->attach('ZfcUser\Form\Register','init', function($e) {
            $form = $e->getTarget();
            // Do what you please with the form instance ($form)    
              
            $form->add(array(
                'name' => 'displayName',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Display Name ',
                ),
            ));

            $form->add(array(
                'name' => 'hoTen',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Full Name ',
                ),
            ));

            $form->add(array(
                'name' => 'diaChi',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Address',
                ),
            ));

            /*$form->add(array(
                'name' => 'state',
                'type' => 'Text',
                'options' => array(
                    'label' => 'state',
                    'value'=>0,
                ),
            ));*/
            $form->add(array(
                'name' => 'moTa',
                'type' => 'TextArea',
                'options' => array(
                    'label' => 'Description',                  
                ), 
                'attributes'=>array(
                    //'id'=>'editor',
                ),
            ));

            $form->add(array(
                'name' => 'dienThoaiCoDinh',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Home Phone',                  
                ),
            ));

             $form->add(array(
                'name' => 'diDong',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Phone Numeber',                
                ),
            ));

            $form->add(array(
                'name' => 'twitter',
                'type' => 'Text',
                'options' => array(
                    'label' => 'Twitter',                  
                ),
            ));
        
        });


        $events->attach('ZfcUser\Form\RegisterFilter','init', function($e) {
           
        });

        $zfcServiceEvents->attach('register', function($e) {
            $user = $e->getParam('user');  // User account object
            $form = $e->getParam('form');  // Form object
            
        });

        $zfcServiceEvents->attach('register.post', function($e) {
            $user = $e->getParam('user');
        });

    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
