<?php
return array(
     'controllers' => array(
         'invokables' => array(
             'Kho\Controller\Index' => 'Kho\Controller\IndexController',

         ),
     ),

     // The following section is new and should be added to your file
     'router' => array(
        'routes' => array(
            'kho' => array(
                'type'    => 'literal', 
                'options' => array(
                    'route'    => '/kho',                     
                    'defaults' => array(
                       '__NAMESPACE__'=>'Kho\Controller',
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
             'kho' => __DIR__ . '/../view',
         ),
    
     ),

     'view_helpers'=>array(
        'factories'=>array(
            
            'get_kho' => function($sm){
                $entityManager=$sm->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $getKhoHelper=new \Kho\View\Helper\GetKho();
                $getKhoHelper->setEntityManager($entityManager);
                return $getKhoHelper;
            },

            'get_ten_kho' => function($sm){
                $entityManager=$sm->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $getTenKhoHelper=new \Kho\View\Helper\GetTenKho();
                $getTenKhoHelper->setEntityManager($entityManager);
                return $getTenKhoHelper;
            },
        ), 
        
    ),     


    'doctrine' => array(
        'driver' => array(
            'kho_annotation_driver' => array(// edit
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__.'/../src/Kho/Entity',//Edit
                ),
            ),

            'orm_default' => array(
                'drivers' => array(

                    'Kho\Entity' => 'kho_annotation_driver'//Edit
                )
            )
        )
    ),
);
?>