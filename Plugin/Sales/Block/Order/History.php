<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Pv\Plugin\Sales\Block\Order;

use Praxigento\Core\Config as Cfg;
use Praxigento\Pv\Repo\Data\Sale as ASale;

class History
{
    /**
     * Tables aliases.
     */
    const AS_PRXGT_PV_SALE = 'pps';
    const A_CREDIT_ACC_ID = 'creditAccId';
    const A_SALE_REF = 'saleRef';
    /**
     * Attributes aliases.
     */
    const A_TOTAL = 'total';
    /**
     * Flag for single joining table
     * @var bool $isJoined
     */
    private $isJoined = false;

    public function afterGetOrders(
        \Magento\Sales\Block\Order\History $subject,
        \Magento\Sales\Model\ResourceModel\Order\Collection $result
    )
    {
        if (($result !== false) && ($this->isJoined === false)) {
            /** @var \Magento\Framework\DB\Select $query */
            $query = $result->getSelect();

            /* LEFT JOIN prxgt_pv_sale */
            $tbl = $result->getTable(ASale::ENTITY_NAME);
            $on = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_A_ENTITY_ID . '=' . self::AS_PRXGT_PV_SALE . '.' . ASale::ATTR_SALE_REF;
            $cols = [
                self::A_TOTAL => ASale::ATTR_TOTAL
            ];
            $query->joinLeft([self::AS_PRXGT_PV_SALE => $tbl], $on, $cols);
            $this->isJoined = true;
        }
        return $result;
    }

    /**
     * @param \Magento\Sales\Block\Order\History $subject
     * @param $result string
     * @return string name and path to template name
     */
    public function afterGetTemplate(
        \Magento\Sales\Block\Order\History $subject,
        $result
    )
    {
        return 'Praxigento_Pv::order/history.phtml';
    }
}
