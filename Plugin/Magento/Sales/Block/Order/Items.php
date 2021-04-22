<?php
/**
 * File creator: dmitriimakhov@gmail.com
 */

namespace Praxigento\Pv\Plugin\Magento\Sales\Block\Order;

use Praxigento\Core\Config as Cfg;
use Praxigento\Pv\Repo\Data\Sale as ASale;

class Items
{
    /**
     * @param \Magento\Sales\Block\Order\Items $subject
     * @param $result string
     * @return string name and path to template name
     */
    public function afterGetTemplate(
        \Magento\Sales\Block\Order\Items $subject,
        $result
    )
    {
        return 'Praxigento_Pv::order/items.phtml';
    }
}
