<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Helper;

/**
 * Application level helper to get PV data.
 */
class GetPv
    implements \Praxigento\Pv\Api\Helper\GetPv
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpWrhsStock;
    /** @var \Praxigento\Pv\Repo\Dao\Stock\Item */
    private $daoPvStockItem;

    public function __construct(
        \Praxigento\Pv\Repo\Dao\Stock\Item $daoPvStockItem,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpWrhsStock
    ) {
        $this->daoPvStockItem = $daoPvStockItem;
        $this->hlpWrhsStock = $hlpWrhsStock;
    }

    public function product($prodId, $stockId = null) {
        if (!$stockId) {
            $stockId = $this->hlpWrhsStock->getCurrentStockId();
        }
        $result = $this->daoPvStockItem->getPvByProductAndStock($prodId, $stockId);
        $result = number_format($result, 2);
        return $result;
    }
}