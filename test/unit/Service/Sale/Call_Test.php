<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Sale;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Pv\Data\Entity\Sale;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
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
    /** @var \Mockery\MockInterface */
    private $mRepoStockItem;
    /** @var \Mockery\MockInterface */
    private $mToolDate;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mCallAccount = $this->_mock(\Praxigento\Accounting\Service\IAccount::class);
        $this->mCallOperation = $this->_mock(\Praxigento\Accounting\Service\IOperation::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Pv\Repo\IModule::class);
        $this->mRepoSale = $this->_mock(\Praxigento\Pv\Repo\Entity\ISale::class);
        $this->mRepoSaleItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Sale\IItem::class);
        $this->mRepoStockItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Stock\IItem::class);
        $this->mToolDate = $this->_mock(\Praxigento\Core\Tool\IDate::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManTrans,
            $this->mCallAccount,
            $this->mCallOperation,
            $this->mRepoMod,
            $this->mRepoSale,
            $this->mRepoSaleItem,
            $this->mRepoStockItem,
            $this->mToolDate
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
        $ORDER_ID = 21;
        $DATE_PAID = 'paid';
        $DATE_UTC = 'utc date';
        $PROD_ID = 32;
        $STOCK_ID = 4;
        $ITEM_ID = 64;
        $PV = 12.44;
        $QTY = 4;
        $ITEM = $this->_mock(\Praxigento\Pv\Service\Sale\Data\Item::class);
        $ITEMS = [$ITEM];
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        //
        // FIRST ITERATION
        //
        // $prodId = $item->getProductId();
        $ITEM->shouldReceive('getProductId')->once()
            ->andReturn($PROD_ID);
        // $stockId = $item->getStockId();
        $ITEM->shouldReceive('getStockId')->once()
            ->andReturn($STOCK_ID);
        // $itemId = $item->getItemId();
        $ITEM->shouldReceive('getItemId')->once()
            ->andReturn($ITEM_ID);
        // $pv = $this->_repoStockItem->getPvByProductAndStock($prodId, $stockId);
        $this->mRepoStockItem
            ->shouldReceive('getPvByProductAndStock')->once()
            ->with($PROD_ID, $STOCK_ID)
            ->andReturn($PV);
        // $qty = $item->getQuantity();
        $ITEM->shouldReceive('getQuantity')->once()
            ->andReturn($QTY);
        // $this->_repoSaleItem->replace($eItem);
        $this->mRepoSaleItem
            ->shouldReceive('replace')->once();
        //
        // $this->_repoSale->replace($orderData);
        $this->mRepoSale
            ->shouldReceive('replace')->once();
        // $this->_repoSaleItem->replace($one);
        $this->mRepoSaleItem
            ->shouldReceive('replace')->twice();
        // $datePaid = $this->_toolDate->getUtcNowForDb();
        $this->mToolDate
            ->shouldReceive('getUtcNowForDb')->once()
            ->andReturn($DATE_UTC);
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\Save();
        $req->setSaleOrderId($ORDER_ID);
        $req->setSaleOrderDatePaid($DATE_PAID);
        $req->setOrderItems($ITEMS);
        $resp = $this->obj->save($req);
        $this->assertTrue($resp->isSucceed());
    }
}