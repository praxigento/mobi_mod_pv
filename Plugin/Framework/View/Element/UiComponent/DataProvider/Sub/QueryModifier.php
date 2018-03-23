<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Framework\View\Element\UiComponent\DataProvider\Sub;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Data\Sale;

class QueryModifier
{
    const AS_FLD_PV_DISCOUNT = 'prxgt_pv_discount_sale_grid';
    const AS_FLD_PV_SUBTOTAL = 'prxgt_pv_subtotal_sale_grid';
    const AS_FLD_PV_TOTAL = 'prxgt_pv_total_sale_grid';
    const AS_TBL_PV_SALES = 'prxgtPvSales';

    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function addFieldsMapping(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
    ) {
        // total
        $fieldAlias = self::AS_FLD_PV_TOTAL;
        $fieldFullName = self::AS_TBL_PV_SALES . '.' . Sale::A_TOTAL;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        // discount
        $fieldAlias = self::AS_FLD_PV_DISCOUNT;
        $fieldFullName = self::AS_TBL_PV_SALES . '.' . Sale::A_DISCOUNT;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        // subtotal
        $fieldAlias = self::AS_FLD_PV_SUBTOTAL;
        $fieldFullName = self::AS_TBL_PV_SALES . '.' . Sale::A_SUBTOTAL;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
    }

    public function populateSelect(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
    ) {
        $select = $collection->getSelect();
        /* LEFT JOIN `prxgt_pv_sale` */
        $tbl = [self::AS_TBL_PV_SALES => $this->_resource->getTableName(Sale::ENTITY_NAME)];
        $on = self::AS_TBL_PV_SALES . '.' . Sale::A_SALE_REF . '=main_table.' . Cfg::E_SALE_ORDER_A_ENTITY_ID;
        $cols = [
            self::AS_FLD_PV_TOTAL => Sale::A_TOTAL,
            self::AS_FLD_PV_DISCOUNT => Sale::A_DISCOUNT,
            self::AS_FLD_PV_SUBTOTAL => Sale::A_SUBTOTAL
        ];
        $select->joinLeft($tbl, $on, $cols);
    }

}