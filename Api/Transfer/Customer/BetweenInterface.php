<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Api\Transfer\Customer;

/**
 * Perform PV transfer from one customer to another.
 *
 * @deprecated TODO: use it or remove it.
 */
interface BetweenInterface
{
    /**
     * @param \Praxigento\Pv\Api\Transfer\Customer\Between\Request $data
     * @return \Praxigento\Pv\Api\Transfer\Customer\Between\Response
     */
    public function exec(\Praxigento\Pv\Api\Transfer\Customer\Between\Request $data);
}