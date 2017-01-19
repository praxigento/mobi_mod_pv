<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Plugin\Catalog\Model;

use Praxigento\Pv\Config as Cfg;

class Layer
{
    const AS_PV_WRHS = 'prxgt_pv_warehouse';
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
        $asStockStatus = 'stock_status_index';
        $asStockItem = 'csi';
        $asStockPv = 'ppsi';
        $tblStockItem = [$asStockItem => $collection->getTable(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM)];
        $tblStockPv = [$asStockPv => $collection->getTable(\Praxigento\Pv\Data\Entity\Stock\Item::ENTITY_NAME)];
        /* INNER JOIN cataloginventory_stock_item AS stock_item */
        $on = $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '='
            . $asStockStatus . '.' . Cfg::E_CATINV_STOCK_STATUS_A_PROD_ID;
        $on .= ' AND ' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '='
            . $asStockStatus . '.' . Cfg::E_CATINV_STOCK_STATUS_A_STOCK_ID;
        $cols = [];
        $query->joinInner($tblStockItem, $on, $cols);
        // LEFT JOIN `prxgt_pv_stock_item` AS `prxgtPvStock`
        $on = $asStockPv . '.' . \Praxigento\Pv\Data\Entity\Stock\Item::ATTR_STOCK_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $cols = [self::AS_PV_WRHS => \Praxigento\Pv\Data\Entity\Stock\Item::ATTR_PV];
        $query->joinLeft($tblStockPv, $on, $cols);
        return $result;
    }
}