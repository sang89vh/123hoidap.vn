<?php

use FAQ\FAQCommon\Util;

/**
 * This makes our life easier when dealing with paths.
 * Everything is relative
 * to the application root now.
 */

// mode : 0, E_ERROR | E_WARNING | E_PARSE | E_NOTICE, E_ALL
//  error_reporting ( E_ALL );
error_reporting ( E_ERROR );
// error_reporting(0);
chdir ( dirname ( __DIR__ ) );
// Setup autoloading
require 'init_autoloader.php';
// Run the application!
try {
	Zend\Mvc\Application::init ( require 'config/application.config.php' )->run ();
	// session_start();
} catch ( Exception $e ) {

	if ($e->getMessage () == 'No RouteMatch instance provided') {
		include "notice-no-route.phtml";
	} else {
		include "notice-error.phtml";
	}
	var_dump($e);
	Util::writeLog ( $e->getMessage () );
}

?>


