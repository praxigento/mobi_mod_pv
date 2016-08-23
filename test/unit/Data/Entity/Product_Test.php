<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Product_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  Product */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Product();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $REF = 'product ref';
        $PV = 'pv';
        /** === Call and asserts  === */
        $this->obj->setProductRef($REF);
        $this->obj->setPv($PV);
        $this->assertEquals($REF, $this->obj->getProductRef());
        $this->assertEquals($PV, $this->obj->getPv());
    }
}