<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Plugin\Catalog\Model;

use Praxigento\Pv\Config as Cfg;

class Layer
{
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
        $conn = $query->getConnection();
        $resource = $collection->getResource();
        $rsrc = $query->getAdapter()->getConnection();
        /* aliases and tables */
        $asStockItem = 'csi';
        $asStockPv = 'ppsi';
        $tblStockItem = [$asStockItem => $conn->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM)];
//        $tblStockItem = [$asStockItem => $resource->getDefaultAttributes(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM)];
//        $tblQty = [$asQty => $conn->getTableName(Quantity::ENTITY_NAME)];
//        $tblLot = [$asLot => $conn->getTableName(Lot::ENTITY_NAME)];
//        /* LEFT JOIN cataloginventory_stock_item AS stock_item */
//        $on = $asQty . '.' . Quantity::ATTR_STOCK_ITEM_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
//        $cols = [Alias::AS_QTY => Quantity::ATTR_TOTAL];
//        $query->joinLeft($tblQty, $on, $cols);
        return $result;
    }
}