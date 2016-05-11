<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Data\Entity\Stock;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Item_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Item */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Item();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $STOCK_ITEM_REF = 'stock item ref';
        $PV = 'pv';
        /** === Call and asserts  === */
        $this->obj->setStockItemRef($STOCK_ITEM_REF);
        $this->obj->setPv($PV);
        $this->assertEquals($STOCK_ITEM_REF, $this->obj->getStockItemRef());
        $this->assertEquals($PV, $this->obj->getPv());
    }
}