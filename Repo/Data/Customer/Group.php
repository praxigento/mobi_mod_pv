<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Data\Customer;
/**
 * Additional flags for customer group entity.
 */
class Group
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_CAN_SEE_PV = 'can_see_pv';
    const ATTR_GROUP_REF = 'group_ref';
    const ENTITY_NAME = 'prxgt_pv_cust_group';

    /** @return bool */
    public function getCanSeePv()
    {
        $result = parent::get(self::ATTR_CAN_SEE_PV);
        return $result;
    }

    /** @return int */
    public function getGroupRef()
    {
        $result = parent::get(self::ATTR_GROUP_REF);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_GROUP_REF];
    }

    /** @param bool $data */
    public function setCanSeePv($data)
    {
        parent::set(self::ATTR_CAN_SEE_PV, $data);
    }

    /** @param int $data */
    public function setGroupRef($data)
    {
        parent::set(self::ATTR_GROUP_REF, $data);
    }

}