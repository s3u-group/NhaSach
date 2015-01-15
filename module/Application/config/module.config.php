<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),        
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ),
    ),

    'controller_plugins' => array(
        'invokables' => array(
            'export_excel' => 'Application\Controller\Plugin\ExportExcel', 
    )), 

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'layout/giaodien'        => __DIR__ . '/../view/layout/giao-dien.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy', //add to use AJAX
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),


    'doctrine' => array(
        'driver' => array(
            // defines an annotation driver with two paths, and names it `my_annotation_driver`
            'application_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Application/Entity',
                ),
            ),

            // default metadata driver, aggregates all other drivers into a single one.
            // Override `orm_default` only if you know what you're doing
            'orm_default' => array(
                'drivers' => array(
                    // register `my_annotation_driver` for any entity under namespace `My\Namespace`
                    'Application\Entity' => 'application_annotation_driver',
                ),
            ),
        ),
    ),

    /*//phân quyền theo zfcuser
    
     'bjyauthorize'=>array(

        'guards'=>array(
            'BjyAuthorize\Guard\Controller'=>array(                
                
                array(
                    'controller'=>array('zfcuser'),  
                    'action'    =>array('login'),                 
                    'roles'     =>array('khach'),
                ),

                array(
                    'controller'=>array('zfcuser'),  
                    'action'    =>array('register','index','logout'),                 
                    'roles'     =>array('nguoi-dung'),
                ),

                array(
                    'controller'=>array('Application\Controller\Index'),
                    'action'    =>array('index','login'),
                    'roles'     =>array('khach'),
                ),  

                array(
                    'controller'=>array('CongNo\Controller\Index'),
                    'action'    =>array('index','thanhToan','congNoNhaCungCap','thanhToanNhaCungCap'),
                    'roles'     =>array('nguoi-dung'),
                ),              
               
                array(
                    'controller'=>array('HangHoa\Controller\Index'),
                    'action'    =>array('index','hangHoa','locHangHoa','sanPham','bangGia','nhapHang','xuatHang','themSanPham','searchKhachHang','searchSanPham','searchNhaCungCap','importHangHoa','importBangGia','exportHangHoa','exportBangGia','xoaSanPham'),
                    'roles'     =>array('nguoi-dung'),
                ),

                
                

                array(
                    'controller'=>array('KenhPhanPhoi\Controller\Index'),
                    'action'    =>array('index','nhaCungCap','chiTietDonHang','chiTietPhieuNhap','themKhachHang','themNhaCungCap','chiTietKhachHang','chiTietNhaCungCap','xoaKhachHang','xoaNhaCungCap','exportKhachHang','exportNhaCungCap'),
                    'roles'     =>array('nguoi-dung'),
                ),

                array(
                    'controller'=>array('LoiNhuan\Controller\Index'),
                    'action'    =>array('index','donHang','doanhThuTheoThang','doanhThuTheoQuy','doanhThuTheoNam','chiTietDoanhThuNgay','chiTietDoanhThuThang','chiTietDoanhThuQuy','chiTietDoanhThuNam','exportDoanhThuTheoNgay','exportDoanhThuTheoThang','exportDoanhThuTheoQuy','exportDoanhThuTheoNam','exportDonHang'),
                    'roles'     =>array('nguoi-dung'),
                ),

                array(
                    'controller'=>array('S3UTaxonomy\Controller\Index'),
                    'action'    =>array('index','edit'),
                    'roles'     =>array('nguoi-dung'),
                ),

                array(
                    'controller'=>array('S3UTaxonomy\Controller\Taxonomy'),
                    'action'    =>array('taxonomyIndex','taxonomyEdit','taxonomyAdd'),
                    'roles'     =>array('nguoi-dung'),
                ),
              
            ),
        ),
    ),*/

    //phân quyền theo chuyển hướng
    
     'bjyauthorize'=>array(

        'guards'=>array(
            'BjyAuthorize\Guard\Controller'=>array(                
                
                array(
                    'controller'=>array('zfcuser'),  
                    //'action'    =>array('index','login','logout'),                 
                    'roles'     =>array('nguoi-dung','khach'),
                ),

                array(
                    'controller'=>array('Application\Controller\Index'),
                    //'action'    =>array('index','login'),
                    'roles'     =>array('khach','nguoi-dung'),
                ),  

                array(
                    'controller'=>array('CongNo\Controller\Index'),
                    //'action'    =>array('index','thanhToan','congNoNhaCungCap','thanhToanNhaCungCap'),
                    'roles'     =>array('nguoi-dung','khach'),
                ),              
               
                array(
                    'controller'=>array('HangHoa\Controller\Index'),
                    //'action'    =>array('index','hangHoa','locHangHoa','sanPham','bangGia','nhapHang','xuatHang','themSanPham','searchKhachHang','searchSanPham','searchNhaCungCap','importHangHoa','importBangGia','exportHangHoa','exportBangGia','xoaSanPham'),
                    'roles'     =>array('nguoi-dung','khach'),
                ),               
                

                array(
                    'controller'=>array('KenhPhanPhoi\Controller\Index'),
                    //'action'    =>array('index','nhaCungCap','chiTietDonHang','chiTietPhieuNhap','themKhachHang','themNhaCungCap','chiTietKhachHang','chiTietNhaCungCap','xoaKhachHang','xoaNhaCungCap','exportKhachHang','exportNhaCungCap'),
                    'roles'     =>array('nguoi-dung','khach'),
                ),

                array(
                    'controller'=>array('LoiNhuan\Controller\Index'),
                    //'action'    =>array('index','donHang','doanhThuTheoThang','doanhThuTheoQuy','doanhThuTheoNam','chiTietDoanhThuNgay','chiTietDoanhThuThang','chiTietDoanhThuQuy','chiTietDoanhThuNam','exportDoanhThuTheoNgay','exportDoanhThuTheoThang','exportDoanhThuTheoQuy','exportDoanhThuTheoNam','exportDonHang'),
                    'roles'     =>array('nguoi-dung','khach'),
                ),

                array(
                    'controller'=>array('S3UTaxonomy\Controller\Index'),
                    'action'    =>array('index'),
                    'roles'     =>array('nguoi-dung'),
                ),

                array(
                    'controller'=>array('S3UTaxonomy\Controller\Taxonomy'),
                    'action'    =>array('taxonomyIndex','taxonomyEdit'),
                    'roles'     =>array('nguoi-dung'),
                ),

                array(
                    'controller'=>array('Kho\Controller\Index'),
                    //'action'    =>array('index'),
                    'roles'     =>array('nguoi-dung','khach'),
                ),
              
            ),
        ),
    ),
   
);
