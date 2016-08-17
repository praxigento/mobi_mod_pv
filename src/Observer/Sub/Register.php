<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Observer\Sub;

class Register
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Praxigento\Pv\Service\ISale */
    protected $_callSale;
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
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function savePv(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $orderId = $order->getId();
        $itemsData = $this->_subCollector->getServiceItemsForMageSaleOrder($order);
        /* compose request data and request itself */
        $req = $this->_manObj->create(\Praxigento\Pv\Service\Sale\Request\Save::class);
        $req->setSaleOrderId($orderId);
        $req->setOrderItems($itemsData);
        $this->_callSale->save($req);
    }
}