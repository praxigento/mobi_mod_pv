<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Batch\Transfer\Process;

/**
 * @method int getBatchId()
 * @method void setBatchId(int $data)
 * @method string getDateApplied() - if 'null' than current date will be used
 * @method void setDateApplied(string $data)
 */
class Request
    extends \Praxigento\Core\App\Service\Request
{
    const BATCH_ID = 'batchId';
    const DATE_APPLIED = 'dateApplied';
}