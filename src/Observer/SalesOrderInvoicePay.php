<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Register PV on invoice is paid event.
 *
 * @package Praxigento\Downline\Observer
 */
class SalesOrderInvoicePay implements ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INVOICE = 'invoice';
    /** @var \Praxigento\Pv\Service\ISale */
    protected $_callSale;
    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Pv\Service\ISale $callSale
    ) {
        $this->_manStock = $manStock;
        $this->_callSale = $callSale;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData(self::DATA_INVOICE);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $invoice->getOrder();
        $orderId = $order->getId();
        $storeId = $order->getStoreId();
        /* get stock ID for the store view */
        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        /** @var \Magento\Sales\Api\Data\OrderItemInterface[] $items */
        $items = $order->getItems();
        $itemsData = [];
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($items as $item) {
            $prodId = $item->getProductId();
            $itemId = $item->getItemId();
            /* qty of the product can be changed in invoice */
            $qtyInvoiced = $item->getQtyInvoiced();
            /* create data item for service */
            $itemData = new \Praxigento\Pv\Service\Sale\Data\Item();
            $itemData->setItemId($itemId);
            $itemData->setProductId($prodId);
            $itemData->setQuantity($qtyInvoiced);
            $itemData->setStockId($stockId);
            $itemsData[] = $itemData;
        }
        /* compose request data and request itself */
        $req = new \Praxigento\Pv\Service\Sale\Request\Save();
        $req->setSaleOrderId($orderId);
        $req->setOrderItems($itemsData);
        $this->_callSale->save($req);
        return;
    }
}