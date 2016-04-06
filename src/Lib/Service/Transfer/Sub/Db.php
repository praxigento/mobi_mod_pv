<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Transfer\Sub;

use Praxigento\Core\Lib\Service\Repo\Request\GetEntityByPk;
use Praxigento\Downline\Data\Entity\Customer;

class Db
{

    public function getDownlineCustomer($customerId)
    {
        $result = null;
        $req = new GetEntityByPk(Customer::ENTITY_NAME, [Customer::ATTR_CUSTOMER_ID => $customerId]);
        $resp = $this->_callRepo->getEntityByPk($req);
        $result = $resp->getData();
        return $result;
    }
}