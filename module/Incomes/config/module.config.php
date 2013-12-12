<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Incomes\Controller\Incomes' => 'Incomes\Controller\IncomesController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'incomes' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/incomes[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Incomes\Controller\Incomes',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'incomes' => __DIR__ . '/../view',
        ),
    ),
);