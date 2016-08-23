<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer\Sub;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Register_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    protected $mCallSale;
    /** @var  \Mockery\MockInterface */
    protected $mManObj;
    /** @var  \Mockery\MockInterface */
    protected $mSubCollector;
    /** @var  Register */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mCallSale = $this->_mock(\Praxigento\Pv\Service\ISale::class);
        $this->mSubCollector = $this->_mock(\Praxigento\Pv\Observer\Sub\Register\Collector::class);
        /** create object to test */
        $this->obj = new Register(
            $this->mManObj,
            $this->mCallSale,
            $this->mSubCollector
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Register::class, $this->obj);
    }

    public function test_savePv()
    {
        /** === Test Data === */
        $ID = 4;
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Setup Mocks === */
        // $orderId = $order->getId();
        $ORDER->shouldReceive('getId')->once()->andReturn($ID);
        // $itemsData = $this->_subCollector->getServiceItemsForMageSaleOrder($order);
        $mItemsData = [];
        $this->mSubCollector
            ->shouldReceive('getServiceItemsForMageSaleOrder')->once()
            ->andReturn($mItemsData);
        // $req = $this->_manObj->create(\Praxigento\Pv\Service\Sale\Request\Save::class);
        $mReq = $this->_mock(\Praxigento\Pv\Service\Sale\Request\Save::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mReq);
        // $req->setSaleOrderId($orderId);
        $mReq->shouldReceive('setSaleOrderId')->once()->with($ID);
        // $req->setOrderItems($itemsData);
        $mReq->shouldReceive('setOrderItems')->once()->with($mItemsData);
        // $this->_callSale->save($req);
        $this->mCallSale->shouldReceive('save')->once()->with($mReq);
        /** === Call and asserts  === */
        $this->obj->savePv($ORDER);
    }

}