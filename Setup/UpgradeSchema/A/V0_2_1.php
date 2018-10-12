<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Setup\UpgradeSchema\A;

use Magento\Framework\DB\Adapter\AdapterInterface as ADbAdapter;
use Praxigento\Accounting\Repo\Data\Transaction as EAccTran;
use Praxigento\Pv\Repo\Data\Sale as ESale;

/**
 * Add "prxgt_pv_sale.trans_ref" field.
 */
class V0_2_1
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function exec($setup)
    {
        $conn = $setup->getConnection();
        $table = $setup->getTable(ESale::ENTITY_NAME);

        $conn->addColumn(
            $table,
            ESale::A_TRANS_REF,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 3,
                'nullable' => true,
                'unsigned' => true,
                'after' => ESale::A_TOTAL,
                'comment' => 'Reference to the related PV transaction.'
            ]
        );

        /* add indexes */
        $fields = [ESale::A_TRANS_REF];
        $type = ADbAdapter::INDEX_TYPE_UNIQUE;
        $tbl = $setup->getTable(ESale::ENTITY_NAME);
        $idx = $setup->getIdxName($tbl, $fields, $type);
        $conn->addIndex($tbl, $idx, $fields, $type);

        /* add foreign keys */
        $tbl = $setup->getTable(ESale::ENTITY_NAME);
        $tblRef = $setup->getTable(EAccTran::ENTITY_NAME);
        $fkName = $setup->getFkName($tbl, $tblRef, ESale::A_TRANS_REF, EAccTran::A_ID);
        $conn->addForeignKey($fkName, $tbl, ESale::A_TRANS_REF, $tblRef, EAccTran::A_ID);
    }

}