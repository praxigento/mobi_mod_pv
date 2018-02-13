<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Sale\Order;

use Praxigento\Pv\Repo\Entity\Data\Sale as EPvSale;
use Praxigento\Pv\Repo\Entity\Data\Sale\Item as EPvSaleItem;
use Praxigento\Pv\Service\Sale\Order\Delete\Request as ARequest;
use Praxigento\Pv\Service\Sale\Order\Delete\Response as AResponse;

/**
 * Clean up relations between cancelled sale and PV data.
 */
class Delete
{
    /** @var \Praxigento\Pv\Repo\Entity\Sale */
    private $repoSale;
    /** @var \Praxigento\Pv\Repo\Entity\Sale\Item */
    private $repoSaleItem;
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    private $repoSaleOrder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $repoSaleOrder,
        \Praxigento\Pv\Repo\Entity\Sale $repoSale,
        \Praxigento\Pv\Repo\Entity\Sale\Item $repoSaleItem
    ) {
        $this->repoSaleOrder = $repoSaleOrder;
        $this->repoSale = $repoSale;
        $this->repoSaleItem = $repoSaleItem;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     * @throws \Exception
     */
    public function exec($request)
    {
        /** define local working data */
        assert($request instanceof ARequest);
        $saleId = $request->getSaleId();

        /** perform processing */
        /** @var \Magento\Sales\Api\Data\OrderInterface $sale */
        $sale = $this->repoSaleOrder->get($saleId);
        if ($sale) {
            $items = $sale->getAllItems();
            /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
            foreach ($items as $item) {
                $saleItemId = $item->getItemId();
                $this->removeSaleItemPv($saleItemId);
            }
            $this->removeSalePv($saleId);
        }
        /** compose result */
        $result = new AResponse();
        $result->isSucceed();
        return $result;
    }

    private function removeSaleItemPv($saleItemId)
    {
        $where = EPvSaleItem::ATTR_ITEM_REF . '=' . (int)$saleItemId;
        $this->repoSaleItem->delete($where);
    }

    private function removeSalePv($saleId)
    {
        $where = EPvSale::ATTR_SALE_REF . '=' . (int)$saleId;
        $this->repoSale->delete($where);
    }
}