<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2021
 */

namespace Praxigento\Pv\Setup\UpgradeSchema\A;

use Praxigento\Pv\Repo\Data\Trans\Batch\Item as EBatchItem;

/**
 * Add "prxgt_pv_trans_batch_item.warn_same_ids" field.
 */
class V0_2_3
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Praxigento\Core\Data $demPackage
     */
    public function exec($setup, $demPackage = null)
    {
        $conn = $setup->getConnection();
        $table = $setup->getTable(EBatchItem::ENTITY_NAME);

        $conn->addColumn(
            $table,
            EBatchItem::A_WARN_SAME_IDS,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'after' => EBatchItem::A_WARN_GROUP,
                'comment' => '\'true\' if sender ID is equal to recipient ID.'
            ]
        );
    }

}
