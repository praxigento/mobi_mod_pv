<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Api\Sale\Order\Get;

/**
 * Request to get entries for downline tree node.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Flancer32\Lib\Data
{
    /**
     * Incremental ID of the order.
     *
     * @return string
     */
    public function getIdInc()
    {
        $result = parent::getIdInc();
        return $result;
    }

    /**
     * Incremental ID of the order.
     *
     * @param stirng $data
     */
    public function setIdInc($data)
    {
        parent::setIdInc($data);
    }

}