<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity\Data\Stock;

class Item
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_PV = 'pv';
    const ATTR_STOCK_ITEM_REF = 'stock_item_ref';
    const ENTITY_NAME = 'prxgt_pv_stock_item';

    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_ITEM_REF];
    }

    /**
     * @return double
     */
    public function getPv()
    {
        $result = parent::get(self::ATTR_PV);
        return $result;
    }

    /**
     * @return int
     */
    public function getStockItemRef()
    {
        $result = parent::get(self::ATTR_STOCK_ITEM_REF);
        return $result;
    }

    /**
     * @param double $data
     */
    public function setPv($data)
    {
        parent::set(self::ATTR_PV, $data);
    }

    /**
     * @param int $data
     */
    public function setStockItemRef($data)
    {
        parent::set(self::ATTR_STOCK_ITEM_REF, $data);
    }
}