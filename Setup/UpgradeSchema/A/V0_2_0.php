<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Setup\UpgradeSchema\A;

use Praxigento\Pv\Repo\Data\Trans\Batch as EBatch;
use Praxigento\Pv\Repo\Data\Trans\Batch\Item as EBatchItem;

/**
 * Add PV Batch Transfers support.
 */
class V0_2_0
{
    /** @var \Praxigento\Core\App\Setup\Dem\Tool */
    private $toolDem;

    public function __construct(
        \Praxigento\Core\App\Setup\Dem\Tool $toolDem
    ) {
        $this->toolDem = $toolDem;
    }

    public function exec($setup)
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Pv/package/Transfer';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        /* add Batch entity */
        $demEntity = $demPackage->get('entity/Batch');
        $this->toolDem->createEntity(EBatch::ENTITY_NAME, $demEntity);
        /* add Batch Item entity */
        $demEntity = $demPackage->get('package/Batch/entity/Item');
        $this->toolDem->createEntity(EBatchItem::ENTITY_NAME, $demEntity);
    }

}