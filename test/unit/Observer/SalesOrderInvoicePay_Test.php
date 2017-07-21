<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

use Praxigento\Pv\Data\Entity\Sale as ESale;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class SalesOrderInvoicePay_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    protected $mLogger;
    /** @var  \Mockery\MockInterface */
    protected $mRepoSale;
    /** @var  \Mockery\MockInterface */
    protected $mSubRegister;
    /** @var  \Mockery\MockInterface */
    protected $mToolDate;
    /** @var  SalesOrderInvoicePay */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mRepoSale = $this->_mock(\Praxigento\Pv\Repo\Entity\Sale::class);
        $this->mToolDate = $this->_mock(\Praxigento\Core\Tool\IDate::class);
        $this->mSubRegister = $this->_mock(\Praxigento\Pv\Observer\Sub\Register::class);
        /** create object to test */
        $this->obj = new SalesOrderInvoicePay(
            $this->mLogger,
            $this->mRepoSale,
            $this->mToolDate,
            $this->mSubRegister
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(SalesOrderInvoicePay::class, $this->obj);
    }

    public function test_execute()
    {
        /** === Test Data === */
        $observer = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** === Setup Mocks === */
        // $invoice = $observer->getData(self::DATA_INVOICE);
        $mInvoce = $this->_mock(\Magento\Sales\Model\Order\Invoice::class);
        $observer->shouldReceive('getData')->once()
            ->with(SalesOrderInvoicePay::DATA_INVOICE)
            ->andReturn($mInvoce);
        // $state = $invoice->getState();
        $mState = \Magento\Sales\Model\Order\Invoice::STATE_PAID;
        $mInvoce->shouldReceive('getState')->once()
            ->andReturn($mState);
        // $order = $invoice->getOrder();
        $mOrder = $this->_mock(\Magento\Sales\Model\Order::class);
        $mInvoce->shouldReceive('getOrder')->once()
            ->andReturn($mOrder);
        // $orderId = $order->getEntityId();
        $mOrderId = 32;
        $mOrder->shouldReceive('getEntityId')->once()
            ->andReturn($mOrderId);
        // $datePaid = $this->_toolDate->getUtcNowForDb();
        $mDatePaid = 'date paid';
        $this->mToolDate
            ->shouldReceive('getUtcNowForDb')->once()
            ->andReturn($mDatePaid);
        // $this->_repoSale->updateById($orderId, $data);
        $this->mRepoSale
            ->shouldReceive('updateById')->once()
            ->with($mOrderId, [ESale::ATTR_DATE_PAID => $mDatePaid]);
        // $this->_subRegister->accountPv($order);
        $this->mSubRegister
            ->shouldReceive('accountPv')->once();
        /** === Call and asserts  === */
        $this->obj->execute($observer);
    }

    public function test_execute_exception()
    {
        /** === Test Data === */
        $observer = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** === Setup Mocks === */
        // $invoice = $observer->getData(self::DATA_INVOICE);
        $mInvoce = $this->_mock(\Magento\Sales\Model\Order\Invoice::class);
        $observer->shouldReceive('getData')->once()
            ->with(SalesOrderInvoicePay::DATA_INVOICE)
            ->andReturn($mInvoce);
        // $state = $invoice->getState();
        $mState = \Magento\Sales\Model\Order\Invoice::STATE_PAID;
        $mInvoce->shouldReceive('getState')->once()
            ->andReturn($mState);
        // $order = $invoice->getOrder();
        $mInvoce->shouldReceive('getOrder')->once()
            ->andThrow(new \Exception());
        /** === Call and asserts  === */
        $this->obj->execute($observer);
    }

}