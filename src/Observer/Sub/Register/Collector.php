<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Observer\Sub\Register;


class Collector
{
    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $manStock
    ) {
        $this->_manStock = $manStock;
    }

    /**
     * Convert Magento SaleOrderItem to service item.
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @param int $stockId
     * @return \Praxigento\Pv\Service\Sale\Data\Item
     */
    public function getServiceItemForMageItem(\Magento\Sales\Api\Data\OrderItemInterface $item, $stockId = null)
    {
        $result = new \Praxigento\Pv\Service\Sale\Data\Item();
        $prodId = $item->getProductId();
        $itemId = $item->getItemId();
        /* qty of the product can be changed in invoice */
        $qtyInvoiced = $item->getQtyInvoiced();
        /* create data item for service */
        $result->setItemId($itemId);
        $result->setProductId($prodId);
        $result->setQuantity($qtyInvoiced);
        $result->setStockId($stockId);
        return $result;
    }

    public function getServiceItemsForMageSaleOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $result = [];
        $storeId = $order->getStoreId();
        /* get stock ID for the store view */
        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        /** @var \Magento\Sales\Api\Data\OrderItemInterface[] $items */
        $items = $order->getItems();
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($items as $item) {
            $itemData = $this->getServiceItemForMageItem($item, $stockId);
            $result[] = $itemData;
        }
        return $result;
    }

}