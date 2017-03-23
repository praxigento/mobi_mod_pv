<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Api\Transfer\Customer;

/**
 * Perform PV transfer from one customer to another.
 */
interface BetweenInterface
{
    /**
     * @param \Praxigento\Pv\Api\Transfer\Customer\Between\Request $data
     * @return \Praxigento\Pv\Api\Transfer\Customer\Between\Response
     */
    public function exec(\Praxigento\Pv\Api\Transfer\Customer\Between\Request $data);
}