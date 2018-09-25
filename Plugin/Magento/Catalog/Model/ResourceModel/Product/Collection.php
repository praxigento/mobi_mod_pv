<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Magento\Catalog\Model\ResourceModel\Product;

/**
 * Convert Web UI grid columns to DB "table.field" to enable order & filter for
 * additional attributes.
 *
 * see \Praxigento\Pv\Plugin\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::aroundCreate
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
        if (CollectionFactory::A_PV_PRODUCT == $attribute) {
            /* map joined attribute to composite pair "tableAlias.field" */
            $alias = CollectionFactory::FQN_PV;
            $result = $subject;
            $conn = $result->getConnection();
            $query = $conn->prepareSqlCondition($alias, $condition);
            $select = $result->getSelect();
            $select->where($query);
        } else {
            /* proceed others Magento attributes as-is */
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
        if (CollectionFactory::A_PV_PRODUCT == $field) {
            /* map joined attribute to composite pair "tableAlias.field" */
            $result = $subject;
            $order = CollectionFactory::FQN_PV . ' ' . $dir;
            $select = $result->getSelect();
            $select->order($order);
        } else {
            /* proceed others Magento attributes as-is */
            $result = $proceed($field, $dir);
        }
        return $result;
    }
}