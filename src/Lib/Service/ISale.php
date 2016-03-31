<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service;

interface ISale {
    /**
     * Account PV on sale done.
     *
     * @param Sale\Request\AccountPv $request
     *
     * @return Sale\Response\AccountPv
     */
    public function accountPv(Sale\Request\AccountPv $request);

    /**
     * Reset cached data.
     */
    public function cacheReset();

    /**
     * Save PV data on sale order save.
     *
     * @param Sale\Request\Save $request
     *
     * @return Sale\Response\Save
     */
    public function save(Sale\Request\Save $request);
}