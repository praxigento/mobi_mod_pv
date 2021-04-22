<?php
/**
 * File creator: dmitriimakhov@gmail.com
 */

namespace Praxigento\Pv\Service\Sale;

use Praxigento\Pv\Service\Sale\Save\Request as ARequest;
use Praxigento\Pv\Service\Sale\Save\Response as AResponse;

class Save
{
    /** @var  \Praxigento\Pv\Repo\Dao\Sale */
    private $daoSale;
    /** @var  \Praxigento\Pv\Repo\Dao\Sale\Item */
    private $daoSaleItem;
    /** @var  \Praxigento\Pv\Repo\Dao\Stock\Item */
    private $daoStockItem;

    public function __construct(
        \Praxigento\Pv\Repo\Dao\Sale $daoSale,
        \Praxigento\Pv\Repo\Dao\Sale\Item $daoSaleItem,
        \Praxigento\Pv\Repo\Dao\Stock\Item $daoStockItem
    )
    {
        $this->daoSale = $daoSale;
        $this->daoSaleItem = $daoSaleItem;
        $this->daoStockItem = $daoStockItem;
    }

    /**
     * Save PV data on sale order save.
     * @param ARequest $request
     * @return AResponse
     */
    public function exec(ARequest $request)
    {
        $result = new AResponse();
        $orderId = $request->getSaleOrderId();
        $datePaid = $request->getSaleOrderDatePaid();
        $items = $request->getOrderItems();
        /* for all items get PV data by warehouse */
        $orderTotal = 0;
        foreach ($items as $item) {
            $prodId = $item->getProductId();
            $stockId = $item->getStockId();
            $itemId = $item->getItemId();
            $pv = $this->daoStockItem->getPvByProductAndStock($prodId, $stockId);
            $qty = $item->getQuantity();
            $total = $pv * $qty;
            $eItem = new \Praxigento\Pv\Repo\Data\Sale\Item();
            $eItem->setItemRef($itemId);
            $eItem->setSubtotal($total);
            $eItem->setDiscount(0);
            $eItem->setTotal($total);
            $this->daoSaleItem->replace($eItem);
            $orderTotal += $total;
        }
        /* save order data */
        $eOrder = new \Praxigento\Pv\Repo\Data\Sale();
        $eOrder->setSaleRef($orderId);
        $eOrder->setSubtotal($orderTotal);
        $eOrder->setDiscount(0);
        $eOrder->setTotal($orderTotal);
        $eOrder->setDatePaid($datePaid);
        $this->daoSale->replace($eOrder);
        $result->markSucceed();
        return $result;

    }

}