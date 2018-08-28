<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Batch\Transfer\Save;

/**
 * @method string getBatchId()
 * @method void setBatchId(string $data)
 * @method string[] getReceiverMlmIdError()
 * @method void setReceiverMlmIdError(string[] $data)
 * @method string[] getSenderMlmIdError()
 * @method void setSenderMlmIdError(string[] $data)
 */
class Response
    extends \Praxigento\Core\App\Service\Response
{
    const BATCH_ID = 'batchId';
    const RECEIVER_MLMID_ERROR = 'receiverMlmIdError';
    const SENDER_MLMID_ERROR = 'senderMlmIdError';
}