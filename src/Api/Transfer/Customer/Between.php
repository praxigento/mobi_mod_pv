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

    public function exec(\Praxigento\Pv\Api\Transfer\Customer\Between\Request $data)
    {
        /* create context for request processing */
        $ctx = new \Flancer32\Lib\Data();
        $ctx->set(self::CTX_REQ, $data);
        $ctx->set(self::CTX_RESULT, null);


        /* get results from context and place it to API response */
        /** @var \Praxigento\Core\Api\Response $result */
        $result = new \Praxigento\Core\Api\Response();
        $rs = $ctx->get(self::CTX_RESULT);
        $result->setData($rs);
        return $result;
    }

}