<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Api\Sale\Order\Get\Response;

/**
 * This is API response data, getters only are defined.
 * Response is based on \Praxigento\Pv\Repo\Entity\Data\Sale entity data object.
 */
interface Data
{
    /**
     * @return string
     */
    public function getDatePaid();

    /**
     * @return double
     */
    public function getDiscount();

    /**
     * @return int
     */
    public function getSaleId();

    /**
     * @return double
     */
    public function getSubtotal();

    /**
     * @return double
     */
    public function getTotal();

}