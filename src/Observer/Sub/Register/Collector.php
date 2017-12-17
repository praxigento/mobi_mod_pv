<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Observer\Sub\Register;


class Collector
{
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    protected $_manStock;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Warehouse\Api\Helper\Stock $manStock
    ) {
        $this->_manObj = $manObj;
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
        /* qty of the product can be changed in invoice but we use ordered only */
        $qty = $item->getQtyOrdered();
        /* create data item for service */
        $result->setItemId($itemId);
        $result->setProductId($prodId);
        $result->setQuantity($qty);
        $result->setStockId($stockId);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Praxigento\Pv\Service\Sale\Data\Item[]
     */
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