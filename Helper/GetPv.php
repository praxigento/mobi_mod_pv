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
    /** @var \Praxigento\Pv\Repo\Dao\Stock\Item */
    private $daoPvStockItem;
    /** @var \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpWrhsStock;

    public function __construct(
        \Praxigento\Pv\Repo\Dao\Stock\Item $daoPvStockItem,
        \Praxigento\Core\Api\Helper\Format $hlpFormat,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpWrhsStock
    ) {
        $this->daoPvStockItem = $daoPvStockItem;
        $this->hlpWrhsStock = $hlpWrhsStock;
        $this->hlpFormat = $hlpFormat;
    }

    public function product($prodId, $stockId = null) {
        if (!$stockId) {
            $stockId = $this->hlpWrhsStock->getCurrentStockId();
        }
        $result = $this->daoPvStockItem->getPvByProductAndStock($prodId, $stockId);
        $result = $this->hlpFormat->toNumber($result);
        return $result;
    }
}