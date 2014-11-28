<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'KenhPhanPhoi\Controller\Index' => 'KenhPhanPhoi\Controller\IndexController',            
		),
	),
    'router' => array(
        'routes' => array(
            'kenh_phan_phoi' => array(
                'type'    => 'literal', 
                'options' => array(
                    'route'    => '/kenh-phan-phoi',                     
                    'defaults' => array(
                       '__NAMESPACE__'=>'KenhPhanPhoi\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(                    
                    'crud' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/][:action][/:id]',
                            'constraints' => array(                            
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'=>'[0-9]+',
                            ),                            
                        ),
                    ),            
                ),
             ),
         ),
     ),    

   

	'view_manager' => array(
		'template_path_stack' => array(
			'kenh_phan_phoi' => __DIR__ . '/../view'
		),        
	),  

    'view_helpers'=>array(
        'invokables'=>array(
            'make_array_option_taxonomy'=>'KenhPhanPhoi\View\Helper\MakeArrayOptionTaxonomy',
        ),
        'factories'=>array(
            'get_so_hoa_don_va_hoa_don_moi_nhat' => function($sm){
                $entityManager=$sm->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $doctrineKenhPhanPhoiHelper=new \KenhPhanPhoi\View\Helper\GetSoHoaDonVaHoaDonMoiNhat();
                $doctrineKenhPhanPhoiHelper->setEntityManager($entityManager);
                return $doctrineKenhPhanPhoiHelper;
            },

            'get_so_phieu_nhap_va_phieu_nhap_moi_nhat' => function($sm){
                $entityManager=$sm->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $doctrineKenhPhanPhoiHelper=new \KenhPhanPhoi\View\Helper\GetSoPhieuNhapVaPhieuNhapMoiNhat();
                $doctrineKenhPhanPhoiHelper->setEntityManager($entityManager);
                return $doctrineKenhPhanPhoiHelper;
            },
        ), 
        
    ),     
);