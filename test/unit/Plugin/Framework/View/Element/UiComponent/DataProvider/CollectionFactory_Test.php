<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Plugin\Framework\View\Element\UiComponent\DataProvider;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');

class CollectionFactory_UnitTest
    extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mSubQueryModifier;
    /** @var  \Mockery\MockInterface */
    private $mSubject;
    /** @var  CollectionFactory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory::class);
        $this->mSubQueryModifier = $this->_mock(Sub\QueryModifier::class);
        /** create object to test */
        $this->obj = new CollectionFactory(
            $this->mSubQueryModifier
        );
    }

    public function test_aroundGetReport()
    {
        /** === Test Data === */
        $REQUEST_NAME = \Praxigento\Core\Config::DS_SALES_ORDERS_GRID;
        $RESULT = $this->_mock(\Magento\Sales\Model\ResourceModel\Order\Grid\Collection::class);
        /** === Setup Mocks === */
        $mProceed = function ($dataIn) use ($REQUEST_NAME, $RESULT) {
            $this->assertEquals($REQUEST_NAME, $dataIn);
            return $RESULT;
        };
        // $this->_subQueryModifier->populateSelect($result);
        $this->mSubQueryModifier
            ->shouldReceive('populateSelect')->once()
            ->with($RESULT);
        // $this->_subQueryModifier->addFieldsMapping($result);
        $this->mSubQueryModifier
            ->shouldReceive('addFieldsMapping')->once()
            ->with($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->aroundGetReport(
            $this->mSubject,
            $mProceed,
            $REQUEST_NAME
        );
        $this->assertEquals($RESULT, $res);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(CollectionFactory::class, $this->obj);
    }

}