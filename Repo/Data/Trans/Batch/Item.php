<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Repo\Data\Trans\Batch;

/**
 * PV transfer batch item.
 */
class Item
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_BATCH_REF = 'batch_ref';
    const A_CUST_FROM_REF= 'cust_from_ref';
    const A_CUST_TO_REF = 'cust_to_ref';
    const A_ID = 'id';
    const A_RESTRICTED = 'restricted';
    const A_VALUE = 'value';

    const ENTITY_NAME = 'prxgt_pv_trans_batch_item';

    /** @return int */
    public function getBatchRef()
    {
        $result = parent::get(self::A_BATCH_REF);
        return $result;
    }

    /** @return int */
    public function getCustFromRef()
    {
        $result = parent::get(self::A_CUST_FROM_REF);
        return $result;
    }

    /** @return int */
    public function getCustToRef()
    {
        $result = parent::get(self::A_CUST_TO_REF);
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

    /** @return bool */
    public function getRestricted()
    {
        $result = parent::get(self::A_RESTRICTED);
        return $result;
    }

    /** @return float */
    public function getValue()
    {
        $result = parent::get(self::A_VALUE);
        return $result;
    }

    /** @param int $data */
    public function setBatchRef($data)
    {
        parent::set(self::A_BATCH_REF, $data);
    }

    /** @param int $data */
    public function setCustFromRef($data)
    {
        parent::set(self::A_CUST_FROM_REF, $data);
    }

    /** @param int $data */
    public function setCustToRef($data)
    {
        parent::set(self::A_CUST_TO_REF, $data);
    }

    /** @param int $data */
    public function setId($data)
    {
        parent::set(self::A_ID, $data);
    }

    /** @param bool $data */
    public function setRestricted($data)
    {
        parent::set(self::A_RESTRICTED, $data);
    }

    /** @param float $data */
    public function setValue($data)
    {
        parent::set(self::A_VALUE, $data);
    }

}