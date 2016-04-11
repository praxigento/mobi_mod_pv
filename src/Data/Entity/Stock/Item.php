<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Data\Entity\Stock;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Item extends EntityBase
{
    const ATTR_PV = 'pv';
    const ATTR_STOCK_ITEM_REF = 'stock_item_ref';
    const ENTITY_NAME = 'prxgt_pv_stock_item';

    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_STOCK_ITEM_REF];
    }

    /**
     * @return double
     */
    public function getPv()
    {
        $result = parent::getData(self::ATTR_PV);
        return $result;
    }

    /**
     * @return int
     */
    public function getStockItemRef()
    {
        $result = parent::getData(self::ATTR_STOCK_ITEM_REF);
        return $result;
    }

    /**
     * @param double $data
     */
    public function setPv($data)
    {
        parent::setData(self::ATTR_PV, $data);
    }

    /**
     * @param int $data
     */
    public function setStockItemRef($data)
    {
        parent::setData(self::ATTR_STOCK_ITEM_REF, $data);
    }
}