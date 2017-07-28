<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Def;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');
use Praxigento\Pv\Config as Cfg;

class Module_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    protected $mRepoDownlineCustomer;
    /** @var  \Mockery\MockInterface */
    protected $mRepoGeneric;
    /** @var  Module */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        $this->mRepoDownlineCustomer = $this->_mock(\Praxigento\Downline\Repo\Entity\Def\Customer::class);
        /** create object to test */
        $this->obj = new Module(
            $this->mRepoGeneric,
            $this->mRepoDownlineCustomer
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Module::class, $this->obj);
    }

    public function test_getDownlineCustomerById()
    {
        /** === Test Data === */
        $ID = 1024;
        $DATA = 'data';
        /** === Setup Mocks === */
        // $result = $this->_repoDownlineCustomer->getById($id);
        $this->mRepoDownlineCustomer
            ->shouldReceive('getById')->once()
            ->with($ID)
            ->andReturn($DATA);
        /** === Call and asserts  === */
        $res = $this->obj->getDownlineCustomerById($ID);
        $this->assertEquals($DATA, $res);
    }

    public function test_getSaleOrderCustomerId()
    {
        /** === Test Data === */
        $ORDER_ID = 1024;
        $CUST_ID = 4321;
        $DATA = [Cfg::E_SALE_ORDER_A_CUSTOMER_ID => $CUST_ID];
        /** === Setup Mocks === */
        // $data = $this->_repoGeneric->getEntityByPk(...)
        $this->mRepoGeneric
            ->shouldReceive('getEntityByPk')->once()
            ->andReturn($DATA);
        /** === Call and asserts  === */
        $res = $this->obj->getSaleOrderCustomerId($ORDER_ID);
        $this->assertEquals($CUST_ID, $res);
    }

}