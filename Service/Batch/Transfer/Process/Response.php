<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Batch\Transfer\Process;

/**
 * @method int getOperationId()
 * @method void setOperationId(int $data)
 */
class Response
    extends \Praxigento\Core\App\Service\Response
{
    const OPERATION_ID = 'operationId';
}