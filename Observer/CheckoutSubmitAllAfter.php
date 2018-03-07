<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer;

/**
 * Registry PV on order submit.
 */
class CheckoutSubmitAllAfter
    implements \Magento\Framework\Event\ObserverInterface
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
        /* save PV for order and order items into the registry */
        $this->_subRegister->savePv($order);
        /* account PV if order is paid (credit card payment) */
        $state = $order->getState();
        $status = $order->getStatus();
        if (
            ($state == \Magento\Sales\Model\Order::STATE_PROCESSING) &&
            ($status != \Magento\Sales\Model\Order::STATUS_FRAUD)
        ) {
            $this->_subRegister->accountPv($order);
        }

    }
}