<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'HangHoa\Controller\Index' => 'HangHoa\Controller\IndexController',            
		),
	),
    'router' => array(
        'routes' => array(
            'hang_hoa' => array(
                'type'    => 'literal', 
                'options' => array(
                    'route'    => '/hang-hoa',                     
                    'defaults' => array(
                       '__NAMESPACE__'=>'HangHoa\Controller',
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
			'hang_hoa' => __DIR__ . '/../view'
		),
        'template_map'=>array(
            'layout/giaodien'        => __DIR__ . '/../view/layout/giao-dien.phtml',
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