<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Catalog\Model\ResourceModel\Product;

use Praxigento\Pv\Data\Entity\Product;


/**
 * Plugin for "\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory" to add fields mapping to product collection.
 */
class CollectionFactory
{
    const AS_FLD_PV = 'prxgt_pv_wholesale';
    const AS_TBL_PROD_PV = 'prxgtPvProd';
    const FULL_PV = self::AS_TBL_PROD_PV . '.' . Product::ATTR_PV;

    public function aroundCreate(
        $subject,
        \Closure $proceed,
        array $data = []
    ) {
        $result = $proceed($data);
        if ($result instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {

            $query = $result->getSelect();
            $conn = $query->getConnection();
            /* LEFT JOIN `prxgt_pv_prod` AS `prxgtPvProd` */
            $tbl = [self::AS_TBL_PROD_PV => $conn->getTableName(Product::ENTITY_NAME)];
            $on = self::AS_TBL_PROD_PV . '.' . Product::ATTR_PROD_REF . '=e.entity_id';
            $cols = [
                self::AS_FLD_PV => Product::ATTR_PV
            ];
            $query->joinLeft($tbl, $on, $cols);
            $sql = (string)$query;

            /* add fields mapping */
            $result->addFilterToMap(self::AS_FLD_PV, self::FULL_PV);
            $result->addFilterToMap('`e`.`' . self::AS_FLD_PV . '`', self::FULL_PV);
        }
        return $result;
    }
}