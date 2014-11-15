<?php
return array(
    'doctrine' => array(

        'connection' => array(
            'odm_default' => array(
                'server'    => '192.168.1.111',
                'port'      => '27017',
                'user'      => 'root',
                'password'  => 'Admin123',
                'dbname'    => '123hoidap',
//                'options'   => array()
// list parameter option http://www.php.net/manual/en/mongoclient.construct.php
            ),
        ),

        'configuration' => array(
            'odm_default' => array(
//                'metadata_cache'     => 'array',
//
//                'driver'             => 'odm_default',
//
               'generate_proxies'   => true,
               'proxy_dir'          => 'data/DoctrineMongoODMModule/Proxy',
               'proxy_namespace'    => 'DoctrineMongoODMModule\Proxy',

               'generate_hydrators' => true,
               'hydrator_dir'       => 'data/DoctrineMongoODMModule/Hydrator',
               'hydrator_namespace' => 'DoctrineMongoODMModule\Hydrator',
//
                'default_db'         => 'developer',
                'session_collection' => 'sessions',
//
//                'filters'            => array(),  // array('filterName' => 'BSON\Filter\Class'),
//
                'logger'             => 'DoctrineMongoODMModule\Logging\DebugStack'
            )
        ),

        'driver' => array(
            'odm_default' => array(
//                'drivers' => array()
            )
        ),

        'documentmanager' => array(
            'odm_default' => array(
//                'connection'    => 'odm_default',
//                'configuration' => 'odm_default',
//                'eventmanager' => 'odm_default'
            )
        ),

        'eventmanager' => array(
            'odm_default' => array(
                'subscribers' => array()
            )
        ),
    ),
);