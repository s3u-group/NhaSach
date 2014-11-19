<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'CongNo\Controller\Index' => 'CongNo\Controller\IndexController',            
		),
	),
    'router' => array(
        'routes' => array(
            'cong_no' => array(
                'type'    => 'literal', 
                'options' => array(
                    'route'    => '/cong-no',                     
                    'defaults' => array(
                       '__NAMESPACE__'=>'CongNo\Controller',
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
			'cong_no' => __DIR__ . '/../view'
		),        
	),    

	/*'doctrine' => array(
        'driver' => array(
            'hang_hoa_annotation_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__.'/../src/HangHoa/Entity',//Edit
                ),
            ),

            'orm_default' => array(
                'drivers' => array(

                    'HangHoa\Entity' => 'hang_hoa_annotation_driver'//Edit
                )
            )
        )
    ),*/
);