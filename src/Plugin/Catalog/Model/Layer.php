<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Plugin\Catalog\Model;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Entity\Data\Stock\Item as EPvStockItem;

class Layer
{
    /** Aliases for tables used in query */
    const AS_PV_STOCK_ITEM = 'prxgt_psi';

    /** Aliases for attributes used in query */
    const A_PV_WRHS = 'prxgt_pv_warehouse';

    /**
     * Join warehouse PV data to product collection.
     *
     * @param \Magento\Catalog\Model\Layer $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\Layer
     */
    public function aroundPrepareProductCollection(
        \Magento\Catalog\Model\Layer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        $result = $proceed($collection);
        $query = $collection->getSelect();

        /* aliases and tables */
        $asStockItem = \Praxigento\Warehouse\Plugin\Catalog\Model\Layer::AS_CATINV_STOCK_ITEM;
        $asStockPv = self::AS_PV_STOCK_ITEM;

        /* LEFT JOIN prxgt_pv_stock_item */
        $tbl = $collection->getTable(\Praxigento\Pv\Repo\Entity\Data\Stock\Item::ENTITY_NAME);
        $as = $asStockPv;
        $cols = [
            self::A_PV_WRHS => EPvStockItem::ATTR_PV
        ];
        $cond = $as . '.' . EPvStockItem::ATTR_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $query->joinLeft([$as => $tbl], $cond, $cols);
        return $result;
    }
}