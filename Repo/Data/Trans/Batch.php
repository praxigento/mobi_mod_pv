<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Repo\Data\Trans;

/**
 * PV transfer batch.
 */
class Batch
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_CUST_REF = 'cust_ref';
    const A_ID = 'id';
    const A_USER_REG = 'user_ref';

    const ENTITY_NAME = 'prxgt_pv_trans_batch';

    /** @return int */
    public function getCustRef()
    {
        $result = parent::get(self::A_CUST_REF);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = parent::get(self::A_ID);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_ID];
    }

    /** @return int */
    public function getUserRef()
    {
        $result = parent::get(self::A_USER_REG);
        return $result;
    }

    /** @param int $data */
    public function setCustRef($data)
    {
        parent::set(self::A_CUST_REF, $data);
    }

    /** @param int $data */
    public function setId($data)
    {
        parent::set(self::A_ID, $data);
    }

    /** @param int $data */
    public function setUserRef($data)
    {
        parent::set(self::A_USER_REG, $data);
    }

}