<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Setup;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Data\Trans\Batch as EBatch;
use Praxigento\Pv\Repo\Data\Trans\Batch\Item as EBatchItem;

class UpgradeSchema
    implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /** @var \Praxigento\Core\App\Setup\Dem\Tool */
    private $toolDem;

    public function __construct(
        \Praxigento\Core\App\Setup\Dem\Tool $toolDem
    ) {
        $this->toolDem = $toolDem;
    }

    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();
        $version = $context->getVersion();

        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Pv/package/Transfer';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        if ($version == Cfg::MOD_VERSION_0_1_0) {

            /* add Batch entity */
            $demEntity = $demPackage->get('entity/Batch');
            $this->toolDem->createEntity(EBatch::ENTITY_NAME, $demEntity);
            /* add Batch Item entity */
            $demEntity = $demPackage->get('package/Batch/entity/Item');
            $this->toolDem->createEntity(EBatchItem::ENTITY_NAME, $demEntity);

        }
        $setup->endSetup();
    }
}