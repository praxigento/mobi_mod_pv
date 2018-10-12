<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Setup;

use Praxigento\Pv\Config as Cfg;

class UpgradeSchema
    implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    /** @var \Praxigento\Pv\Setup\UpgradeSchema\A\V0_2_0 */
    private $v0_2_0;
    /** @var \Praxigento\Pv\Setup\UpgradeSchema\A\V0_2_1 */
    private $v0_2_1;

    public function __construct(
        \Praxigento\Pv\Setup\UpgradeSchema\A\V0_2_0 $v0_2_0,
        \Praxigento\Pv\Setup\UpgradeSchema\A\V0_2_1 $v0_2_1
    ) {
        $this->v0_2_0 = $v0_2_0;
        $this->v0_2_1 = $v0_2_1;
    }

    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $setup->startSetup();
        $version = $context->getVersion();

        if ($version == Cfg::MOD_VERSION_0_1_0) {
            $this->v0_2_0->exec($setup);
            $this->v0_2_1->exec($setup);
        }
        if ($version == Cfg::MOD_VERSION_0_2_0) {
            $this->v0_2_1->exec($setup);
        }
        $setup->endSetup();
    }
}