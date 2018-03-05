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
    /** @var array permissions by customer group */
    private $cacheCanSeeByGid = [];
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

    /**
     * Cached accessor for 'Can See PV' flag.
     *
     * @param int|null $gid customer group ID, if 'null' - group for current customer is used.
     * @return bool
     */
    public function canSeePv($gid = null)
    {
        $result = false;
        if (is_null($gid)) {
            $gid = $this->session->getCustomerGroupId();
        }
        if (!isset($this->cacheCanSeeByGid[$gid])) {
            $item = $this->repoPvCustGroup->getById($gid);
            if ($item) $result = (bool)$item->getCanSeePv();
            $this->cacheCanSeeByGid[$gid] = $result;
        } else {
            $result = $this->cacheCanSeeByGid[$gid];
        }
        return $result;
    }
}