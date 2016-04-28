<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service;

use Praxigento\Core\ICached;
use Praxigento\Pv\Lib\Service\Transfer\Request;
use Praxigento\Pv\Lib\Service\Transfer\Response;

interface ITransfer extends  ICached{
    /**
     * @param Request\BetweenCustomers $request
     *
     * @return Response\BetweenCustomers
     */
    public function betweenCustomers(Request\BetweenCustomers $request);

    /**
     * @param Request\CreditToCustomer $request
     *
     * @return Response\CreditToCustomer
     */
    public function creditToCustomer(Request\CreditToCustomer $request);

    /**
     * @param Request\DebitFromCustomer $request
     *
     * @return Response\DebitFromCustomer
     */
    public function debitFromCustomer(Request\DebitFromCustomer $request);

}