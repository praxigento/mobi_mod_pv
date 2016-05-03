<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo;


interface IModule
{
    /**
     * @param int $id
     * @return \Praxigento\Downline\Data\Entity\Customer
     */
    public function getDownlineCustomerById($id);
}