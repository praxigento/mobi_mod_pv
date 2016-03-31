<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Praxigento\Core\Lib\Setup\Db as Db;
use Praxigento\Pv\Lib\Entity\Sale;
use Praxigento\Pv\Lib\Entity\Sale\Item as SaleItem;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{
    protected function _setup(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Pv';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Sale */
        $entityAlias = Sale::ENTITY_NAME;
        $demEntity = $demPackage['entity']['SaleOrder'];
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Sale Item*/
        $entityAlias = SaleItem::ENTITY_NAME;
        $demEntity = $demPackage['package']['SaleOrder']['entity']['OrderItem'];
        $this->_toolDem->createEntity($entityAlias, $demEntity);

    }


}