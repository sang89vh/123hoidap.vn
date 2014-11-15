<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Index' => 'Admin\Controller\IndexController',
            'Admin\Controller\News' => 'Admin\Controller\NewsController',

            'catalog' => 'Admin\Controller\CatalogController',
            'Admin\Controller\Phpjob' => 'Admin\Controller\PhpjobController',
            'Admin\Controller\Question' => 'Admin\Controller\QuestionController',
            'Admin\Controller\Report' => 'Admin\Controller\ReportController',
            'Admin\Controller\Member' => 'Admin\Controller\MemberController'
        )

    ),
    'router' => array(
        'routes' => array(
            'home_admin' => array(
                'type' => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route' => '/admin',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    )
                )
            ),
            'catalog' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/catalog[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'catalog',
                        'action' => 'index'
                    )
                )
            ),
            'news' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/news[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\News',
                        'action' => 'index'
                    )
                )
            ),
            'phpjobs' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/phpjob[/][:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Phpjob',
                        'action' => 'manager-keyword'
                    )
                )
            ),
            'admin_question' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/question[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Question',
                        'action' => 'index'
                    )
                )

            ),
            'report' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/report[/][:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Report',
                        'action' => 'index'
                    )
                )
            ),
            'admin_member' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/member[/][:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Member',
                        'action' => 'index'
                    )
                )
            )
        )
    ),
    'navigation' => array(
        'navigation_admin' => array(
            array(
                'label' => 'Từ khóa',
                'id' => 'faq-keyword',
                'class' => 'faq-menu-admin',
                'route' => 'phpjobs',
                'pages' => array(
                    array(
                        'label' => 'Khởi tạo từ khóa',
                        'route' => 'phpjobs',
                        'action' => 'init-keyword'
                    ),
                    array(
                        'label' => 'Cập nhật từ khóa',
                        'route' => 'phpjobs',
                        'action' => 'upate-keyword'
                    )
                )
            ),
            array(
                'label' => 'Tin',
                'id' => 'faq-news',
                'class' => 'faq-menu-admin',
                'route' => 'news'
            )
        )

    )
    ,
    'service_manager' => array(
        'factories' => array(
            'navigation_admin' => 'FAQ\View\AdminNavigatorFactory'
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Admin' => __DIR__ . '/../view'
        )
    )
);
