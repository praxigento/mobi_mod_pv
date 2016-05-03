<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer\Request;

class DebitFromCustomer extends Base
{
    const FROM_CUSTOMER_ID = 'from_customer_id';

    /**
     * @return int
     */
    public function getFromCustomerId()
    {
        $result = parent::getData(self::FROM_CUSTOMER_ID);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setFromCustomerId($data)
    {
        parent::setData(self::FROM_CUSTOMER_ID, $data);
    }
}