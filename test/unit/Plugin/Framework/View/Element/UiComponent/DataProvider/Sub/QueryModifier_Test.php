<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Plugin\Framework\View\Element\UiComponent\DataProvider\Sub;

include_once(__DIR__ . '/../../../../../../../phpunit_bootstrap.php');

class QueryModifier_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  QueryModifier */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mResource = $this->_mockResourceConnection();
        /** create object to test */
        $this->obj = new QueryModifier(
            $this->mResource
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(QueryModifier::class, $this->obj);
    }

    public function test_addFieldsMapping()
    {
        /** === Test Data === */
        $COLLECTION = $this->_mock(\Magento\Sales\Model\ResourceModel\Order\Grid\Collection::class);
        /** === Setup Mocks === */
        // $collection->addFilterToMap($fieldAlias, $fieldFullName);
        $COLLECTION->shouldReceive('addFilterToMap')->once()
            ->with('prxgt_pv_total', 'prxgtPvSales.total');
        // $collection->addFilterToMap($fieldAlias, $fieldFullName);
        $COLLECTION->shouldReceive('addFilterToMap')->once()
            ->with('prxgt_pv_discount', 'prxgtPvSales.discount');
        // $collection->addFilterToMap($fieldAlias, $fieldFullName);
        $COLLECTION->shouldReceive('addFilterToMap')->once()
            ->with('prxgt_pv_subtotal', 'prxgtPvSales.subtotal');
        /** === Call and asserts  === */
        $this->obj->addFieldsMapping($COLLECTION);
    }


    public function test_populateSelect()
    {
        /** === Test Data === */
        $TABLE = 'table';
        $COLLECTION = $this->_mock(\Magento\Sales\Model\ResourceModel\Order\Grid\Collection::class);
        /** === Setup Mocks === */
        // $select = $collection->getSelect();
        $mSelect = $this->_mockDbSelect();
        $COLLECTION->shouldReceive('getSelect')->once()
            ->andReturn($mSelect);
        // $tbl = [self::AS_TBL_PV_SALES => $this->_resource->getTableName(Sale::ENTITY_NAME)];
        $this->mResource
            ->shouldReceive('getTableName')->once()
            ->andReturn($TABLE);
        // $select->joinLeft($tbl, $on, $cols);
        $mSelect->shouldReceive('joinLeft')->once();
        /** === Call and asserts  === */
        $this->obj->populateSelect($COLLECTION);
    }

}