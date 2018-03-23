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

    /** @param string $data */
    public function setDatePaid($data)
    {
        parent::set(self::A_DATE_PAID, $data);
    }

    /** @param float $data */
    public function setDiscount($data)
    {
        parent::set(self::A_DISCOUNT, $data);
    }

    /** @param int $data */
    public function setSaleRef($data)
    {
        parent::set(self::A_SALE_REF, $data);
    }

    /** @param float $data */
    public function setSubtotal($data)
    {
        parent::set(self::A_SUBTOTAL, $data);
    }

    /** @param float $data */
    public function setTotal($data)
    {
        parent::set(self::A_TOTAL, $data);
    }

}