<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Def;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\IModule;

class Module implements IModule
{
    /** @var  \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $_repoDownlineCustomer;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;

    public function __construct(
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Praxigento\Downline\Repo\Entity\ICustomer $repoDownlineCustomer

    ) {
        $this->_repoGeneric = $repoGeneric;
        $this->_repoDownlineCustomer = $repoDownlineCustomer;
    }

    public function getDownlineCustomerById($id)
    {
        $result = $this->_repoDownlineCustomer->getById($id);
        return $result;
    }

    public function getSaleOrderCustomerId($saleId)
    {
        $data = $this->_repoGeneric->getEntityByPk(
            Cfg::ENTITY_MAGE_SALES_ORDER,
            [Cfg::E_COMMON_A_ENTITY_ID => $saleId],
            [Cfg::E_SALE_ORDER_A_CUSTOMER_ID]
        );
        $result = $data[Cfg::E_SALE_ORDER_A_CUSTOMER_ID];
        return $result;
    }
}