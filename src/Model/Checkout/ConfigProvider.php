<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Model\Checkout;

/**
 * Provide PV visibility configuration data for checkout process.
 */
class ConfigProvider
    implements \Magento\Checkout\Model\ConfigProviderInterface
{
    const CFG_CAN_SEE_PV = 'praxigentoCustomerCanSeePv';

    /** @var \Praxigento\Pv\Repo\Entity\Customer\Group */
    private $repoCustGroup;
    /** @var \Magento\Customer\Model\Session */
    private $session;

    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Praxigento\Pv\Repo\Entity\Customer\Group $repoCustGroup
    ) {
        $this->session = $session;
        $this->repoCustGroup = $repoCustGroup;
    }

    /**
     * Get 'Can See PV' flag for current customer.
     * @return bool
     */
    private function getCanSeePv()
    {
        $result = false;
        if ($this->session) {
            $groupId = $this->session->getCustomerGroupId();
            $entity = $this->repoCustGroup->getById($groupId);
            if ($entity) {
                $result = (bool)$entity->getCanSeePv();
            }
        }
        return $result;
    }

    public function getConfig()
    {
        $canSeePv = $this->getCanSeePv();
        /* and add configuration data to checkout config */
        $result = [
            self::CFG_CAN_SEE_PV => $canSeePv
        ];
        return $result;
    }
}