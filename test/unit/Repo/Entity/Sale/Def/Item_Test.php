<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Entity\Sale\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Item_UnitTest extends \Praxigento\Core\Test\BaseRepoEntityCase
{
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  Item */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        /** create object to test */
        $this->obj = new Item(
            $this->mManObj,
            $this->mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Item::class, $this->obj);
    }

    public function test_getItemsByOrderId()
    {
        /** === Test Data === */
        $ORDER_ID = 32;
        $ITEM_ID = 64;
        /** === Setup Mocks === */
        // $query = $conn->select();
        $mQuery = $this->_mockDbSelect(['from', 'joinLeft', 'where']);
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mQuery);
        // $rows = $conn->fetchAll($query);
        $mRow = [];
        $mRows = [$mRow];
        $this->mConn
            ->shouldReceive('fetchAll')->once()
            ->andReturn($mRows);
        // $item = $this->_manObj->create(Entity::class, $row);
        $mItem = $this->_mock(\Praxigento\Pv\Data\Entity\Sale\Item::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(\Praxigento\Pv\Data\Entity\Sale\Item::class, $mRow)
            ->andReturn($mItem);
        // $result[$item->getSaleItemId()] = $item;
        $mItem->shouldReceive('getSaleItemId')->once()
            ->andReturn($ITEM_ID);
        /** === Call and asserts  === */
        $res = $this->obj->getItemsByOrderId($ORDER_ID);
        $this->assertTrue(is_array($res));
        $this->assertTrue(isset($res[$ITEM_ID]));
    }
}