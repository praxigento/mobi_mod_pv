<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer\A;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Register
{
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Praxigento\Pv\Service\Sale\Account\Pv */
    private $salePv;
    /** @var \Praxigento\Pv\Service\Sale\Save */
    private $saleSave;

    public function __construct(
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Praxigento\Pv\Service\Sale\Account\Pv $servSale
    ) {
        $this->hlpStock = $hlpStock;
        $this->salePv = $servSale;
    }

    /**
     * Collect order data and call service method to transfer PV to customer account.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @throws \Exception
     */
    public function accountPv(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $state = $order->getState();
        if ($state == \Magento\Sales\Model\Order::STATE_PROCESSING) {
            /* transfer PV if order is paid */
            $orderId = $order->getEntityId();
            /* TODO: remove this attr from request, it is never used */
//            $itemsData = $this->getServiceItemsForMageSaleOrder($order);
            /** @var \Praxigento\Pv\Service\Sale\Request\AccountPv $req */
            $req = new \Praxigento\Pv\Service\Sale\Account\Pv\Request();
            $req->setSaleOrderId($orderId);
//            $req->setOrderItems($itemsData);
            $this->salePv->exec($req);
        }
    }

    /**
     * Convert Magento SaleOrderItem to service item.
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @param int $stockId
     * @return \Praxigento\Pv\Service\Sale\Data\Item
     * @throws \Exception
     */
    private function getServiceItemForMageItem(\Magento\Sales\Api\Data\OrderItemInterface $item, $stockId = null)
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
     * @throws \Exception
     */
    private function getServiceItemsForMageSaleOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $result = [];
        $storeId = $order->getStoreId();
        /* get stock ID for the store view */
        $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
        /** @var \Magento\Sales\Api\Data\OrderItemInterface[] $items */
        $items = $order->getItems();
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($items as $item) {
            $itemData = $this->getServiceItemForMageItem($item, $stockId);
            $result[] = $itemData;
        }
        return $result;
    }

    /**
     * Collect orders data and call service method to register order PV.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @throws \Exception
     */
    public function savePv(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $orderId = $order->getId();
        $state = $order->getState();
        $dateCreated = $order->getCreatedAt();
        $itemsData = $this->getServiceItemsForMageSaleOrder($order);
        /* compose request data and request itself */
        /** @var \Praxigento\Pv\Service\Sale\Save\Request $req */
        $req = new \Praxigento\Pv\Service\Sale\Save\Request();
        $req->setSaleOrderId($orderId);
        $req->setOrderItems($itemsData);
        if ($state == \Magento\Sales\Model\Order::STATE_PROCESSING) {
            $req->setSaleOrderDatePaid($dateCreated);
        }
        $this->saleSave->exec($req);
    }
}