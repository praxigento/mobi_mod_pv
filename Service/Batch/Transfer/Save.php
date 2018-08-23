<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Batch\Transfer;

use Praxigento\Pv\Service\Batch\Transfer\Save\Request as ARequest;
use Praxigento\Pv\Service\Batch\Transfer\Save\Response as AResponse;

class Save
{
    /** @var \Praxigento\Core\Api\Helper\Csv */
    private $hlpCsv;

    public function __construct(
        \Praxigento\Core\Api\Helper\Csv $hlpCsv
    ) {
        $this->hlpCsv = $hlpCsv;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        return $result;
    }
}