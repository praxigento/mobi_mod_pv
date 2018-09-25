<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Pv\Plugin\Magento\Sales\Block\Order;

use Praxigento\Core\Config as Cfg;
use Praxigento\Pv\Repo\Data\Sale as ASale;

class PrintShipment
{
    /**
     * @param \Magento\Sales\Block\Order\PrintShipment $subject
     * @param $result string
     * @return string name and path to template name
     */
    public function afterGetTemplate(
        \Magento\Sales\Block\Order\PrintShipment $subject,
        $result
    )
    {
        return 'Praxigento_Pv::order/items.phtml';
    }
}
