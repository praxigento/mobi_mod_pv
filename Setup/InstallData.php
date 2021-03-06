<?php
/**
 * Populate DB schema with module's initial data
 * .
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Praxigento\Accounting\Repo\Data\Type\Asset as TypeAsset;
use Praxigento\Accounting\Repo\Data\Type\Operation as TypeOperation;
use Praxigento\Pv\Config as Cfg;

class InstallData extends \Praxigento\Core\App\Setup\Data\Base
{
    private function _addAccountingAssetsTypes()
    {
        $this->_conn->insertArray(
            $this->_resource->getTableName(TypeAsset::ENTITY_NAME),
            [
                TypeAsset::A_CODE,
                TypeAsset::A_NOTE,
                TypeAsset::A_IS_TRANSFERABLE
            ], [
                [
                    Cfg::CODE_TYPE_ASSET_PV,
                    'PV (Points of volume or volume points).',
                    true
                ]
            ]
        );
    }

    private function _addAccountingOperationsTypes()
    {
        $this->_conn->insertArray(
            $this->_resource->getTableName(TypeOperation::ENTITY_NAME),
            [TypeOperation::A_CODE, TypeOperation::A_NOTE],
            [
                [Cfg::CODE_TYPE_OPER_PV_SALE_PAID, 'PV assets related to order processing.'],
                [Cfg::CODE_TYPE_OPER_PV_TRANSFER, 'PV transfer between customers accounts.']
            ]
        );
    }

    protected function _setup()
    {
        $this->_addAccountingAssetsTypes();
        $this->_addAccountingOperationsTypes();
    }
}