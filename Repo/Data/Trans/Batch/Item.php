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
    const A_CUST_REF_FROM = 'cust_ref_from';
    const A_CUST_REF_TO = 'cust_ref_to';
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
    public function getCustRefFrom()
    {
        $result = parent::get(self::A_CUST_REF_FROM);
        return $result;
    }

    /** @return int */
    public function getCustRefTo()
    {
        $result = parent::get(self::A_CUST_REF_TO);
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
    public function setCustRefFrom($data)
    {
        parent::set(self::A_CUST_REF_FROM, $data);
    }

    /** @param int $data */
    public function setCustRefTo($data)
    {
        parent::set(self::A_CUST_REF_TO, $data);
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