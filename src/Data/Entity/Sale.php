<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Sale extends EntityBase
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
        $result = parent::getData(self::ATTR_DATE_PAID);
        return $result;
    }

    /**
     * @return double
     */
    public function getDiscount()
    {
        $result = parent::getData(self::ATTR_DISCOUNT);
        return $result;
    }

    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_SALE_ID];
    }

    /**
     * @return int
     */
    public function getSaleId()
    {
        $result = parent::getData(self::ATTR_SALE_ID);
        return $result;
    }

    /**
     * @return double
     */
    public function getSubtotal()
    {
        $result = parent::getData(self::ATTR_SUBTOTAL);
        return $result;
    }

    /**
     * @return double
     */
    public function getTotal()
    {
        $result = parent::getData(self::ATTR_TOTAL);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setDatePaid($data)
    {
        parent::setData(self::ATTR_DATE_PAID, $data);
    }

    /**
     * @param double $data
     */
    public function setDiscount($data)
    {
        parent::setData(self::ATTR_DISCOUNT, $data);
    }

    /**
     * @param int $data
     */
    public function setSaleId($data)
    {
        parent::setData(self::ATTR_SALE_ID, $data);
    }

    /**
     * @param double $data
     */
    public function setSubtotal($data)
    {
        parent::setData(self::ATTR_SUBTOTAL, $data);
    }

    /**
     * @param double $data
     */
    public function setTotal($data)
    {
        parent::setData(self::ATTR_TOTAL, $data);
    }

}