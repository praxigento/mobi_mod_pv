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
    /** @var \Magento\Framework\App\State */
    private $state;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Customer\Model\Session $session,
        \Praxigento\Pv\Repo\Dao\Customer\Group $daoPvCustGroup
    ) {
        $this->state = $state;
        $this->session = $session;
        $this->daoPvCustGroup = $daoPvCustGroup;
    }

    /**
     * Cached accessor for 'Can See PV' flag bound to customer's groups.
     *
     * @param int|null $gid customer group ID, if 'null' - group for current customer is used.
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function canSeePv($gid = null)
    {
        $result = $this->isAdmin();
        if (!$result) {
            if (is_null($gid)) {
                $gid = $this->session->getCustomerGroupId();
            }

            if (!isset($this->cacheCanSeeByGid[$gid])) {
                $item = $this->daoPvCustGroup->getById($gid);
                if ($item) {
                    $result = (bool)$item->getCanSeePv();
                }
                $this->cacheCanSeeByGid[$gid] = $result;
            } else {
                $result = $this->cacheCanSeeByGid[$gid];
            }
        }
        return $result;
    }

    /**
     * Return 'true' for adminhtml area (backend).
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isAdmin()
    {
        $areaCode = $this->state->getAreaCode();
        $result = ($areaCode == \Magento\Framework\App\Area::AREA_ADMINHTML);
        return $result;
    }
}