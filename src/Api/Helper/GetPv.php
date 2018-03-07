<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Api\Helper;


interface GetPv
{
    /**
     * Get Warehouse PV for product. Current stock (bound to store) will be used if $stockId is null.
     *
     * @param int $prodId
     * @param int|null $stockId
     * @return float
     */
    public function product($prodId, $stockId = null);
}