<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;

use Praxigento\Pv\Repo\Entity\Data\Product;
use Praxigento\Pv\Repo\Entity\Data\Sale;
use Praxigento\Pv\Repo\Entity\Data\Sale\Item as SaleItem;
use Praxigento\Pv\Repo\Entity\Data\Stock\Item as StockItem;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{
    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Pv';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Stock Item */
        $entityAlias = StockItem::ENTITY_NAME;
        $demEntity = $demPackage->get('package/Stock/entity/StockItem');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Product */
        $entityAlias = Product::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Product');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Sale */
        $entityAlias = Sale::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/SaleOrder');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Sale Item */
        $entityAlias = SaleItem::ENTITY_NAME;
        $demEntity = $demPackage->get('package/SaleOrder/entity/OrderItem');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

    }


}