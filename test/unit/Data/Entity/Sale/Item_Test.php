<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Data\Entity\Sale;

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
        /* === Test Data === */
        $DISCOUNT = 'discount';
        $SALE_ITEM_ID = 'sale item id';
        $SUBTOTAL = 'subtotal';
        $TOTAL = 'total';
        /* === Call and asserts  === */
        $this->obj->setDiscount($DISCOUNT);
        $this->obj->setSaleItemId($SALE_ITEM_ID);
        $this->obj->setSubtotal($SUBTOTAL);
        $this->obj->setTotal($TOTAL);
        $this->assertEquals($DISCOUNT, $this->obj->getDiscount());
        $this->assertEquals($SALE_ITEM_ID, $this->obj->getSaleItemId());
        $this->assertEquals($SUBTOTAL, $this->obj->getSubtotal());
        $this->assertEquals($TOTAL, $this->obj->getTotal());
    }
}