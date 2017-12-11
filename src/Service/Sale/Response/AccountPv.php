<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Sale\Response;

class AccountPv extends \Praxigento\Core\App\Service\Base\Response
{
    const OPERATION_ID = 'operation_id';

    public function getOperationId()
    {
        $result = parent::get(self::OPERATION_ID);
        return $result;
    }

    public function setOperationId($data)
    {
        parent::set(self::OPERATION_ID, $data);
    }
}