<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Plugin\Catalog\Model\ResourceModel\Product;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class Collection_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  Collection */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        /** create object to test */
        $this->obj = new Collection();
    }

    public function test_aroundAddFieldToFilter_isPv()
    {
        /** === Test Data === */
        $ATTR = CollectionFactory::AS_FLD_PV;
        $CONDITION = 'condition';
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        // $conn = $result->getConnection();
        $mConn = $this->_mockConn();
        $this->mSubject
            ->shouldReceive('getConnection')->once()
            ->andReturn($mConn);
        // $query = $conn->prepareSqlCondition($alias, $condition);
        $mQuery = 'where query';
        $mConn->shouldReceive('prepareSqlCondition')->once()
            ->with(CollectionFactory::FULL_PV, $CONDITION)
            ->andReturn($mQuery);
        // $select = $result->getSelect();
        $mSelect = $this->_mockDbSelect();
        $this->mSubject
            ->shouldReceive('getSelect')->once()
            ->andReturn($mSelect);
        // $select->where($query);
        $mSelect->shouldReceive('where')->once()
            ->with($mQuery);
        /** === Call and asserts  === */
        $res = $this->obj->aroundAddFieldToFilter(
            $this->mSubject,
            $mProceed,
            $ATTR,
            $CONDITION
        );
        $this->assertEquals($this->mSubject, $res);
    }

    public function test_aroundAddFieldToFilter_notPv()
    {
        /** === Test Data === */
        $ATTR = 'uiAlias';
        $CONDITION = 'condition';
        $RESULT = 'processing result';
        /** === Setup Mocks === */
        $mProceed = function ($fieldIn, $dirIn) use ($ATTR, $CONDITION, $RESULT) {
            $this->assertEquals($ATTR, $fieldIn);
            $this->assertEquals($CONDITION, $dirIn);
            return $RESULT;
        };
        /** === Call and asserts  === */
        $res = $this->obj->aroundAddFieldToFilter(
            $this->mSubject,
            $mProceed,
            $ATTR,
            $CONDITION
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_aroundAddOrder_isPv()
    {
        /** === Test Data === */
        $FIELD = CollectionFactory::AS_FLD_PV;
        $DIR = \Magento\Framework\Data\Collection::SORT_ORDER_ASC;
        /** === Setup Mocks === */
        $mProceed = function () {
        };
        // $result = $subject;
        // $select = $result->getSelect();
        $mSelect = $this->_mockDbSelect(['order']);
        $this->mSubject
            ->shouldReceive('getSelect')->once()
            ->andReturn($mSelect);
        // $select->order($order);
        $mSelect->shouldReceive('order')->once()->with($DIR);
        /** === Call and asserts  === */
        $res = $this->obj->aroundAddOrder(
            $this->mSubject,
            $mProceed,
            $FIELD,
            $DIR
        );
        $this->assertEquals($this->mSubject, $res);
    }

    public function test_aroundAddOrder_notPv()
    {
        /** === Test Data === */
        $FIELD = 'uiAlias';
        $DIR = \Magento\Framework\Data\Collection::SORT_ORDER_ASC;
        $RESULT = 'processing result';
        /** === Setup Mocks === */
        $mProceed = function ($fieldIn, $dirIn) use ($FIELD, $DIR, $RESULT) {
            $this->assertEquals($FIELD, $fieldIn);
            $this->assertEquals($DIR, $dirIn);
            return $RESULT;
        };
        /** === Call and asserts  === */
        $res = $this->obj->aroundAddOrder(
            $this->mSubject,
            $mProceed,
            $FIELD,
            $DIR
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Collection::class, $this->obj);
    }
}