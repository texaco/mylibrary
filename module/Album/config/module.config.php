<?php

return array(
    'service_manager' => array(
        'invokables' => array(
            'Album\Service\DataTableInterface' => 'Album\Service\DataTable',
            'Album\Service\AuthServiceInterface' => 'Zend\Authentication\AuthenticationService',
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Album\Controller\Album' => 'Album\Controller\AlbumController',
            'Album\Controller\Shelve' => 'Album\Controller\ShelveController',
            'Album\Controller\Platform' => 'Album\Controller\PlatformController',
            'Album\Controller\User' => 'Album\Controller\UserController',
            'Album\Controller\Home' => 'Album\Controller\HomeController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'album' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/album[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Album\Controller\Album',
                        'action' => 'index',
                    ),
                ),
            ),
            'shelve' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/shelve[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Album\Controller\Shelve',
                        'action' => 'index',
                    ),
                ),
            ),
            'platform' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/platform[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Album\Controller\Platform',
                        'action' => 'index',
                    ),
                ),
            ),
            'user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Album\Controller\User',
                        'action' => 'index',
                    ),
                ),
            ),
            'home' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/home[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Album\Controller\Home',
                        'action' => 'login',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
);
