<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Registry PV for paid order on invoice payments (check/money order).
 */
class SalesOrderInvoicePay implements ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INVOICE = 'invoice';
    /** @var \Praxigento\Pv\Observer\Sub\Register */
    protected $_subRegister;

    public function __construct(
        \Praxigento\Pv\Observer\Sub\Register $subRegister
    ) {

        $this->_subRegister = $subRegister;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData(self::DATA_INVOICE);
        $state = $invoice->getState();
        if ($state == \Magento\Sales\Model\Order\Invoice::STATE_PAID) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $invoice->getOrder();
            $this->_subRegister->savePv($order);
        }
        return;
    }
}