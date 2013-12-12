<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Outcomes\Controller\Outcomes' => 'Outcomes\Controller\OutcomesController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'outcomes' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/outcomes[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Outcomes\Controller\Outcomes',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'outcomes' => __DIR__ . '/../view',
        ),
    ),
);