<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Data;

class Sale
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_DATE_PAID = 'date_paid';
    const A_DISCOUNT = 'discount';
    const A_SALE_REF = 'sale_ref';
    const A_SUBTOTAL = 'subtotal';
    const A_TOTAL = 'total';
    const A_TRANS_REF = 'trans_ref';

    const ENTITY_NAME = 'prxgt_pv_sale';

    /** @return string */
    public function getDatePaid()
    {
        $result = parent::get(self::A_DATE_PAID);
        return $result;
    }

    /** @return float */
    public function getDiscount()
    {
        $result = parent::get(self::A_DISCOUNT);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_SALE_REF];
    }

    /** @return int */
    public function getSaleRef()
    {
        $result = parent::get(self::A_SALE_REF);
        return $result;
    }

    /** @return float */
    public function getSubtotal()
    {
        $result = parent::get(self::A_SUBTOTAL);
        return $result;
    }

    /** @return float */
    public function getTotal()
    {
        $result = parent::get(self::A_TOTAL);
        return $result;
    }

    /** @return int */
    public function getTransRef()
    {
        $result = parent::get(self::A_TRANS_REF);
        return $result;
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDatePaid($data)
    {
        parent::set(self::A_DATE_PAID, $data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setDiscount($data)
    {
        parent::set(self::A_DISCOUNT, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setSaleRef($data)
    {
        parent::set(self::A_SALE_REF, $data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setSubtotal($data)
    {
        parent::set(self::A_SUBTOTAL, $data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setTotal($data)
    {
        parent::set(self::A_TOTAL, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setTransRef($data)
    {
        parent::set(self::A_TRANS_REF, $data);
    }

}