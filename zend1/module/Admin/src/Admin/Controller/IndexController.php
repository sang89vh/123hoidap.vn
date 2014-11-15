<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Admin for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;

class IndexController extends FAQAbstractActionController
{
    public function indexAction()
    {
        $this->setLayoutAdmin();
        return array();
    }

    public function fooAction()
    {
        $this->setLayoutHome();
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /admin/index/foo
        return array();
    }
}
