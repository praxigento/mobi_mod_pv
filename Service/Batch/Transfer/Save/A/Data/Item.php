<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Batch\Transfer\Save\A\Data;

/**
 * CSV file item.
 */
class Item
    extends \Praxigento\Core\Data
{
    const FROM = 'from';
    const TO = 'to';
    const VALUE = 'value';

    /** @var string sender's MLM ID */
    public $from;
    /** @var string receiver's MLM ID */
    public $to;
    /** @var float transfer amount (>0) */
    public $value;
}