<?php
/**
 * Empty class to get stub for tests
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv;
include_once(__DIR__ . '/phpunit_bootstrap.php');

class Config_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_constructor() {
        $cfg = new Config();
        $this->assertTrue($cfg instanceof \Praxigento\Pv\Config);
    }

}