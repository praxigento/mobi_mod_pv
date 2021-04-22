<?php
/**
 * File creator: dmitriimakhov@gmail.com
 */

namespace Praxigento\Pv\Plugin\Magento\Sales\Block\Order;

use Praxigento\Core\Config as Cfg;
use Praxigento\Pv\Repo\Data\Sale as ASale;

class History
{
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
