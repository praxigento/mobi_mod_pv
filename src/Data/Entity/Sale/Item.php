<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Data\Entity\Sale;

class Item
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_DISCOUNT = 'discount';
    const ATTR_SALE_ITEM_ID = 'sale_item_id';
    const ATTR_SUBTOTAL = 'subtotal';
    const ATTR_TOTAL = 'total';
    const ENTITY_NAME = 'prxgt_pv_sale_item';

    /**
     * @return double
     */
    public function getDiscount()
    {
        $result = parent::getData(self::ATTR_DISCOUNT);
        return $result;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_SALE_ITEM_ID];
    }

    /**
     * @return int
     */
    public function getSaleItemId()
    {
        $result = parent::getData(self::ATTR_SALE_ITEM_ID);
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
     * @param double $data
     */
    public function setDiscount($data)
    {
        parent::setData(self::ATTR_DISCOUNT, $data);
    }

    /**
     * @param int $data
     */
    public function setSaleItemId($data)
    {
        parent::setData(self::ATTR_SALE_ITEM_ID, $data);
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