<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Data\Stock;

class Item
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_ITEM_REF = 'item_ref';
    const ATTR_PV = 'pv';
    const ENTITY_NAME = 'prxgt_pv_stock_item';

    /** @return int */
    public function getItemRef()
    {
        $result = parent::get(self::ATTR_ITEM_REF);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ITEM_REF];
    }

    /** @return float */
    public function getPv()
    {
        $result = parent::get(self::ATTR_PV);
        return $result;
    }

    /** @param int $data */
    public function setItemRef($data)
    {
        parent::set(self::ATTR_ITEM_REF, $data);
    }

    /** @param float $data */
    public function setPv($data)
    {
        parent::set(self::ATTR_PV, $data);
    }
}