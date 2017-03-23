<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Api\Transfer\Customer;


class Between
    implements \Praxigento\Pv\Api\Transfer\Customer\BetweenInterface
{
    const CTX_REQ = 'request'; // API request data
    const CTX_RESULT = 'result'; // data to place to response
    const VAR_AMOUNT = 'amount';
    const VAR_CUST_ID_FROM = 'cust_id_from';
    const VAR_CUST_ID_TO = 'cust_id_to';
    /** @var \Praxigento\Pv\Service\ITransfer */
    protected $callTransfer;

    public function __construct(
        \Praxigento\Pv\Service\ITransfer $callTransfer
    ) {
        $this->callTransfer = $callTransfer;
    }

    public function exec(\Praxigento\Pv\Api\Transfer\Customer\Between\Request $data)
    {
        /* create context for request processing */
        $ctx = new \Flancer32\Lib\Data();
        $ctx->set(self::CTX_REQ, $data);
        $ctx->set(self::CTX_RESULT, null);

        $this->preProcess($ctx);
        $this->process($ctx);
        $this->postProcess($ctx);

        /* get results from context and place it to API response */
        /** @var \Praxigento\Core\Api\Response $result */
        $result = new \Praxigento\Core\Api\Response();
        $rs = $ctx->get(self::CTX_RESULT);
        $result->setData($rs);
        return $result;
    }

    protected function postProcess(\Flancer32\Lib\Data $ctx)
    {
        /* sample for post-processing handling */
        /** @var \Praxigento\Pv\Api\Transfer\Customer\Between\Response\Data $res */
        $res = $ctx->get(self::CTX_RESULT);
        if ($res->getOperationId()) {
            $res->setIsSucceed(true);
        } else {
            $res->setIsSucceed(false);
        }
    }

    protected function preProcess(\Flancer32\Lib\Data $ctx)
    {
        /** @var \Praxigento\Pv\Api\Transfer\Customer\Between\Request $req */
        $req = $ctx->get(self::CTX_REQ);
        $custFrom = $req->getCustFromId();
        $custTo = $req->getCustToId();
        $amount = $req->getAmount();

        /* save pre-processing result (working variables)*/
        $ctx->set(self::VAR_CUST_ID_FROM, $custFrom);
        $ctx->set(self::VAR_CUST_ID_TO, $custTo);
        $ctx->set(self::VAR_AMOUNT, $amount);

    }

    protected function process(\Flancer32\Lib\Data $ctx)
    {
        /* get working variables from context */
        $custIdFrom = $ctx->get(self::VAR_CUST_ID_FROM);
        $custIdTo = $ctx->get(self::VAR_CUST_ID_TO);
        $amount = $ctx->get(self::VAR_AMOUNT);

        /* perform action */
        $req = new \Praxigento\Pv\Service\Transfer\Request\BetweenCustomers();
        $req->setFromCustomerId($custIdFrom);
        $req->setToCustomerId($custIdTo);
        $req->setValue($amount);
        $resp = $this->callTransfer->betweenCustomers($req);

        /* prepare result and place it to context */
        $result = new \Praxigento\Pv\Api\Transfer\Customer\Between\Response\Data();
        $operId = $resp->getOperationId();
        $result->setOperationId($operId);

        $ctx->set(self::CTX_RESULT, $result);
    }

}