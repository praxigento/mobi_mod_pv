<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Api\Transfer\Customer\Between\Response;

/**
 * Data for response to perform PV transfer from one customer to another.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 * @method void setIsSucceed(bool $data)
 * @method void setOperationId(int $data)
 */
class Data
    extends \Flancer32\Lib\Data
{
    /**
     * @return bool
     */
    public function getIsSucceed()
    {
        $result = parent::getIsSucceed();
        return $result;
    }

    /**
     * Operation ID if transfer is successfully completed.
     *
     * @return int
     */
    public function getOperationId()
    {
        $result = parent::getOperationId();
        return $result;
    }

}