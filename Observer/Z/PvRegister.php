<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer\Z;

use Praxigento\Pv\Repo\Data\Sale as ESale;

/**
 * - account PV (transfer PV to customer account)
 * - register PV for the sale order
 * - update paid date in sale order registry
 */
class PvRegister
{
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Pv\Repo\Dao\Sale */
    private $daoSale;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Praxigento\Pv\Api\Service\Sale\Account\Pv */
    private $srvPvAccount;
    /** @var \Praxigento\Pv\Service\Sale\Save */
    private $srvPvSave;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Pv\Repo\Dao\Sale $daoSale,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Pv\Api\Service\Sale\Account\Pv $srvPvAccount,
        \Praxigento\Pv\Service\Sale\Save $srvPvSave
    ) {
        $this->logger = $logger;
        $this->daoSale = $daoSale;
        $this->hlpStock = $hlpStock;
        $this->hlpDate = $hlpDate;
        $this->srvPvAccount = $srvPvAccount;
        $this->srvPvSave = $srvPvSave;
    }

    /**
     * Collect order data and call service method to transfer PV to customer account.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @throws \Exception
     */
    public function accountPv(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        /* sale state validation should be performed before */
        $orderId = $order->getEntityId();
        /* update record state in the registry */
        $this->updateDatePaid($orderId);
        /* ... then transfer PV to the customer account */
        $req = new \Praxigento\Pv\Api\Service\Sale\Account\Pv\Request();
        $req->setSaleOrderId($orderId);
        $this->srvPvAccount->exec($req);
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
        $this->srvPvSave->exec($req);
    }

    /**
     * Update paid date in sale order registry of PV module.
     *
     * @param int $orderId
     */
    private function updateDatePaid($orderId)
    {
        $datePaid = $this->hlpDate->getUtcNowForDb();
        $data = [ESale::A_DATE_PAID => $datePaid];
        $this->daoSale->updateById($orderId, $data);
        $this->logger->info("Update paid date in PV registry when sale order (#$orderId) is paid.");
    }
}