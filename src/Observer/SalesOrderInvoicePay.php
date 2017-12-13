<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Observer;

use Praxigento\Pv\Repo\Entity\Data\Sale as ESale;

/**
 * Update 'date_paid' in PV register and account PV when order is paid completely (bank transfer).
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class SalesOrderInvoicePay
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INVOICE = 'invoice';
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var \Praxigento\Pv\Repo\Entity\Sale */
    protected $_repoSale;
    /** @var \Praxigento\Pv\Observer\Sub\Register */
    protected $_subRegister;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Pv\Repo\Entity\Sale $repoSale,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Pv\Observer\Sub\Register $subRegister
    ) {
        $this->_logger = $logger;
        $this->_repoSale = $repoSale;
        $this->_toolDate = $toolDate;
        $this->_subRegister = $subRegister;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData(self::DATA_INVOICE);
        $state = $invoice->getState();
        if ($state == \Magento\Sales\Model\Order\Invoice::STATE_PAID) {
            /* update date_paid in the PV registry */
            /** @var \Magento\Sales\Model\Order $order */
            $order = $invoice->getOrder();
            $orderId = $order->getEntityId();
            if ($orderId) {
                $datePaid = $this->_toolDate->getUtcNowForDb();
                $this->_logger->debug("Update paid date in PV registry on sale order (#$orderId) is paid.");
                $data = [ESale::ATTR_DATE_PAID => $datePaid];
                $this->_repoSale->updateById($orderId, $data);
                /* transfer PV to customer account */
                $this->_subRegister->accountPv($order);
            }
        }
    }
}