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
    ),     

	/*'doctrine' => array(
        'driver' => array(
            'kenh_phan_phoi_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__.'/../src/KenhPhanPhoi/Entity',//Edit
                ),
            ),

            'orm_default' => array(
                'drivers' => array(
                    'KenhPhanPhoi\Entity' => 'kenh_phan_phoi_annotation_driver',//Edit
                )
            )
        )
    ),*/
);