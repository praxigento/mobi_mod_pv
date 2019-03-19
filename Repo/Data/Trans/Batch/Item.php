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
    const A_CUST_FROM_REF = 'cust_from_ref';
    const A_CUST_TO_REF = 'cust_to_ref';
    const A_ID = 'id';
    const A_VALUE = 'value';
    const A_WARN_BALANCE = 'warn_balance';
    const A_WARN_COUNTRY = 'warn_country';
    const A_WARN_DWNL = 'warn_dwnl';
    const A_WARN_GROUP = 'warn_group';
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

    /** @return float */
    public function getValue()
    {
        $result = parent::get(self::A_VALUE);
        return $result;
    }

    /** @return bool */
    public function getWarnBalance()
    {
        $result = parent::get(self::A_WARN_BALANCE);
        return $result;
    }

    /** @return bool */
    public function getWarnCountry()
    {
        $result = parent::get(self::A_WARN_COUNTRY);
        return $result;
    }

    /** @return bool */
    public function getWarnDwnl()
    {
        $result = parent::get(self::A_WARN_DWNL);
        return $result;
    }

    /** @return bool */
    public function getWarnGroup()
    {
        $result = parent::get(self::A_WARN_GROUP);
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

    /** @param float $data */
    public function setValue($data)
    {
        parent::set(self::A_VALUE, $data);
    }

    /** @param bool $data */
    public function setWarnBalance($data)
    {
        parent::set(self::A_WARN_BALANCE, $data);
    }

    /** @param bool $data */
    public function setWarnCountry($data)
    {
        parent::set(self::A_WARN_COUNTRY, $data);
    }

    /** @param bool $data */
    public function setWarnDwnl($data)
    {
        parent::set(self::A_WARN_DWNL, $data);
    }

    /** @param bool $data */
    public function setWarnGroup($data)
    {
        parent::set(self::A_WARN_GROUP, $data);
    }

}