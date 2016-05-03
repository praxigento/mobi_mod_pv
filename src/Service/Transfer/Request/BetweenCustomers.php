<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer\Request;

class BetweenCustomers extends Base
{
    const FROM_CUSTOMER_ID = 'from_customer_id';
    const TO_CUSTOMER_ID = 'to_customer_id';

    /**
     * @return int
     */
    public function getFromCustomerId()
    {
        $result = parent::getData(self::FROM_CUSTOMER_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getToCustomerId()
    {
        $result = parent::getData(self::TO_CUSTOMER_ID);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setFromCustomerId($data)
    {
        parent::setData(self::FROM_CUSTOMER_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setToCustomerId($data)
    {
        parent::setData(self::TO_CUSTOMER_ID, $data);
    }
}