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
    /** @var \Praxigento\Pv\Repo\Dao\Customer\Group */
    private $daoPvCustGroup;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Praxigento\Pv\Repo\Dao\Customer\Group $daoPvCustGroup
    ) {
        $this->session = $session;
        $this->daoPvCustGroup = $daoPvCustGroup;
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
            $item = $this->daoPvCustGroup->getById($gid);
            if ($item) $result = (bool)$item->getCanSeePv();
            $this->cacheCanSeeByGid[$gid] = $result;
        } else {
            $result = $this->cacheCanSeeByGid[$gid];
        }
        return $result;
    }
}