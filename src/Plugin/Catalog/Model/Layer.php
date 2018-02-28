<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Plugin\Catalog\Model;

use Praxigento\Pv\Config as Cfg;

class Layer
{
    const AS_ATTR_PV_WRHS = 'prxgt_pv_warehouse';
    const AS_TBL_PV_STOCK_ITEM = 'prxgt_psi';
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
        $asStockItem = \Praxigento\Warehouse\Plugin\Catalog\Model\Layer::AS_CATALOGINVENTORY_STOCK_ITEM;
        $asStockPv = self::AS_TBL_PV_STOCK_ITEM;
        $tblStockPv = [$asStockPv => $collection->getTable(\Praxigento\Pv\Repo\Entity\Data\Stock\Item::ENTITY_NAME)];
        // LEFT JOIN `prxgt_pv_stock_item` AS `prxgtPvStock`
        $on = $asStockPv . '.' . \Praxigento\Pv\Repo\Entity\Data\Stock\Item::ATTR_ITEM_REF . '='
            . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $cols = [self::AS_ATTR_PV_WRHS => \Praxigento\Pv\Repo\Entity\Data\Stock\Item::ATTR_PV];
        $query->joinLeft($tblStockPv, $on, $cols);
        return $result;
    }
}