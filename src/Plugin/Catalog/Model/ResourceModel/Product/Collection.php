<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Catalog\Model\ResourceModel\Product;

/**
 * Plugin for "\Magento\Catalog\Model\ResourceModel\Product\Collection" to enable order & filter for
 * additional attributes.
 */
class Collection
{
    /**
     * Convert field alias from UI to DB pair 'tblAlias.fldName'.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     * @param $attribute
     * @param null $condition
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function aroundAddFieldToFilter(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $attribute,
        $condition = null
    ) {
        if (CollectionFactory::AS_FLD_PV == $attribute) {
            $alias = CollectionFactory::FULL_PV;
            $result = $subject;
            $conn = $result->getConnection();
            $query = $conn->prepareSqlCondition($alias, $condition);
            $select = $result->getSelect();
            $select->where($query);
        } else {
            $result = $proceed($attribute, $condition);
        }
        return $result;
    }

    /**
     * Convert field alias from UI to DB pair 'tblAlias.fldName'.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     * @param $field
     * @param string $dir
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function aroundAddOrder(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $field,
        $dir = \Magento\Framework\Data\Collection::SORT_ORDER_DESC
    ) {
        if (CollectionFactory::AS_FLD_PV == $field) {
            $result = $subject;
            $order = CollectionFactory::FULL_PV . ' ' . $dir;
            $select = $result->getSelect();
            $select->order($order);
        } else {
            $result = $proceed($field, $dir);
        }
        return $result;
    }
}