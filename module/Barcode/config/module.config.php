<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'Barcode\Controller\Index' => 'Barcode\Controller\IndexController',            
		),
	),
    'router' => array(
        'routes' => array(
            'barcode' => array(
                'type'    => 'literal', 
                'options' => array(
                    'route'    => '/barcode',                     
                    'defaults' => array(
                       '__NAMESPACE__'=>'Barcode\Controller',
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
			'barcode' => __DIR__ . '/../view'
		),
        /*'template_map'=>array(
            'layout/giaodien'        => __DIR__ . '/../view/layout/giao-dien.phtml',
        ),*/
	),

    /*'view_helpers'=>array(
        'invokables'=>array(
            'make_array_option_taxonomy'=>'HangHoa\View\Helper\MakeArrayOptionTaxonomy',
        ),
        'factories'=>array(
            'in_gia_xuat' => function($sm){
                $entityManager=$sm->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $doctrineInGiaXuatHelper=new \HangHoa\View\Helper\InGiaXuat();
                $doctrineInGiaXuatHelper->setEntityManager($entityManager);
                return $doctrineInGiaXuatHelper;
            },
        ), 
    ),*/    

	'doctrine' => array(
        'driver' => array(
            'barcode_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__.'/../src/Barcode/Entity',//Edit
                ),
            ),

            'orm_default' => array(
                'drivers' => array(

                    'Barcode\Entity' => 'barcode_annotation_driver'//Edit
                )
            )
        )
    ),    
);