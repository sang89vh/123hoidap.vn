<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'qapolo' => array(
            		'type' => 'Zend\Mvc\Router\Http\Literal',
            		'options' => array(
            				'route' => '/',
            				'defaults' => array(
            						'controller' => 'Web\Controller\Home',
            						'action' => 'question'
            				)
            		)
            ),
            'question' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/question[/][:action][/:id][/:urlseo]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9a-zA-Z]*',
                        'urlseo' => '[0-9a-zA-Z-.%]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Question',
                        'action' => 'index'
                    )
                )
            ),
            'home' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/home[/][:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Home',
                        'action' => 'question'
                    )
                )
            ),
            'answer' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/answer[/][:action][/:urlseo][/:answerID]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'urlseo' => '[0-9a-zA-Z-.]*',
                        'answerID' => '[0-9a-zA-Z]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Answer',
                        'action' => 'index'
                    )
                )
            ),
            'comment' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/comment[/][:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Comment',
                        'action' => 'index'
                    )
                )
            ),
            'tag' => array(
                'type' => 'segment',
                'options' => array(
//                     'route' => '/tag[/]',
                    'route' => '/tag[/][:action][/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page' => '[0-9a-zA-Z]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Tag',
                        'action' => 'index'
                    )
                )
            ),
            // 'question' => array(
            // 'type' => 'Zend\Mvc\Router\Http\Literal',
            // 'options' => array(
            // 'route' => '/question',
            // 'defaults' => array(
            // 'controller' => 'Web\Controller\Question',
            // 'action' => 'index'
            // )
            // )
            // ),
            'subject' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/subject[/][:action][/:tab][/:id][/:urlseo]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'tab' => '[0-9a-zA-Z]*',
                        'id' => '[0-9a-zA-Z]*',
                        'urlseo' => '[0-9a-zA-Z-.]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Subject',
                        'action' => 'index'
                    )
                )
            ),
            'member' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/member[/][:action][/:id][/:urlseo]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9a-zA-Z]*',
                        'urlseo' => '[0-9a-zA-Z-.]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Member',
                        'action' => 'index'
                    )
                )
            ),
            'message' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/message[/][:action][/:tab]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'tab' => '[0-9a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Message',
                        'action' => 'index'
                    )
                )
            ),
