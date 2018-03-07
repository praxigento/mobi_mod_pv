<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Api\Transfer\Customer\Between;

/**
 * Request to perform PV transfer from one customer to another.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\App\Api\Web\Request
{
    /**
     * Transfer amount (positive only values are possible).
     *
     * @return string
     */
    public function getAmount()
    {
        $result = parent::getAmount();
        return $result;
    }

    /**
     * ID for debit-customer. Currently logged customer is used if 'null'.
     *
     * @return int|null
     */
    public function getCustFromId()
    {
        $result = parent::getCustFromId();
        return $result;
    }

    /**
     * ID for credit-customer.
     *
     * @return int
     */
    public function getCustToId()
    {
        $result = parent::getCustToId();
        return $result;
    }

    /**
     * Transfer amount (positive only values are possible).
     *
     * @param string $data
     */
    public function setAmount($data)
    {
        parent::setAmount($data);
    }

    /**
     *  ID for debit-customer.
     *
     * @param int $data
     */
    public function setCustFromId($data)
    {
        parent::setCustFromId($data);
    }

    /**
     * ID for credit-customer.
     *
     * @param int $data
     */
    public function setCustToId($data)
    {
        parent::setCustToId($data);
    }
}