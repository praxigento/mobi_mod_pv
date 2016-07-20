<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Catalog\Model\ResourceModel\Product;

/**
 * Plugin for "\Magento\Catalog\Model\ResourceModel\Product\Collection" to enable ordering by additional attributes.
 */
class Collection
{
    public function aroundAddOrder(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $field,
        $dir = \Magento\Framework\Data\Collection::SORT_ORDER_DESC
    ) {
        $result = $proceed($field, $dir);
        if (CollectionFactory::AS_FLD_PV == $field) {
            $order = CollectionFactory::FULL_PV . ' ' . $dir;
            $subject->getSelect()->order($order);
        }
        return $result;
    }

    public function aroundAddFieldToFilter(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $attribute,
        $condition = null
    ) {
        if (CollectionFactory::AS_FLD_PV == $attribute) {
            $alias = CollectionFactory::FULL_PV;
            $result = $subject;
            $query = $result->getConnection()->prepareSqlCondition($alias, $condition);
            $result->getSelect()->where($query);
        } else {
            $result = $proceed($attribute, $condition);
        }
        return $result;
    }
}