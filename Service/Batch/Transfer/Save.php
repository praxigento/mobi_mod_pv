<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Batch\Transfer;

use Praxigento\Pv\Service\Batch\Transfer\Save\A\Data\Item as DItem;
use Praxigento\Pv\Service\Batch\Transfer\Save\Request as ARequest;
use Praxigento\Pv\Service\Batch\Transfer\Save\Response as AResponse;

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
        $this->aProcItems->exec($items);
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
            $from = $one[0];
            $to = $one[1];
            $value = $one[2];

            $item = new DItem();
            $item->from = $from;
            $item->to = $to;
            $item->value = $value;
            $result[] = $item;
        }
        return $result;
    }
}