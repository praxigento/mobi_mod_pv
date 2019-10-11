<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Observer;

/**
 * This way is used for check/money payments only.
 * Update 'date_paid' in PV register and account PV when order is paid completely.
 */
class SalesOrderInvoicePay
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INVOICE = 'invoice';

    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var \Praxigento\Pv\Observer\Z\PvRegister */
    private $zRegister;
    /** @var \Praxigento\Pv\Repo\Dao\Sale */
    private $daoSale;

    public function __construct(

        \Praxigento\Pv\Repo\Dao\Sale $daoSale,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Pv\Observer\Z\PvRegister $zRegister
    ) {
        $this->daoSale = $daoSale;
        $this->hlpDate = $hlpDate;
        $this->zRegister = $zRegister;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData(self::DATA_INVOICE);
        $state = $invoice->getState();
        if ($state == \Magento\Sales\Model\Order\Invoice::STATE_PAID) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $invoice->getOrder();
            /* $orderId is null for orders being paid by credit card */
            $orderId = $order->getEntityId();
            if ($orderId) {
                /* this order is paid by check/money order, transfer PV to the customer account */
                $this->zRegister->accountPv($order);
            }
        }
    }
}