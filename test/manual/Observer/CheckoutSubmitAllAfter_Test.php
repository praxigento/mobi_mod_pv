<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer;


use Praxigento\Pv\Data\Entity\Sale;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class CheckoutSubmitAllAfter_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  \Magento\Sales\Api\OrderRepositoryInterface */
    private $_mageRepoSaleOrder;
    /** @var  CheckoutSubmitAllAfter */
    private $obj;

    protected function setUp()
    {
        $this->_mageRepoSaleOrder = $this->_manObj->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $this->obj = $this->_manObj->create(CheckoutSubmitAllAfter::class);
    }

    public function test_execute()
    {
        /** @var \Magento\Framework\Event\Observer $event */
        $event = $this->_manObj->create(\Magento\Framework\Event\Observer::class);
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $this->_mageRepoSaleOrder->get(1);
        $event->setData(CheckoutSubmitAllAfter::DATA_ORDER, $order);
        $this->obj->execute($event);

    }
}