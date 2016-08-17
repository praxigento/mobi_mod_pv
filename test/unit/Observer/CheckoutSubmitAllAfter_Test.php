<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class CheckoutSubmitAllAfter_UnitTest
    extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    protected $mSubRegister;
    /** @var  CheckoutSubmitAllAfter */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubRegister = $this->_mock(\Praxigento\Pv\Observer\Sub\Register::class);
        /** create object to test */
        $this->obj = new CheckoutSubmitAllAfter(
            $this->mSubRegister
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(CheckoutSubmitAllAfter::class, $this->obj);
    }

    public function test_execute()
    {
        /** === Test Data === */
        $OBSERVER = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** === Setup Mocks === */
        // $order = $observer->getData(self::DATA_ORDER);
        $mOrder = $this->_mock(\Magento\Sales\Model\Order::class);
        $OBSERVER->shouldReceive('getData')->once()
            ->with(CheckoutSubmitAllAfter::DATA_ORDER)
            ->andReturn($mOrder);
        // $this->_subRegister->savePv($order);
        $this->mSubRegister
            ->shouldReceive('savePv')->once();
        /** === Call and asserts  === */
        $this->obj->execute($OBSERVER);
    }

}