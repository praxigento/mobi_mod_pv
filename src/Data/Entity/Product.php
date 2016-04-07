<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Product extends EntityBase
{
    const ATTR_PROD_REF = 'prod_ref';
    const ATTR_PV = 'pv';
    const ENTITY_NAME = 'prxgt_pv_prod';


    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_PROD_REF];
    }

    /**
     * @return int
     */
    public function getProductRef()
    {
        $result = parent::getData(self::ATTR_PROD_REF);
        return $result;
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
     * @param int $data
     */
    public function setProductRef($data)
    {
        parent::setData(self::ATTR_PROD_REF, $data);
    }

    /**
     * @param double $data
     */
    public function setPv($data)
    {
        parent::setData(self::ATTR_PV, $data);
    }

}