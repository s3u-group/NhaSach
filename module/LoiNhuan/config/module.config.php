<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'LoiNhuan\Controller\Index' => 'LoiNhuan\Controller\IndexController',            
		),
	),
    'router' => array(
        'routes' => array(
            'loi_nhuan' => array(
                'type'    => 'literal', 
                'options' => array(
                    'route'    => '/loi-nhuan',
                    'defaults' => array(
                       '__NAMESPACE__'=>'LoiNhuan\Controller',
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
			'loi_nhuan' => __DIR__ . '/../view'
		),        
	),    

	/*'doctrine' => array(
        'driver' => array(
            'loi_nhuan_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__.'/../src/LoiNhuan/Entity',//Edit
                ),
            ),

            'orm_default' => array(
                'drivers' => array(

                    'LoiNhuan\Entity' => 'loi_nhuan_annotation_driver',//Edit
                )
            )
        )
    ),*/
);