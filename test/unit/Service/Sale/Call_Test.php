<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Sale;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Pv\Data\Entity\Sale;
use Praxigento\Pv\Data\Entity\Sale\Item as SaleItem;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    private $DATA = [
        Sale::ATTR_SALE_ID => 1,
        Sale::ATTR_SUBTOTAL => 500,
        Sale::ATTR_DISCOUNT => 50,
        Sale::ATTR_TOTAL => 450,
        Request\Save::DATA_ITEMS => [
            1 => [
                SaleItem::ATTR_SALE_ITEM_ID => 1,
                Sale::ATTR_SUBTOTAL => 250,
                Sale::ATTR_DISCOUNT => 50,
                Sale::ATTR_TOTAL => 200,
            ],
            2 => [
                SaleItem::ATTR_SALE_ITEM_ID => 2,
                Sale::ATTR_SUBTOTAL => 250,
                Sale::ATTR_DISCOUNT => 0,
                Sale::ATTR_TOTAL => 250,
            ]
        ]
    ];
    /** @var \Mockery\MockInterface */
    private $mCallAccount;
    /** @var \Mockery\MockInterface */
    private $mCallOperation;
    /** @var \Mockery\MockInterface */
    private $mLogger;
    /** @var \Mockery\MockInterface */
    private $mManTrans;
    /** @var \Mockery\MockInterface */
    private $mRepoMod;
    /** @var \Mockery\MockInterface */
    private $mRepoSale;
    /** @var \Mockery\MockInterface */
    private $mRepoSaleItem;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mManTrans = $this->_mockTransactionManager();
        /* TODO: remove generic repo */
        $mRepoGeneric = $this->_mockRepoGeneric();
        $this->mCallAccount = $this->_mock(\Praxigento\Accounting\Service\IAccount::class);
        $this->mCallOperation = $this->_mock(\Praxigento\Accounting\Service\IOperation::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Pv\Repo\IModule::class);
        $this->mRepoSale = $this->_mock(\Praxigento\Pv\Repo\Entity\ISale::class);
        $this->mRepoSaleItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Sale\IItem::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManTrans,
            $mRepoGeneric,
            $this->mCallAccount,
            $this->mCallOperation,
            $this->mRepoMod,
            $this->mRepoSale,
            $this->mRepoSaleItem
        );
    }

    public function test_accountPv()
    {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $ORDER_ID = 34;
        $OPER_ID = 1024;
        $PV_TOTAL = 300;
        $ACC_ID_CUST = 101;
        $ACC_ID_REPRES = 202;
        /** === Setup Mocks === */
        // $sale = $this->_repoSale->getById($saleId);
        $this->mRepoSale
            ->shouldReceive('getById')->once()
            ->with($ORDER_ID)
            ->andReturn(new Sale([Sale::ATTR_TOTAL => $PV_TOTAL]));
        // $customerId = $this->_repoMod->getSaleOrderCustomerId($saleId);
        $this->mRepoMod
            ->shouldReceive('getSaleOrderCustomerId')->once()
            ->with($ORDER_ID)
            ->andReturn($CUSTOMER_ID);
        // $respGetAccCust = $this->_callAccount->get($reqGetAccCust);
        $mRespGetAccCust = new \Praxigento\Accounting\Service\Account\Response\Get();
        $mRespGetAccCust->setData([Account::ATTR_ID => $ACC_ID_CUST]);
        $this->mCallAccount
            ->shouldReceive('get')->once()
            ->andReturn($mRespGetAccCust);
        // $respGetAccRepres = $this->_callAccount->getRepresentative($reqGetAccRepres);
        $mRespGetAccRepres = new \Praxigento\Accounting\Service\Account\Response\GetRepresentative();
        $mRespGetAccRepres->setData([Account::ATTR_ID => $ACC_ID_REPRES]);
        $this->mCallAccount
            ->shouldReceive('getRepresentative')->once()
            ->andReturn($mRespGetAccRepres);
        // $respAddOper = $this->_callOperation->add($reqAddOper);
        $mRespAddOper = new \Praxigento\Accounting\Service\Operation\Response\Add();
        $this->mCallOperation
            ->shouldReceive('add')->once()
            ->andReturn($mRespAddOper);
        // $operId = $respAddOper->getOperationId();
        $mRespAddOper->setOperationId($OPER_ID);
        /** === Call and asserts  === */
        $req = new Request\AccountPv();
        $req->setSaleOrderId($ORDER_ID);
        $res = $this->obj->accountPv($req);
        $this->assertTrue($res->isSucceed());
        $operId = $res->getOperationId();
        $this->assertEquals($OPER_ID, $operId);
    }

    public function test_cacheReset()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        // $this->_callAccount->cacheReset();
        $this->mCallAccount
            ->shouldReceive('cacheReset')->once();
        /** === Call and asserts  === */
        $this->obj->cacheReset();
    }

    public function test_save()
    {
        /** === Test Data === */
        $data = $this->DATA;
        /** === Setup Mocks === */
        // $trans = $this->_manTrans->transactionBegin();
        $mTrans = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('transactionBegin')->once()
            ->andReturn($mTrans);
        // $this->_repoSale->replace($orderData);
        $this->mRepoSale
            ->shouldReceive('replace')->once();
        // $this->_repoSaleItem->replace($one);
        $this->mRepoSaleItem
            ->shouldReceive('replace')->twice();
        // $this->_manTrans->transactionCommit($trans);
        $this->mManTrans
            ->shouldReceive('transactionCommit')->once();
        // $this->_manTrans->transactionClose($trans);
        $this->mManTrans
            ->shouldReceive('transactionClose')->once();
        /** === Call and asserts  === */
        $req = new Request\Save();
        $req->setData($data);
        $resp = $this->obj->save($req);
        $this->assertTrue($resp->isSucceed());
    }

}