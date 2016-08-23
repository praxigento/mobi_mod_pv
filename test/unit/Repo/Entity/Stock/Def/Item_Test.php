<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Entity\Stock\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Item_UnitTest extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  Item */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Item(
            $this->mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Item::class, $this->obj);
    }

    public function test_getPvByProductAndStock()
    {
        /** === Test Data === */
        $PRODUCT_ID = 32;
        $STOCK_ID = 64;
        $RESULT = 'result';
        /** === Setup Mocks === */
        // $query = $conn->select();
        $mQuery = $this->_mockDbSelect(['from', 'joinLeft', 'where']);
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mQuery);
        // $result = $conn->fetchOne($query);
        $this->mConn
            ->shouldReceive('fetchOne')->once()
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getPvByProductAndStock($PRODUCT_ID, $STOCK_ID);
        $this->assertEquals($RESULT, $res);
    }
}