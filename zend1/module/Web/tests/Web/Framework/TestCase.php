<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Web for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace WebTest\Framework;

use PHPUnit_Framework_TestCase;
use FAQ\Mapper\TestMapper;

class TestCase extends \PHPUnit_Framework_TestCase
{

    public static $locator;

    public static function setLocator($locator)
    {
        self::$locator = $locator;
    }

    public function getLocator()
    {
        return self::$locator;
    }
    public function testFAQ(){
        $testma=new TestMapper();
        $testma->insertUserCategory();
    }
}
