<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Api\Service\Sale\Account;

use Praxigento\Pv\Api\Service\Sale\Account\Pv\Request as ARequest;
use Praxigento\Pv\Api\Service\Sale\Account\Pv\Response as AResponse;

interface Pv
{
    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request);

}