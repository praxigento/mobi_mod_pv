<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Observer;

use Praxigento\Pv\Data\Entity\Sale as EntitySale;

/**
 * Update 'date_paid' in PV register.
 */
class SalesOrderInvoicePay
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INVOICE = 'invoice';
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var \Praxigento\Pv\Repo\Entity\ISale */
    protected $_repoSale;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Pv\Repo\Entity\ISale $repoSale
    ) {
        $this->_logger = $logger;
        $this->_repoSale = $repoSale;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData(self::DATA_INVOICE);
        $state = $invoice->getState();
        if ($state == \Magento\Sales\Model\Order\Invoice::STATE_PAID) {
            try {
                /* update date_paid in the PV registry */
                /** @var \Magento\Sales\Model\Order $order */
                $order = $invoice->getOrder();
                $orderId = $order->getEntityId();
                $datePaid = $invoice->getCreatedAt();
                $this->_logger->debug("Update paid date in PV registry on sale order (#$orderId) is paid.");
                $data = [EntitySale::ATTR_DATE_PAID => $datePaid];
                $this->_repoSale->updateById($orderId, $data);
                /* transfer PV to customer account */

            } catch (\Exception $e) {
                /* catch all exceptions and steal them */
                $msg = 'Some error is occurred on update of the paid date in PV register. Error: ' . $e->getMessage();
                $this->_logger->error($msg);
            }
        }
    }
}