<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Framework\View\Element\UiComponent\DataProvider;

use Praxigento\Pv\Config as Cfg;

/**
 * Add PV data for sale orders.
 */
class CollectionFactory
{
    /** @var  Sub\QueryModifier */
    protected $_subQueryModifier;

    public function __construct(
        Sub\QueryModifier $subQueryModufier
    ) {
        $this->_subQueryModifier = $subQueryModufier;
    }

    /**
     * Modify result collection for "sales_order_grid_data_source" (add joins & filter mapping, MOBI-351).
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Closure $proceed
     * @param $requestName
     * @return null
     */
    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == Cfg::DS_SALES_ORDERS_GRID) {
            if ($result instanceof \Magento\Sales\Model\ResourceModel\Order\Grid\Collection) {
                /* add JOINS to the select query */
                $this->_subQueryModifier->populateSelect($result);
                /* add fields to mapping */
                $this->_subQueryModifier->addFieldsMapping($result);
            }
        }
        return $result;
    }
}