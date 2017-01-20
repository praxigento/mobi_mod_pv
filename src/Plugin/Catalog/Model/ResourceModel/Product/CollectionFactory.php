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
    const AS_ATTR_PV = 'prxgt_pv_wholesale';
    const AS_TBL_PROD_PV = 'prxgtPvProd';
    const FULL_PV = self::AS_TBL_PROD_PV . '.' . Product::ATTR_PV;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function aroundCreate(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $subject,
        \Closure $proceed,
        array $data = []
    ) {
        $result = $proceed($data);
        if ($result instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {
            $query = $result->getSelect();
            /* LEFT JOIN `prxgt_pv_prod` AS `prxgtPvProd` */
            $tbl = [self::AS_TBL_PROD_PV => $this->_resource->getTableName(Product::ENTITY_NAME)];
            $on = self::AS_TBL_PROD_PV . '.' . Product::ATTR_PROD_REF . '=e.entity_id';
            $cols = [
                self::AS_ATTR_PV => Product::ATTR_PV
            ];
            $query->joinLeft($tbl, $on, $cols);
            /* add fields mapping */
            $result->addFilterToMap(self::AS_ATTR_PV, self::FULL_PV);
            $result->addFilterToMap('`e`.`' . self::AS_ATTR_PV . '`', self::FULL_PV);
        }
        return $result;
    }
}