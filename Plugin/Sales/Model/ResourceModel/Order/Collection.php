<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Sales\Model\ResourceModel\Order;

use Praxigento\Pv\Api\Data\Sales\Order as DPvSales;
use Praxigento\Pv\Repo\Data\Sale as EPvSale;
use Praxigento\Warehouse\Config as Cfg;

class Collection
{
    const AS_PV_SALE = 'prxgtPvSale';
    /** @var  \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function aroundAddAttributeToSelect(
        \Magento\Sales\Model\ResourceModel\Order\Collection $subject,
        \Closure $proceed,
        $attribute,
        $joinType = false
    ) {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $result */
        $result = $proceed($attribute, $joinType);
        if ($attribute == '*') {
            $query = $result->getSelect();
            $this->queryAddPv($query);
        }
        return $result;
    }

    public function aroundAddFieldToSelect(
        \Magento\Sales\Model\ResourceModel\Order\Collection $subject,
        \Closure $proceed,
        $field,
        $alias = null
    ) {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $result */
        $result = $proceed($field, $alias);
        if ($field == '*') {
            $query = $result->getSelect();
            $this->queryAddPv($query);
        }
        return $result;
    }

    /**
     * @param \Magento\Framework\DB\Select $query
     * @return int|null|string
     * @throws \Zend_Db_Select_Exception
     */
    private function getAliasForSalesTable($query)
    {
        $result = null;
        $from = $query->getPart(\Magento\Framework\DB\Select::FROM);
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER);
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
        $asSales = $this->getAliasForSalesTable($query);
        if ($asSales) {
            /* there is 'sales_order' table - we can JOIN our tables to get PV */
            $tbl = $this->resource->getTableName(EPvSale::ENTITY_NAME);
            $as = self::AS_PV_SALE;
            $cols = [
                DPvSales::A_PV_SUBTOTAL => EPvSale::A_SUBTOTAL,
                DPvSales::A_PV_DISCOUNT => EPvSale::A_DISCOUNT,
                DPvSales::A_PV_GRAND => EPvSale::A_TOTAL,
            ];
            $cond = "$as." . EPvSale::A_SALE_REF . "=$asSales." . Cfg::E_SALE_ORDER_A_ENTITY_ID;
            $query->joinLeft([$as => $tbl], $cond, $cols);
        }
    }

}