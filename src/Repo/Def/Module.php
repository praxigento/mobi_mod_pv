<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Def;

use Praxigento\Pv\Repo\IModule;

class Module implements IModule
{
    /** @var  \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $_repoDownlineCustomer;

    public function __construct(
        \Praxigento\Downline\Repo\Entity\ICustomer $repoDownlineCustomer
    ) {
        $this->_repoDownlineCustomer = $repoDownlineCustomer;
    }

    public function getDownlineCustomerById($id)
    {
        $result = $this->_repoDownlineCustomer->getById($id);
        return $result;
    }
}