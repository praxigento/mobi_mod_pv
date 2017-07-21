<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Sale;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Pv\Data\Entity\Sale;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Service\Call
{
    /** @var \Mockery\MockInterface */
    private $mCallAccount;
    /** @var \Mockery\MockInterface */
    private $mCallOperation;
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
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mCallAccount = $this->_mock(\Praxigento\Accounting\Service\IAccount::class);
        $this->mCallOperation = $this->_mock(\Praxigento\Accounting\Service\IOperation::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Pv\Repo\IModule::class);
        $this->mRepoSale = $this->_mock(\Praxigento\Pv\Repo\Entity\Sale::class);
        $this->mRepoSaleItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Sale\Item::class);
        $this->mRepoStockItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Stock\Item::class);
        $this->mToolDate = $this->_mock(\Praxigento\Core\Tool\IDate::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManObj,
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
        $custId = 21;
        $orderId = 34;
        $operId = 1024;
        $pvTotal = 300;
        $accIdCust = 101;
        $accIdRepres = 202;
        /** === Setup Mocks === */
        // $sale = $this->_repoSale->getById($saleId);
        $this->mRepoSale
            ->shouldReceive('getById')->once()
            ->with($orderId)
            ->andReturn(new Sale([Sale::ATTR_TOTAL => $pvTotal]));
        // $customerId = $this->_repoMod->getSaleOrderCustomerId($saleId);
        $this->mRepoMod
            ->shouldReceive('getSaleOrderCustomerId')->once()
            ->with($orderId)
            ->andReturn($custId);
        // $respGetAccCust = $this->_callAccount->get($reqGetAccCust);
        $mRespGetAccCust = new \Praxigento\Accounting\Service\Account\Response\Get();
        $mRespGetAccCust->set([Account::ATTR_ID => $accIdCust]);
        $this->mCallAccount
            ->shouldReceive('get')->once()
            ->andReturn($mRespGetAccCust);
        // $respGetAccRepres = $this->_callAccount->getRepresentative($reqGetAccRepres);
        $mRespGetAccRepres = new \Praxigento\Accounting\Service\Account\Response\GetRepresentative();
        $mRespGetAccRepres->set([Account::ATTR_ID => $accIdRepres]);
        $this->mCallAccount
            ->shouldReceive('getRepresentative')->once()
            ->andReturn($mRespGetAccRepres);
        // $respAddOper = $this->_callOperation->add($reqAddOper);
        $mRespAddOper = new \Praxigento\Accounting\Service\Operation\Response\Add();
        $this->mCallOperation
            ->shouldReceive('add')->once()
            ->andReturn($mRespAddOper);
        // $operId = $respAddOper->getOperationId();
        $mRespAddOper->setOperationId($operId);
        /** === Call and asserts  === */
        $req = new Request\AccountPv();
        $req->setSaleOrderId($orderId);
        $res = $this->obj->accountPv($req);
        $this->assertTrue($res->isSucceed());
        $operId = $res->getOperationId();
        $this->assertEquals($operId, $operId);
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

    /**
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function test_save()
    {
        /** === Test Data === */
        $operId = 21;
        $datePaid = 'paid';
        $prodId = 32;
        $stockId = 4;
        $itemId = 64;
        $pv = 12.44;
        $qty = 4;
        $item = $this->_mock(\Praxigento\Pv\Service\Sale\Data\Item::class);
        $items = [$item];
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
        $item->shouldReceive('getProductId')->once()
            ->andReturn($prodId);
        // $stockId = $item->getStockId();
        $item->shouldReceive('getStockId')->once()
            ->andReturn($stockId);
        // $itemId = $item->getItemId();
        $item->shouldReceive('getItemId')->once()
            ->andReturn($itemId);
        // $pv = $this->_repoStockItem->getPvByProductAndStock($prodId, $stockId);
        $this->mRepoStockItem
            ->shouldReceive('getPvByProductAndStock')->once()
            ->with($prodId, $stockId)
            ->andReturn($pv);
        // $qty = $item->getQuantity();
        $item->shouldReceive('getQuantity')->once()
            ->andReturn($qty);
        // $this->_repoSaleItem->replace($eItem);
        $this->mRepoSaleItem
            ->shouldReceive('replace')->once();
        //
        // $this->_repoSale->replace($orderData);
        $this->mRepoSale
            ->shouldReceive('replace')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\Save();
        $req->setSaleOrderId($operId);
        $req->setSaleOrderDatePaid($datePaid);
        $req->setOrderItems($items);
        $resp = $this->obj->save($req);
        $this->assertTrue($resp->isSucceed());
    }
}