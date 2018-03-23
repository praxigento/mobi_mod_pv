<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Data;
/**
 * Base PV attributes (related to the whole product).
 * Warehouse PV are in \Praxigento\Pv\Repo\Data\Stock\Item.
 */
class Product
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_PROD_REF = 'prod_ref';
    const A_PV = 'pv';
    const ENTITY_NAME = 'prxgt_pv_prod';

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_PROD_REF];
    }

    /**
     * @return int
     */
    public function getProductRef()
    {
        $result = parent::get(self::A_PROD_REF);
        return $result;
    }

    /**
     * @return float
     */
    public function getPv()
    {
        $result = parent::get(self::A_PV);
        return $result;
    }


    /**
     * @param int $data
     */
    public function setProductRef($data)
    {
        parent::set(self::A_PROD_REF, $data);
    }

    /**
     * @param float $data
     */
    public function setPv($data)
    {
        parent::set(self::A_PV, $data);
    }

}