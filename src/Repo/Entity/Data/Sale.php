<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity\Data;

class Sale
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_DATE_PAID = 'date_paid';
    const ATTR_DISCOUNT = 'discount';
    const ATTR_SALE_ID = 'sale_id';
    const ATTR_SUBTOTAL = 'subtotal';
    const ATTR_TOTAL = 'total';
    const ENTITY_NAME = 'prxgt_pv_sale';

    /**
     * @return string
     */
    public function getDatePaid()
    {
        $result = parent::get(self::ATTR_DATE_PAID);
        return $result;
    }

    /**
     * @return double
     */
    public function getDiscount()
    {
        $result = parent::get(self::ATTR_DISCOUNT);
        return $result;
    }

    /**
     * @return string[]
     */
    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_SALE_ID];
    }

    /**
     * @return int
     */
    public function getSaleId()
    {
        $result = parent::get(self::ATTR_SALE_ID);
        return $result;
    }

    /**
     * @return double
     */
    public function getSubtotal()
    {
        $result = parent::get(self::ATTR_SUBTOTAL);
        return $result;
    }

    /**
     * @return double
     */
    public function getTotal()
    {
        $result = parent::get(self::ATTR_TOTAL);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setDatePaid($data)
    {
        parent::set(self::ATTR_DATE_PAID, $data);
    }

    /**
     * @param double $data
     */
    public function setDiscount($data)
    {
        parent::set(self::ATTR_DISCOUNT, $data);
    }

    /**
     * @param int $data
     */
    public function setSaleId($data)
    {
        parent::set(self::ATTR_SALE_ID, $data);
    }

    /**
     * @param double $data
     */
    public function setSubtotal($data)
    {
        parent::set(self::ATTR_SUBTOTAL, $data);
    }

    /**
     * @param double $data
     */
    public function setTotal($data)
    {
        parent::set(self::ATTR_TOTAL, $data);
    }

}