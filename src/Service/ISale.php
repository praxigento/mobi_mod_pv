<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service;

use Praxigento\Core\ICached;

interface ISale extends ICached
{
    /**
     * Account PV on sale done.
     *
     * @param Sale\Request\AccountPv $request
     *
     * @return Sale\Response\AccountPv
     */
    public function accountPv(Sale\Request\AccountPv $request);
    
    /**
     * Save PV data on sale order save.
     *
     * @param Sale\Request\Save $req
     *
     * @return Sale\Response\Save
     */
    public function save(Sale\Request\Save $req);
}