//             'setting' => array(
//                 'type' => 'Zend\Mvc\Router\Http\Segment',
//                 'options' => array(
//                     'route' => '/setting[/:controller[/:action]]',
//                     'constraints' => array(
//                         'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
//                     ),
//                     'defaults' => array(
//                         'controller' => 'Web\Controller\User',
//                         'action' => 'about'
//                     )
//                 )
//             ),
            'about' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/about',
                    'defaults' => array(
                        'controller' => 'Web\Controller\About',
                        'action' => 'index'
                    )
                )
            ),
            'user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\User',
                        'action' => 'about'
                    )
                )
            ),
            'review' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/review[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9a-zA-Z]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Review',
                        'action' => 'index'
                    )
                )
            ),
            'search' => array(
            		'type' => 'segment',
            		'options' => array(
            				'route' => '/search[/][:action][/:id]',
            				'constraints' => array(
            						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            						'id' => '[0-9a-zA-Z]*'
            				),
            				'defaults' => array(
            						'controller' => 'Web\Controller\Search',
            						'action' => 'index'
            				)
            		)
            ),
            'media' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/media[/][:action][/:dirid][/:id][/:urlseo]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'dirid' => '[^/]*',
                        'id' => '[0-9a-zA-Z]*',
                        'urlseo' => '[0-9a-zA-Z-.]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Media',
                        'action' => 'index'
                    )
                )
            ),
            'support' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/support[/][:action][/:urlseo]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'urlseo' => '[0-9a-zA-Z-.]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Support',
                        'action' => 'help'
                    )
                )
            ),
            'tour' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/tour[/][:action][/:urlseo]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'urlseo' => '[0-9a-zA-Z-.]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Web\Controller\Tour',
                        'action' => 'help'
                    )
                )
            )
        )
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => '<span class="glyphicon glyphicon-home"></span>&nbsp;Trang chủ',
//                 'id' => 'home',
//                 'class' => 'faq-menu-link',
                'route' => 'qapolo',
                'pages' => array(
                    array(
                        'label' => 'Câu hỏi',
                        'route' => 'home',
                        'action' => 'question'
                    )
                )
            ),

            array(
                'label' => '<span class="glyphicon glyphicon-info-sign"></span>&nbsp;Câu trả lời',
//                 'id' => 'answer',
//                 'class' => 'faq-menu-link',
                'route' => 'answer'
            ),
            array(
                'label' => '<span class="glyphicon glyphicon-question-sign"></span>&nbsp;Câu hỏi',
//                 'id' => 'question',
//                 'class' => 'faq-menu-link',
                'route' => 'question',
                'pages' => array(
                    array(
                        'label' => '1 Chọn chủ đề',
                        'route' => 'question',
                        'action' => 'select-subject',
                        'pages' => array(
                            array(
                                'label' => '2 Nội dung câu hỏi',
                                'route' => 'question',
                                'action' => 'content-question',
                                'pages' => array(
                                    array(
                                        'label' => '3 Hoàn tất',
                                        'route' => 'question',
                                        'action' => 'finish-question'
                                    )
                                )
                            )

                        )
                    )

                )
            )
            ,
            array(
                'label' => '<span class="glyphicon glyphicon-th-list"></span>&nbsp;Chủ đề',
//                 'id' => 'subject',
//                 'class' => 'faq-menu-link',
                'route' => 'subject'
            ),
            array(
                'label' => '<span class="glyphicon glyphicon-user"></span>&nbsp;Thành viên',
//                 'id' => 'member',
//                 'class' => 'faq-menu-link',
                'route' => 'member',
                'pages' => array(

                    array(
                        'label' => 'Câu hỏi',
                        'route' => 'member',
                        'action' => 'question'
                    ),
                    array(
                    		'label' => 'Câu trả lời',
                    		'route' => 'member',
                    		'action' => 'answer'
                    ),
                    array(
                    		'label' => 'Thông tin cá nhân',
                    		'route' => 'member',
                    		'action' => 'profile'
                    )
                 )
            ),
            array(
                'label' => '<span class="glyphicon glyphicon-bell"></span>&nbsp;Thông báo',
//                 'id' => 'message',
//                 'class' => 'faq-menu-link',
                'route' => 'message'
            ),
            array(
                'label' => '<span class="glyphicon glyphicon-wrench"></span>&nbsp;Cài đặt',
//                 'id' => 'setting',
//                 'class' => 'faq-menu-link',
                'route' => 'user'
            )
        ),
        'media_nav' => array(
            array(
                'label' => 'Thư mục',
                'id' => 'faq-media-dir',
                'class' => 'faq-menu-link',
                'route' => 'media',
                'action' => 'index'
            ),
            array(
                'label' => 'Link Ảnh',
                'id' => 'faq-media-image-link',
                'class' => 'faq-menu-link',
                'route' => 'media',
                'action' => 'image-link'
            ),
            array(
                'label' => 'Link video',
                'id' => 'faq-media-video-link',
                'class' => 'faq-menu-link',
                'route' => 'media',
                'action' => 'video-link'
            ),
            array(
                'label' => 'File Ảnh',
                'id' => 'faq-media-image',
                'class' => 'faq-menu-link',
                'route' => 'media',
                'action' => 'image-file'
            ),
            array(
                'label' => 'File Video',
                'id' => 'faq-media-video',
                'class' => 'faq-menu-link',
                'route' => 'media',
                'action' => 'video-file'
            ),
            array(
                'label' => 'File',
                'id' => 'faq-media-file',
                'class' => 'faq-menu-link',
                'route' => 'media',
                'action' => 'media-file',
                'dirid' => 5
            )
        ),
        'navigation_review' => array(
            array(
                'label' => 'Xử lý câu hỏi',
                'id' => 'review',
                'class' => 'faq-menu-link',
                'route' => 'review',
                'pages' => array(
                		array(
                				'label' => 'Duyệt vi phạm',
                				'route' => 'review',
                				'action' => 'spam-question'
                		),
                		array(
                				'label' => 'Bỏ vi phạm',
                				'route' => 'review',
                				'action' => 'unspam-question'
                		),
                		array(
                				'label' => 'Sửa câu hỏi',
                				'route' => 'review',
                				'action' => 'edit-question'
                		),
                		array(
                				'label' => 'Sửa câu trả lời',
                				'route' => 'review',
                				'action' => 'edit-answer'
                		),
                )
            )
        ),
        'navigation_guest' => array(
           array(
    						 'label' => '<span class="glyphicon glyphicon-home"></span>&nbsp;Trang chủ',
//     						'id' => 'home',
//     						'class' => 'faq-menu-link',
    						'route' => 'qapolo',
    						'pages' => array(
    								array(
    										'label' => 'Câu hỏi',
    										'route' => 'home',
    										'action' => 'question'
    								)
    						)
    				),

    				array(
    						'label' => '<span class="glyphicon glyphicon-th-list"></span>&nbsp;Chủ đề',
//     						'id' => 'subject',
//     						'class' => 'faq-menu-link',
    						'route' => 'subject'
    				),
    				array(
    						 'label' => '<span class="glyphicon glyphicon-user"></span>&nbsp;Thành viên',
//     						'id' => 'member',
//     						'class' => 'faq-menu-link',
    						'route' => 'member'
    				),

    		),

    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'media_nav' => 'FAQ\View\MediaNavigatorFactory',
            'navigation_guest' => 'FAQ\View\GuestNavigatorFactory',
            'memcache'=> 'Zend\Cache\Storage\Adapter\Memcached',
            'navigation_review' => 'FAQ\View\ReviewNavigatorFactory',
            'reCaptchaService' => function (\Zend\ServiceManager\ServiceManager $sm)
            {
                $config = $sm->get('Config');

                $recap = new \ZendService\ReCaptcha\ReCaptcha($config['recaptcha']['pubKey'], $config['recaptcha']['privKey']);
                return $recap;
            }
        )
    )
    ,
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo'
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Web\Controller\Home' => 'Web\Controller\HomeController',
            'Web\Controller\Question' => 'Web\Controller\QuestionController',
            'Web\Controller\Media' => 'Web\Controller\MediaController',
            'Web\Controller\User' => 'Web\Controller\UserController',
            'Web\Controller\Search' => 'Web\Controller\SearchController',
            'Web\Controller\Comment' => 'Web\Controller\CommentController',
            'Web\Controller\Answer' => 'Web\Controller\AnswerController',
            'Web\Controller\Subject' => 'Web\Controller\SubjectController',
            'Web\Controller\Member' => 'Web\Controller\MemberController',
            'Web\Controller\Support' => 'Web\Controller\SupportController',
            'Web\Controller\About' => 'Web\Controller\AboutController',
            'Web\Controller\Message' => 'Web\Controller\MessageController',
            'Web\Controller\Media' => 'Web\Controller\MediaController',
            'Web\Controller\Tag' => 'Web\Controller\TagController',
            'Web\Controller\Tour' => 'Web\Controller\TourController',
            'Web\Controller\Review' => 'Web\Controller\ReviewController'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'web/test/index' => __DIR__ . '/../view/web/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )
);