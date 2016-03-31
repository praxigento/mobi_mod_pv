<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Sale;

use Praxigento\Accounting\Lib\Entity\Account;
use Praxigento\Accounting\Lib\Service\Account\Response\Get as AccountGetResponse;
use Praxigento\Accounting\Lib\Service\Account\Response\GetRepresentative as AccountGetRepresentativeResponse;
use Praxigento\Accounting\Lib\Service\Operation\Response\Add as OperationAddResponse;
use Praxigento\Core\Lib\Service\Repo\Response\GetEntityByPk as GetEntityByPkResponse;
use Praxigento\Core\Lib\Service\Repo\Response\ReplaceEntity as ReplaceEntityResponse;
use Praxigento\Pv\Config;
use Praxigento\Pv\Lib\Entity\Sale;
use Praxigento\Pv\Lib\Entity\Sale\Item as SaleItem;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {
    private $DATA = [
        Sale::ATTR_SALE_ID       => 1,
        Sale::ATTR_SUBTOTAL      => 500,
        Sale::ATTR_DISCOUNT      => 50,
        Sale::ATTR_TOTAL         => 450,
        Request\Save::DATA_ITEMS => [
            1 => [
                SaleItem::ATTR_SALE_ITEM_ID => 1,
                Sale::ATTR_SUBTOTAL         => 250,
                Sale::ATTR_DISCOUNT         => 50,
                Sale::ATTR_TOTAL            => 200,
            ],
            2 => [
                SaleItem::ATTR_SALE_ITEM_ID => 2,
                Sale::ATTR_SUBTOTAL         => 250,
                Sale::ATTR_DISCOUNT         => 0,
                Sale::ATTR_TOTAL            => 250,
            ]
        ]
    ];

    public function test_accountPv() {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $ORDER_ID = 34;
        $OPER_ID = 1024;
        $PV_TOTAL = 300;
        $ACC_ID_CUST = 101;
        $ACC_ID_REPRES = 202;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\IAccount');
        $mCallOperation = $this->_mockFor('Praxigento\Accounting\Lib\Service\IOperation');

        // $respGetSalePv = $this->_callRepo->getEntityByPk($reqGetSalePv);
        $mSalePv = new GetEntityByPkResponse();
        $mSalePv->setData(Sale::ATTR_TOTAL, $PV_TOTAL);
        $mCallRepo
            ->expects($this->once())
            ->method('getEntityByPk')
            ->willReturn($mSalePv);
        // $respGetAccCust = $this->_callAccount->get($reqGetAccCust);
        $mGetAccCust = new AccountGetResponse();
        $mGetAccCust->setData(Account::ATTR_ID, $ACC_ID_CUST);
        $mCallAccount
            ->expects($this->once())
            ->method('get')
            ->willReturn($mGetAccCust);
        // $respGetAccRepres = $this->_callAccount->getRepresentative($reqGetAccRepres);
        $mGetAccRepres = new AccountGetRepresentativeResponse();
        $mGetAccRepres->setData(Account::ATTR_ID, $ACC_ID_REPRES);
        $mCallAccount
            ->expects($this->once())
            ->method('getRepresentative')
            ->willReturn($mGetAccRepres);
        // $respAddOper = $this->_callOperation->add($reqAddOper);
        $mRespAddOper = new OperationAddResponse();
        $mRespAddOper->setOperationId($OPER_ID);
        $mRespAddOper->setAsSucceed();
        $mCallOperation
            ->expects($this->once())
            ->method('add')
            ->willReturn($mRespAddOper);
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallAccount, $mCallOperation);
        $req = new Request\AccountPv();
        $req->setCustomerId($CUSTOMER_ID);
        $req->setSaleOrderId($ORDER_ID);
        $resp = $call->accountPv($req);
        $this->assertTrue($resp->isSucceed());
        $operId = $resp->getData(Response\AccountPv::OPERATION_ID);
        $this->assertEquals($OPER_ID, $operId);
    }

    public function test_accountPv_withoutCustomerId() {
        /** === Test Data === */
        $CUSTOMER_ID = 21;
        $ORDER_ID = 34;
        $OPER_ID = 1024;
        $PV_TOTAL = 300;
        $ACC_ID_CUST = 101;
        $ACC_ID_REPRES = 202;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\IAccount');
        $mCallOperation = $this->_mockFor('Praxigento\Accounting\Lib\Service\IOperation');

        // $respGetSalePv = $this->_callRepo->getEntityByPk($reqGetSalePv);
        $mSalePv = new GetEntityByPkResponse();
        $mSalePv->setData(Sale::ATTR_TOTAL, $PV_TOTAL);
        $mCallRepo
            ->expects($this->at(0))
            ->method('getEntityByPk')
            ->willReturn($mSalePv);
        // $respGetSaleOrder = $this->_callRepo->getEntityByPk($reqGetSaleOrder);
        $mSaleOrder = new GetEntityByPkResponse();
        $mSaleOrder->setData(Config::E_SALE_ORDER_A_CUSTOMER_ID, $CUSTOMER_ID);
        $mSaleOrder->setAsSucceed();
        $mCallRepo
            ->expects($this->at(1))
            ->method('getEntityByPk')
            ->willReturn($mSaleOrder);
        // $respGetAccCust = $this->_callAccount->get($reqGetAccCust);
        $mGetAccCust = new AccountGetResponse();
        $mGetAccCust->setData(Account::ATTR_ID, $ACC_ID_CUST);
        $mCallAccount
            ->expects($this->once())
            ->method('get')
            ->willReturn($mGetAccCust);
        // $respGetAccRepres = $this->_callAccount->getRepresentative($reqGetAccRepres);
        $mGetAccRepres = new AccountGetRepresentativeResponse();
        $mGetAccRepres->setData(Account::ATTR_ID, $ACC_ID_REPRES);
        $mCallAccount
            ->expects($this->once())
            ->method('getRepresentative')
            ->willReturn($mGetAccRepres);
        // $respAddOper = $this->_callOperation->add($reqAddOper);
        $mRespAddOper = new OperationAddResponse();
        $mRespAddOper->setOperationId($OPER_ID);
        $mRespAddOper->setAsSucceed();
        $mCallOperation
            ->expects($this->once())
            ->method('add')
            ->willReturn($mRespAddOper);
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallAccount, $mCallOperation);
        $req = new Request\AccountPv();
        $req->setSaleOrderId($ORDER_ID);
        $resp = $call->accountPv($req);
        $this->assertTrue($resp->isSucceed());
        $operId = $resp->getData(Response\AccountPv::OPERATION_ID);
        $this->assertEquals($OPER_ID, $operId);
    }

    public function test_save() {
        /** === Test Data === */
        $data = $this->DATA;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\IAccount');
        $mCallOperation = $this->_mockFor('Praxigento\Accounting\Lib\Service\IOperation');

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respReplace = $this->_callRepo->replaceEntity($reqReplace);
        $mRespReplace = new ReplaceEntityResponse();
        $mRespReplace->setAsSucceed();
        $mCallRepo
            ->expects($this->any())
            ->method('replaceEntity')
            ->willReturn($mRespReplace);
        // $this->_conn->commit();
        $mConn
            ->expects($this->once())
            ->method('commit');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallAccount, $mCallOperation);
        $req = new Request\Save();
        $req->setData($data);
        $resp = $call->save($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_save_exception() {
        /** === Test Data === */
        $data = $this->DATA;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\IAccount');
        $mCallOperation = $this->_mockFor('Praxigento\Accounting\Lib\Service\IOperation');

        // $this->_conn->beginTransaction();
        $mConn
            ->expects($this->once())
            ->method('beginTransaction');
        // $respReplace = $this->_callRepo->replaceEntity($reqReplace);
        $mRespReplaceOrder = new ReplaceEntityResponse();
        $mRespReplaceOrder->setAsSucceed();
        $mCallRepo
            ->expects($this->at(0))
            ->method('replaceEntity')
            ->willReturn($mRespReplaceOrder);
        // return 'flase' on second replace
        $mCallRepo
            ->expects($this->at(1))
            ->method('replaceEntity')
            ->willReturn(new ReplaceEntityResponse());
        // $this->_conn->commit();
        $mConn
            ->expects($this->once())
            ->method('rollBack');
        /**
         * Prepare request and perform call.
         */
        /** === Test itself === */
        /** @var  $call Call */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallAccount, $mCallOperation);
        $req = new Request\Save();
        $req->setData($data);
        $resp = $call->save($req);
        $this->assertFalse($resp->isSucceed());
    }


    public function test_cacheReset() {
        /** === Test Data === */
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\IAccount');
        $mCallOperation = $this->_mockFor('Praxigento\Accounting\Lib\Service\IOperation');

        /**
         * Prepare request and perform call.
         */
        /** @var  $sub Db */
        $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallAccount, $mCallOperation);
        $call->cacheReset();
    }
}