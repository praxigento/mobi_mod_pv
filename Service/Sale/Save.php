<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Pv\Service\Sale;

use Praxigento\Pv\Service\Sale\Save\Request as ARequest;
use Praxigento\Pv\Service\Sale\Save\Response as AResponse;

class Save
{
    /** @var  \Praxigento\Pv\Repo\Entity\Sale */
    private $repoSale;
    /** @var  \Praxigento\Pv\Repo\Entity\Sale\Item */
    private $repoSaleItem;
    /** @var  \Praxigento\Pv\Repo\Entity\Stock\Item */
    private $repoStockItem;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Sale $repoSale,
        \Praxigento\Pv\Repo\Entity\Sale\Item $repoSaleItem,
        \Praxigento\Pv\Repo\Entity\Stock\Item $repoStockItem
    )
    {
        $this->repoSale = $repoSale;
        $this->repoSaleItem = $repoSaleItem;
        $this->repoStockItem = $repoStockItem;
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
            $pv = $this->repoStockItem->getPvByProductAndStock($prodId, $stockId);
            $qty = $item->getQuantity();
            $total = $pv * $qty;
            $eItem = new \Praxigento\Pv\Repo\Entity\Data\Sale\Item();
            $eItem->setItemRef($itemId);
            $eItem->setSubtotal($total);
            $eItem->setDiscount(0);
            $eItem->setTotal($total);
            $this->repoSaleItem->replace($eItem);
            $orderTotal += $total;
        }
        /* save order data */
        $eOrder = new \Praxigento\Pv\Repo\Entity\Data\Sale();
        $eOrder->setSaleRef($orderId);
        $eOrder->setSubtotal($orderTotal);
        $eOrder->setDiscount(0);
        $eOrder->setTotal($orderTotal);
        $eOrder->setDatePaid($datePaid);
        $this->repoSale->replace($eOrder);
        $result->markSucceed();
        return $result;

    }

}