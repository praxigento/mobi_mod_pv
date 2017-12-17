<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Query\Product;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Entity\Data\Stock\Item as EPvStockItem;

class GetPv
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_PV = 'pv';
    const AS_STOCK_ITEM = 'stockItem';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_PV = 'pv';

    /** Bound variables names ('camelCase' naming) */
    const BND_PROD_ID = 'prodId';
    const BND_STOCK_ID = 'stockId';

    /** Entities are used in the query */
    const E_INV_ITEM = Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM;
    const E_PV_STOCK_ITEM = EPvStockItem::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null) {
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asItem = self::AS_STOCK_ITEM;
        $asPv = self::AS_PV;

        /* FROM cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(self::E_INV_ITEM);
        $as = $asItem;
        $cols = [];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_pv_stock_item */
        $tbl = $this->resource->getTableName(self::E_PV_STOCK_ITEM);
        $as = $asPv;
        $cols = [
            self::A_PV => EPvStockItem::ATTR_PV
        ];
        $cond = $as . '.' . EPvStockItem::ATTR_STOCK_ITEM_REF . '=' . $asItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byProdId = "$asItem." . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . "=:" . self::BND_PROD_ID;
        $byStockId = "$asItem." . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . "=:" . self::BND_STOCK_ID;
        $result->where("($byProdId) AND ($byStockId)");

        return $result;
    }
}