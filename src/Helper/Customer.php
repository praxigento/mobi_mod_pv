<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Helper;

/**
 * Does current customer have permissions to see PV on the front.
 */
class Customer
{
    /** @var bool|null */
    private $cacheCanSee = null;
    /** @var \Praxigento\Pv\Repo\Entity\Customer\Group */
    private $repoPvCustGroup;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Praxigento\Pv\Repo\Entity\Customer\Group $repoPvCustGroup
    ) {
        $this->session = $session;
        $this->repoPvCustGroup = $repoPvCustGroup;
    }

    public function canSeePv()
    {
        if (is_null($this->cacheCanSee)) {
            $gid = $this->session->getCustomerGroupId();
            if (!is_null($gid)) {
                $item = $this->repoPvCustGroup->getById($gid);
                $this->cacheCanSee = (bool)$item->getCanSeePv();
            } else {
                $this->cacheCanSee = false;
            }
        }
        return $this->cacheCanSee;
    }
}