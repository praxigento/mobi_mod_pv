<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer\Sub\Register;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Collector_UnitTest
    extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    protected $mManObj;
    /** @var  \Mockery\MockInterface */
    protected $mManStock;
    /** @var  Collector */
    private $obj;
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mManStock = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $this->mManObj,
            $this->mManStock
        ];
        /** create object to test */
        $this->obj = new Collector(
            $this->mManObj,
            $this->mManStock
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Collector::class, $this->obj);
    }

    public function test_getServiceItemForMageItem()
    {
        /** === Test Data === */
        $PROD_ID = 8;
        $ITEM_ID = 16;
        $STOCK_ID = 4;
        $QTY = 32;
        $ITEM = $this->_mock(\Magento\Sales\Api\Data\OrderItemInterface::class);
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(\Praxigento\Pv\Service\Sale\Data\Item::class);
        $mResult = $this->_mock(\Praxigento\Pv\Service\Sale\Data\Item::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mResult);
        // $prodId = $item->getProductId();
        $ITEM->shouldReceive('getProductId')->once()->andReturn($PROD_ID);
        // $itemId = $item->getItemId();
        $ITEM->shouldReceive('getItemId')->once()->andReturn($ITEM_ID);
        // $qty = $item->getQtyOrdered();
        $ITEM->shouldReceive('getQtyOrdered')->once()->andReturn($QTY);
        // setters
        $mResult->shouldReceive('setItemId')->once()->with($ITEM_ID);
        $mResult->shouldReceive('setProductId')->once()->with($PROD_ID);
        $mResult->shouldReceive('setQuantity')->once()->with($QTY);
        $mResult->shouldReceive('setStockId')->once()->with($STOCK_ID);
        /** === Call and asserts  === */
        $res = $this->obj->getServiceItemForMageItem($ITEM, $STOCK_ID);
        $this->assertEquals($mResult, $res);
    }

    public function test_getServiceItemsForMageSaleOrder()
    {
        /** === Test Data === */
        $STORE_ID = 4;
        $STOCK_ID = 8;
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(Collector::class . '[getServiceItemForMageItem]', $this->objArgs);
        /** === Setup Mocks === */
        // $storeId = $order->getStoreId();
        $ORDER->shouldReceive('getStoreId')->once()->andReturn($STORE_ID);
        // $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $this->mManStock
            ->shouldReceive('getStockIdByStoreId')->once()
            ->andReturn($STOCK_ID);
        // $items = $order->getItems();
        $mItem = $this->_mock(\Magento\Sales\Api\Data\OrderItemInterface::class);
        $ORDER->shouldReceive('getItems')->once()
            ->andReturn([$mItem]);
        // $itemData = $this->getServiceItemForMageItem($item, $stockId);
        $mItemData = $this->_mock(\Praxigento\Pv\Service\Sale\Data\Item::class);
        $this->obj->shouldReceive('getServiceItemForMageItem')->once()
            ->andReturn($mItemData);
        /** === Call and asserts  === */
        $res = $this->obj->getServiceItemsForMageSaleOrder($ORDER);
        $this->assertTrue(is_array($res));
        $this->assertTrue(count($res) > 0);
    }

}