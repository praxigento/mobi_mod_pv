<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Batch\Transfer;

use Praxigento\Pv\Service\Batch\Transfer\Save\A\Data\Item as DItem;
use Praxigento\Pv\Service\Batch\Transfer\Save\Request as ARequest;
use Praxigento\Pv\Service\Batch\Transfer\Save\Response as AResponse;

/**
 * Parse CSV file data and save it to DB.
 */
class Save
{
    /** @var \Praxigento\Pv\Service\Batch\Transfer\Save\A\ProcessItems */
    private $aProcItems;
    /** @var \Praxigento\Core\Api\Helper\Csv */
    private $hlpCsv;

    public function __construct(
        \Praxigento\Core\Api\Helper\Csv $hlpCsv,
        \Praxigento\Pv\Service\Batch\Transfer\Save\A\ProcessItems $aProcItems
    ) {
        $this->hlpCsv = $hlpCsv;
        $this->aProcItems = $aProcItems;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        $file = $request->getFile();
        $items = $this->readCsv($file);
        list($batchId, $senderErrors, $receiverErrors) = $this->aProcItems->exec($items);
        $result->setBatchId($batchId);
        $result->setSenderMlmIdError($senderErrors);
        $result->setReceiverMlmIdError($receiverErrors);
        return $result;
    }

    /**
     * Read & parse CSV file.
     *
     * @param string $fullPath
     * @return DItem[]
     */
    private function readCsv($fullPath)
    {
        $result = [];
        $entries = $this->hlpCsv->read($fullPath);
        foreach ($entries as $one) {
            $i=0;
            $fromId = $one[$i++];
            $fromName = $one[$i++];
            $toId = $one[$i++];
            $toName = $one[$i++];
            $volume = $one[$i++];

            $item = new DItem();
            $item->from = $fromId;
            $item->to = $toId;
            $item->value = $volume;
            $result[] = $item;
        }
        return $result;
    }
}