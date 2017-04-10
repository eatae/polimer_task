<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'News\Controller\News' => 'News\Controller\NewsController',
            'News\Controller\NewsAdmin' => 'News\Controller\NewsAdminController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'news' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/news[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'News\Controller\News',
                        'action' => 'index',
                    ),
                ),
            ),
            'news-admin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/news-admin[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'News\Controller\NewsAdmin',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'news' => __DIR__ . '/../view',
        ),
    ),
);