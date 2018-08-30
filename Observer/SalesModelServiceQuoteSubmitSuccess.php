<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Observer;

/**
 * Registry PV on order submit and account PV if order being paid by credit card.
 */
class SalesModelServiceQuoteSubmitSuccess
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_ORDER = 'order';

    /** @var \Praxigento\Pv\Observer\Z\PvRegister */
    private $zRegister;

    public function __construct(
        \Praxigento\Pv\Observer\Z\PvRegister $zRegister
    ) {
        $this->zRegister = $zRegister;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData(self::DATA_ORDER);
        /* save PV for order and order items into the registry */
        $this->zRegister->savePv($order);
        /* account PV if order is paid (credit card payment) */
        $state = $order->getState();
        $status = $order->getStatus();
        if (
            ($state == \Magento\Sales\Model\Order::STATE_PROCESSING) &&
            ($status != \Magento\Sales\Model\Order::STATUS_FRAUD)
        ) {
            $this->zRegister->accountPv($order);
        }

    }
}