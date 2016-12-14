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
abstract class Base extends \Praxigento\Core\Service\Base\Response {
    const ERR_IS_NOT_DOWNLINE = 'receiver is not in the downline of sender';
}