<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Api\Helper\Validate;

use Praxigento\Pv\Repo\Data\Trans\Batch\Item as EBatchItem;

interface Transfer
{
    /**
     * Validate one item from the batch of PV transfers.
     *
     * @param \Praxigento\Pv\Repo\Data\Trans\Batch\Item $item
     * @return bool
     */
    public function isRestricted(EBatchItem $item);
}