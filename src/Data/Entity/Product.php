<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Data\Entity;

class Product
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_PROD_REF = 'prod_ref';
    const ATTR_PV = 'pv';
    const ENTITY_NAME = 'prxgt_pv_prod';

    public function getPrimaryKeyAttrs()
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
     * @return double
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
     * @param double $data
     */
    public function setPv($data)
    {
        parent::set(self::ATTR_PV, $data);
    }

}