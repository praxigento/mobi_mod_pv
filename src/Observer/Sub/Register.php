<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer\Sub;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Register
{
    /** @var \Praxigento\Pv\Service\ISale */
    protected $_callSale;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Praxigento\Pv\Observer\Sub\Register\Collector */
    protected $_subCollector;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Pv\Service\ISale $callSale,
        \Praxigento\Pv\Observer\Sub\Register\Collector $subCollector
    ) {
        $this->_manObj = $manObj;
        $this->_callSale = $callSale;
        $this->_subCollector = $subCollector;
    }

    /**
     * Collect order data and call service method to transfer PV to customer account.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function accountPv(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $state = $order->getState();
        if ($state == \Magento\Sales\Model\Order::STATE_PROCESSING) {
            /* transfer PV if order is paid */
            $orderId = $order->getEntityId();
            $itemsData = $this->_subCollector->getServiceItemsForMageSaleOrder($order);
            /** @var \Praxigento\Pv\Service\Sale\Request\AccountPv $req */
            $req = $this->_manObj->create(\Praxigento\Pv\Service\Sale\Request\AccountPv::class);
            $req->setSaleOrderId($orderId);
            $req->setOrderItems($itemsData);
            $this->_callSale->accountPv($req);
        }
    }

    /**
     * Collect orders data and call service method to register order PV.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function savePv(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $orderId = $order->getId();
        $state = $order->getState();
        $dateCreated = $order->getCreatedAt();
        $itemsData = $this->_subCollector->getServiceItemsForMageSaleOrder($order);
        /* compose request data and request itself */
        /** @var \Praxigento\Pv\Service\Sale\Request\Save $req */
        $req = $this->_manObj->create(\Praxigento\Pv\Service\Sale\Request\Save::class);
        $req->setSaleOrderId($orderId);
        $req->setOrderItems($itemsData);
        if ($state == \Magento\Sales\Model\Order::STATE_PROCESSING) {
            $req->setSaleOrderDatePaid($dateCreated);
        }
        $this->_callSale->save($req);
    }
}