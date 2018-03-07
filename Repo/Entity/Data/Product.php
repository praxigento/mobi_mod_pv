<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity\Data;
/**
 * Base PV attributes (related to the whole product).
 * Warehouse PV are in \Praxigento\Pv\Repo\Entity\Data\Stock\Item.
 */
class Product
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const ATTR_PROD_REF = 'prod_ref';
    const ATTR_PV = 'pv';
    const ENTITY_NAME = 'prxgt_pv_prod';

    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_PROD_REF];
    }

    /**
     * @return int
     */
    public function getProductRef()
    {
        $result = parent::get(self::ATTR_PROD_REF);
        return $result;
    }

    /**
     * @return float
     */
    public function getPv()
    {
        $result = parent::get(self::ATTR_PV);
        return $result;
    }


    /**
     * @param int $data
     */
    public function setProductRef($data)
    {
        parent::set(self::ATTR_PROD_REF, $data);
    }

    /**
     * @param float $data
     */
    public function setPv($data)
    {
        parent::set(self::ATTR_PV, $data);
    }

}