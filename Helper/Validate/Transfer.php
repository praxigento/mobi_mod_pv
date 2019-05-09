<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Helper\Validate;


use Praxigento\Pv\Repo\Data\Trans\Batch\Item as EBatchItem;

/**
 * Default implementation for all projects.
 */
class Transfer
    implements \Praxigento\Pv\Api\Helper\Validate\Transfer
{

    public function validateBatchItem(EBatchItem $item)
    {
        return [];
    }
}