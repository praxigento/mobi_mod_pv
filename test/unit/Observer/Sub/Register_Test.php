<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer\Sub;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
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

    public function test_accountPv()
    {
        /** === Test Data === */
        $order = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Setup Mocks === */
        // $state = $order->getState();
        $mState = \Magento\Sales\Model\Order::STATE_PROCESSING;
        $order->shouldReceive('getState')->once()
            ->andReturn($mState);
        // $orderId = $order->getEntityId();
        $mOrderId = 32;
        $order->shouldReceive('getEntityId')->once()
            ->andReturn($mOrderId);
        // $itemsData = $this->_subCollector->getServiceItemsForMageSaleOrder($order);
        $mItemsData = [];
        $this->mSubCollector
            ->shouldReceive('getServiceItemsForMageSaleOrder')->once()
            ->andReturn($mItemsData);
        // $req = $this->_manObj->create(\Praxigento\Pv\Service\Sale\Request\AccountPv::class);
        $mReq = new \Praxigento\Pv\Service\Sale\Request\AccountPv();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(\Praxigento\Pv\Service\Sale\Request\AccountPv::class)
            ->andReturn($mReq);
        // $this->_callSale->accountPv($req);
        $this->mCallSale
            ->shouldReceive('accountPv')->once()
            ->with($mReq);
        /** === Call and asserts  === */
        $this->obj->accountPv($order);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Register::class, $this->obj);
    }

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function test_savePv()
    {
        /** === Test Data === */
        $id = 4;
        $state = \Magento\Sales\Model\Order::STATE_PROCESSING;
        $dateCreated = 'created at';
        $order = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Setup Mocks === */
        // $orderId = $order->getId();
        $order->shouldReceive('getId')->once()->andReturn($id);
        // $state = $order->getState();
        $order->shouldReceive('getState')->once()->andReturn($state);
        // $dateCreated = $order->getCreatedAt();
        $order->shouldReceive('getCreatedAt')->once()->andReturn($dateCreated);
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
        $mReq->shouldReceive('setSaleOrderId')->once()->with($id);
        // $req->setOrderItems($itemsData);
        $mReq->shouldReceive('setOrderItems')->once()->with($mItemsData);
        // $req->setSaleOrderDatePaid($dateCreated);
        $mReq->shouldReceive('setSaleOrderDatePaid')->once()->with($dateCreated);
        // $this->_callSale->save($req);
        $this->mCallSale->shouldReceive('save')->once()->with($mReq);
        /** === Call and asserts  === */
        $this->obj->savePv($order);
    }

}