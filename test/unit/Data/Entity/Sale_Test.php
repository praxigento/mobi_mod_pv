<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Sale_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Sale */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new Sale();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $DATE_PAID = 'date paid';
        $DISCOUNT = 'discount';
        $SALE_ID = 'sale id';
        $SUBTOTAL = 'subtotal';
        $TOTAL = 'total';
        /** === Call and asserts  === */
        $this->obj->setDatePaid($DATE_PAID);
        $this->obj->setDiscount($DISCOUNT);
        $this->obj->setSaleId($SALE_ID);
        $this->obj->setSubtotal($SUBTOTAL);
        $this->obj->setTotal($TOTAL);
        $this->assertEquals($DATE_PAID, $this->obj->getDatePaid());
        $this->assertEquals($DISCOUNT, $this->obj->getDiscount());
        $this->assertEquals($SALE_ID, $this->obj->getSaleId());
        $this->assertEquals($SUBTOTAL, $this->obj->getSubtotal());
        $this->assertEquals($TOTAL, $this->obj->getTotal());
    }
}