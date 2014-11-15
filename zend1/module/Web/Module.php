<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Web for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Web;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use FAQ\DB\Db;
use Zend\Cache\StorageFactory;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Session\SaveHandler\MongoDBOptions;
use Zend\Session\SaveHandler\MongoDB;
use FAQ\FAQCommon\Util;
use Zend\Cache\Service\StorageCacheFactory;
use FAQ\FAQCommon\memcacheSessionHandler;
use FAQ\FAQCommon\Appcfg;

class Module implements AutoloaderProviderInterface {
	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\ClassMapAutoloader' => array (
						__DIR__ . '/autoload_classmap.php'
				),
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								// if we're in a namespace deeper than one level we need to fix the \ in the path
								__NAMESPACE__ => __DIR__ . '/src/' . str_replace ( '\\', '/', __NAMESPACE__ )
						)
				)
		);
	}
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	/**
	 *
	 * @param \Zend\Mvc\MvcEvent $e
	 */
	public function onBootstrap($e) {
		// You may not need to do this if you're doing it elsewhere in your
		// application
		$eventManager = $e->getApplication ()->getEventManager ();
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
		$sm = $e->getApplication ()->getServiceManager ();
		Db::$serviceLocator = $sm;
		// TODO. setting up session. select a storage
		// faq session manager
		// $this->bootstrapSession($e);
		// faq memcache session
		$this->bootstrapSessionMemcache ( $e );
	}
	public function bootstrapSession($e) {
		/* @var $conn \Doctrine\MongoDB\Connection */
		$dm = $e->getApplication ()->getServiceManager ()->get ( "doctrine.documentmanager.odm_default" );
		$conn = $dm->getConnection ();
		$mg = new \Mongo ( $conn->getServer () );
		/* @var $config \Doctrine\ODM\MongoDB\Configuration */
		$config = $conn->getConfiguration ();
		$options = new MongoDBOptions ( array (
				'database' => $config->getDefaultDB (),
				'collection' => $config->getSessionCollection ()
		) );

		$saveHandler = new MongoDB ( $mg, $options );
		$sm = new SessionManager ();
		$sm->setSaveHandler ( $saveHandler );
		$sm->start ();
		Util::$sm = $sm;
	}

	/**
	 *
	 * @author ldlong
	 * @todo setting session with memcache
	 * @param unknown $e
	 */
	public function bootstrapSessionMemcache($e) {
		$saveHandler = new memcacheSessionHandler ();
		$sm = new SessionManager (null);
		$sm->setSaveHandler ( $saveHandler );
		$sm->start ();
		Util::$sm = $sm;
		// create session which allow access from subdomain
		$sessionid = $sm->getId ();
		setcookie ( 'QAPOLOSESSIONID', $sessionid, null, '/', '.' . Appcfg::$qapolo_domain, false, true );
	}
}