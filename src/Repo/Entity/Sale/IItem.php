<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity\Sale;


interface IItem
    extends \Praxigento\Core\Repo\IEntity
{

    /**
     * Get array of the PvSaleItems entities by Magento order ID.
     * @param int $orderId
     * @return \Praxigento\Pv\Data\Entity\Sale\Item[] index is a $saleItemId
     */
    public function getItemsByOrderId($orderId);
}