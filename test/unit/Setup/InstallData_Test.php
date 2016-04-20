<?php
/**
 * Empty class to get stub for tests
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Setup;


include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallData_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  InstallData */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mConn = $this->_mockConn();
        $this->mResource = $this->_mockResourceConnection($this->mConn);
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        /* create object to test */
        $this->obj = new InstallData(
            $this->mResource,
            $this->mRepoGeneric
        );
    }

    public function test_install()
    {
        /* === Setup Mocks === */
        $mSetup = $this->_mock(\Magento\Framework\Setup\ModuleDataSetupInterface::class);
        $mSetup->shouldReceive('startSetup')->once();
        $mSetup->shouldReceive('endSetup')->once();
        $mContext = $this->_mock(\Magento\Framework\Setup\ModuleContextInterface::class);
        // $this->_setup();
        $this->mConn->shouldReceive('getTableName')->twice();
        $this->mConn->shouldReceive('insertArray')->twice();
        /* === Call and asserts  === */
        $this->obj->install($mSetup, $mContext);
    }

}