<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Sale\Response;

class AccountPv extends \Praxigento\Core\Service\Base\Response
{
    const OPERATION_ID = 'operation_id';

    public function getOperationId()
    {
        $result = parent::getData(self::OPERATION_ID);
        return $result;
    }

    public function setOperationId($data)
    {
        parent::setData(self::OPERATION_ID, $data);
    }
}