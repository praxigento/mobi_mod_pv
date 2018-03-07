<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer\Response;

/**
 * @method int getOperationId()
 * @method void setOperationId(int $data)
 * @method array getTransactionsIds() [$transId, ...] or [$transId => $ref, ...]
 * @method void setTransactionsIds(array $data)
 */
abstract class Base extends \Praxigento\Core\App\Service\Base\Response {

}