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
