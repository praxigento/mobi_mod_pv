<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;

use Praxigento\Pv\Data\Entity\Product;
use Praxigento\Pv\Data\Entity\Sale;
use Praxigento\Pv\Data\Entity\Sale\Item as SaleItem;
use Praxigento\Pv\Data\Entity\Stock\Item as StockItem;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallSchema_UnitTest extends \Praxigento\Core\Test\BaseCase\Setup\Schema
{
    /** @var  InstallSchema */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new InstallSchema(
            $this->mResource,
            $this->mToolDem
        );
    }

    public function test_install()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        // $setup->startSetup();
        $this->mSetup
            ->shouldReceive('startSetup')->once();
        // $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);
        $mDemPackage = $this->_mock(DataObject::class);
        $this->mToolDem
            ->shouldReceive('readDemPackage')->once()
            ->withArgs([\Mockery::any(), '/dBEAR/package/Praxigento/package/Pv'])
            ->andReturn($mDemPackage);
        // $demEntity = $demPackage->get('package/Type/entity/Asset');
        $mDemPackage->shouldReceive('getData');
        //
        // $this->_toolDem->createEntity($entityAlias, $demEntity);
        //
        $this->mToolDem->shouldReceive('createEntity')->withArgs([StockItem::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Product::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Sale::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([SaleItem::ENTITY_NAME, \Mockery::any()]);
        // $setup->endSetup();
        $this->mSetup
            ->shouldReceive('endSetup')->once();
        /** === Call and asserts  === */
        $this->obj->install($this->mSetup, $this->mContext);
    }
}