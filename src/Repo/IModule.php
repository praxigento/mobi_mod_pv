<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo;


interface IModule
{
    /**
     * @param int $id
     * @return \Praxigento\Downline\Repo\Entity\Data\Customer
     */
    public function getDownlineCustomerById($id);

    /**
     * Get Customer Mage ID from sale order that is selected by order ID.
     *
     * @param int $saleId
     * @return int
     */
    public function getSaleOrderCustomerId($saleId);
}