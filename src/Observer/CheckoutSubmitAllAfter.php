<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Registry PV for paid order for credit cards payments.
 */
class CheckoutSubmitAllAfter implements ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_ORDER = 'order';
    /** @var \Praxigento\Pv\Observer\Sub\Register */
    protected $_subRegister;

    public function __construct(
        \Praxigento\Pv\Observer\Sub\Register $subRegister
    ) {

        $this->_subRegister = $subRegister;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData(self::DATA_ORDER);
        $this->_subRegister->savePv($order);
        return;
    }
}