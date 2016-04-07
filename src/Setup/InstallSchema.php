<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;

use Praxigento\Pv\Data\Entity\Product;
use Praxigento\Pv\Data\Entity\Sale;
use Praxigento\Pv\Data\Entity\Sale\Item as SaleItem;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{
    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Pv';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Product */
        $entityAlias = Product::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/Product');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Sale */
        $entityAlias = Sale::ENTITY_NAME;
        $demEntity = $demPackage->getData('entity/SaleOrder');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Sale Item*/
        $entityAlias = SaleItem::ENTITY_NAME;
        $demEntity = $demPackage->getData('package/SaleOrder/entity/OrderItem');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

    }


}