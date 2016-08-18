<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Plugin\Catalog\Model\ResourceModel\Product;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class CollectionFactory_UnitTest
    extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  CollectionFactory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class);
        $this->mResource = $this->_mockResourceConnection();
        /** create object to test */
        $this->obj = new CollectionFactory(
            $this->mResource
        );
    }

    public function test_aroundCreate()
    {
        /** === Test Data === */
        $DATA = [];
        $RESULT = $this->_mock(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        /** === Setup Mocks === */
        $mProceed = function ($dataIn) use ($DATA, $RESULT) {
            $this->assertEquals($DATA, $dataIn);
            return $RESULT;
        };
        // $query = $result->getSelect();
        $mQuery = $this->_mockDbSelect();
        $RESULT->shouldReceive('getSelect')->once()
            ->andReturn($mQuery);
        // $tbl = [self::AS_TBL_PROD_PV => $this->_resource->getTableName(Product::ENTITY_NAME)];
        $this->mResource
            ->shouldReceive('getTableName')->once()
            ->andReturn('TABLE');
        // $query->joinLeft($tbl, $on, $cols);
        $mQuery->shouldReceive('joinLeft')->once();
        // $result->addFilterToMap(self::AS_FLD_PV, self::FULL_PV);
        // $result->addFilterToMap('`e`.`' . self::AS_FLD_PV . '`', self::FULL_PV);
        $RESULT->shouldReceive('addFilterToMap')->twice();
        /** === Call and asserts  === */
        $res = $this->obj->aroundCreate(
            $this->mSubject,
            $mProceed,
            $DATA
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(CollectionFactory::class, $this->obj);
    }

}