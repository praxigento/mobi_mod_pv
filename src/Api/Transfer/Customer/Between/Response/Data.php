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
 * @method void setIsInvalidCountries(bool $data)
 * @method void setIsInvalidDownline(bool $data)
 * @method void setOperationId(int $data)
 */
class Data
    extends \Praxigento\Core\Data
{
    /**
     * 'true' - Country of the PV recipient is not the same as of PV sender.
     *
     * @return bool|null
     */
    public function getIsInvalidCountries()
    {
        $result = parent::getIsInvalidCountries();
        return $result;
    }

    /**
     * 'true' - PV recipient is not in the downline of the PV sender.
     *
     * @return bool|null
     */
    public function getIsInvalidDownline()
    {
        $result = parent::getIsInvalidDownline();
        return $result;
    }

    /**
     * Operation ID if transfer is successfully completed.
     *
     * @return int|null
     */
    public function getOperationId()
    {
        $result = parent::getOperationId();
        return $result;
    }

}