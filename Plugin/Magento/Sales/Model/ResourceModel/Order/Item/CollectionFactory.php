<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Magento\Sales\Model\ResourceModel\Order\Item;

use Praxigento\Pv\Api\Data\Sales\Order\Item as DPvSaleItem;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Data\Sale\Item as EPvSaleItem;

class CollectionFactory
{

    const AS_PV_SALE_ITEM = 'prxgtPvSaleItem';

    /** @var  \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function afterCreate(
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $subject,
        \Magento\Sales\Model\ResourceModel\Order\Item\Collection $result
    ) {
        $query = $result->getSelect();
        $this->queryAddPv($query);
        return $result;
    }

    /**
     * @param \Magento\Framework\DB\Select $query
     * @return int|null|string
     * @throws \Zend_Db_Select_Exception
     */
    private function getAliasForMainTable($query)
    {
        $result = null;
        $from = $query->getPart(\Magento\Framework\DB\Select::FROM);
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM);
        foreach ($from as $as => $item) {
            if (
                isset($item['tableName']) &&
                $item['tableName'] == $tbl
            ) {
                $result = $as;
                break;
            }
        }
        return $result;
    }

    /**
     * Add PV to original query.
     *
     * @param $query
     * @throws \Zend_Db_Select_Exception
     */
    private function queryAddPv($query)
    {
        $asSales = $this->getAliasForMainTable($query);
        if ($asSales) {
            /* there is 'sales_order' table - we can JOIN our tables to get PV */
            $tbl = $this->resource->getTableName(EPvSaleItem::ENTITY_NAME);
            $as = self::AS_PV_SALE_ITEM;
            $cols = [
                DPvSaleItem::A_PV_SUBTOTAL => EPvSaleItem::A_SUBTOTAL,
                DPvSaleItem::A_PV_DISCOUNT => EPvSaleItem::A_DISCOUNT,
                DPvSaleItem::A_PV_GRAND => EPvSaleItem::A_TOTAL,
            ];
            $cond = "$as." . EPvSaleItem::A_ITEM_REF . "=$asSales." . Cfg::E_SALE_ORDER_ITEM_A_ITEM_ID;
            $query->joinLeft([$as => $tbl], $cond, $cols);
        }
    }
}