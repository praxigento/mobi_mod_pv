<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Transfer;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Lib\Service\Account\Request\Get as AccountGetResponse;
use Praxigento\Accounting\Lib\Service\Account\Response\GetRepresentative as AccountGetRepresentativeResponse;
use Praxigento\Accounting\Lib\Service\Operation\Response\Add as AccountingOperationAddResponse;
use Praxigento\Downline\Data\Entity\Customer as DownlineCustomer;
use Praxigento\Pv\Lib\Entity\Sale;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

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
        $mSubDb = $this->_mockFor('Praxigento\Pv\Lib\Service\Transfer\Sub\Db');

        /**
         * Prepare request and perform call.
         */
        /** @var  $sub Db */
        $call = new Call(
            $mLogger,
            $mDba,
            $mToolbox,
            $mCallRepo,
            $mCallAccount,
            $mCallOperation,
            $mSubDb
        );
        $call->cacheReset();
    }

    public function test_creditToCustomer() {
        /** === Test Data === */
        $CUSTOMER_ID = 123;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\IAccount');
        $mCallOperation = $this->_mockFor('Praxigento\Accounting\Lib\Service\IOperation');
        $mSubDb = $this->_mockFor('Praxigento\Pv\Lib\Service\Transfer\Sub\Db');

        $mCall = $this
            ->getMockBuilder('Praxigento\Pv\Lib\Service\Transfer\Call')
            ->setConstructorArgs([
                $mLogger,
                $mDba,
                $mToolbox,
                $mCallRepo,
                $mCallAccount,
                $mCallOperation,
                $mSubDb
            ])
            ->setMethods([ 'betweenCustomers' ])
            ->getMock();

        // $respRepres = $this->_callAccount->getRepresentative($reqRepres);
        $mRespGetRepres = new AccountGetRepresentativeResponse();
        $mRespGetRepres->setData(Account::ATTR_CUST_ID, $CUSTOMER_ID);
        $mCallAccount
            ->expects($this->once())
            ->method('getRepresentative')
            ->willReturn($mRespGetRepres);
        // $respBetween = $this->betweenCustomers($reqBetween);
        $mRespBetween = new Response\BetweenCustomers();
        $mRespBetween->setAsSucceed();
        $mCall
            ->expects($this->once())
            ->method('betweenCustomers')
            ->willReturn($mRespBetween);
        /**
         * Prepare request and perform call.
         */
        /** @var  $sub Db */
        // $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallAccount, $mCallOperation);
        $call = $mCall;
        $req = new Request\CreditToCustomer();
        $resp = $call->creditToCustomer($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_debitFromCustomer() {
        /** === Test Data === */
        $CUSTOMER_ID = 123;
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\IAccount');
        $mCallOperation = $this->_mockFor('Praxigento\Accounting\Lib\Service\IOperation');
        $mSubDb = $this->_mockFor('Praxigento\Pv\Lib\Service\Transfer\Sub\Db');

        $mCall = $this
            ->getMockBuilder('Praxigento\Pv\Lib\Service\Transfer\Call')
            ->setConstructorArgs([
                $mLogger,
                $mDba,
                $mToolbox,
                $mCallRepo,
                $mCallAccount,
                $mCallOperation,
                $mSubDb
            ])
            ->setMethods([ 'betweenCustomers' ])
            ->getMock();

        // $respRepres = $this->_callAccount->getRepresentative($reqRepres);
        $mRespGetRepres = new AccountGetRepresentativeResponse();
        $mRespGetRepres->setData(Account::ATTR_CUST_ID, $CUSTOMER_ID);
        $mCallAccount
            ->expects($this->once())
            ->method('getRepresentative')
            ->willReturn($mRespGetRepres);
        // $respBetween = $this->betweenCustomers($reqBetween);
        $mRespBetween = new Response\BetweenCustomers();
        $mRespBetween->setAsSucceed();
        $mCall
            ->expects($this->once())
            ->method('betweenCustomers')
            ->willReturn($mRespBetween);
        /**
         * Prepare request and perform call.
         */
        /** @var  $sub Db */
        // $call = new Call($mLogger, $mDba, $mToolbox, $mCallRepo, $mCallAccount, $mCallOperation);
        $call = $mCall;
        $req = new Request\DebitFromCustomer();
        $resp = $call->debitFromCustomer($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_betweenCustomers() {
        /** === Test Data === */
        $CUSTOMER_ID_FROM = 123;
        $CUSTOMER_ID_TO = 321;
        $COUNTRY_FROM = 'lv';
        $COUNTRY_TO = 'ru';
        $PATH_FROM = '/1/2/3/';
        $PATH_TO = '/1/2/5/';
        $DATE_APPLIED = '2015-06-23 13:23:34';
        $VALUE = '23.34';
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolDate = $this->_mockFor('Praxigento\Core\Lib\Tool\Date');
        $mToolbox = $this->_mockToolbox(null, $mToolDate);
        $mCallRepo = $this->_mockCallRepo();
        $mCallAccount = $this->_mockFor('Praxigento\Accounting\Lib\Service\IAccount');
        $mCallOperation = $this->_mockFor('Praxigento\Accounting\Lib\Service\IOperation');
        $mSubDb = $this->_mockFor('Praxigento\Pv\Lib\Service\Transfer\Sub\Db');

        // $dtNow = $toolDate->getUtcNowForDb();
        $mToolDate
            ->expects($this->once())
            ->method('getUtcNowForDb')
            ->willReturn($DATE_APPLIED);
        // $downDebit = $this->_subDb->getDownlineCustomer($custIdDebit);
        $mSubDb
            ->expects($this->at(0))
            ->method('getDownlineCustomer')
            ->with($CUSTOMER_ID_FROM)
            ->willReturn([
                DownlineCustomer::ATTR_CUSTOMER_ID  => $CUSTOMER_ID_FROM,
                DownlineCustomer::ATTR_COUNTRY_CODE => $COUNTRY_FROM,
                DownlineCustomer::ATTR_PATH         => $PATH_FROM
            ]);
        // $downCredit = $this->_subDb->getDownlineCustomer($custIdCredit);
        $mSubDb
            ->expects($this->at(1))
            ->method('getDownlineCustomer')
            ->with($CUSTOMER_ID_TO)
            ->willReturn([
                DownlineCustomer::ATTR_CUSTOMER_ID  => $CUSTOMER_ID_TO,
                DownlineCustomer::ATTR_COUNTRY_CODE => $COUNTRY_TO,
                DownlineCustomer::ATTR_PATH         => $PATH_TO
            ]);
        // $respAccDebit = $this->_callAccount->get($reqAccGet);
        $mRespGetDebit = new AccountGetResponse();
        $mCallAccount
            ->expects($this->at(0))
            ->method('get')
            ->willReturn($mRespGetDebit);
        // $respAccCredit = $this->_callAccount->get($reqAccGet);
        $mRespGetCredit = new AccountGetResponse();
        $mCallAccount
            ->expects($this->at(1))
            ->method('get')
            ->willReturn($mRespGetCredit);
        //  $respAddOper = $this->_callOperation->add($reqAddOper);
        $mRespAddOper = new AccountingOperationAddResponse();
        $mRespAddOper->setAsSucceed();
        $mCallOperation
            ->expects($this->once())
            ->method('add')
            ->willReturn($mRespAddOper);
        /**
         * Prepare request and perform call.
         */
        /** @var  $sub Db */
        $call = new Call(
            $mLogger,
            $mDba,
            $mToolbox,
            $mCallRepo,
            $mCallAccount,
            $mCallOperation,
            $mSubDb
        );
        $req = new Request\BetweenCustomers();
        $req->setData(Request\BetweenCustomers::FROM_CUSTOMER_ID, $CUSTOMER_ID_FROM);
        $req->setData(Request\BetweenCustomers::TO_CUSTOMER_ID, $CUSTOMER_ID_TO);
        $req->setData(Request\BetweenCustomers::VALUE, $VALUE);
        $req->setData(Request\BetweenCustomers::COND_FORCE_COUNTRY, true);
        $req->setData(Request\BetweenCustomers::COND_FORCE_DOWNLINE, true);
        $resp = $call->betweenCustomers($req);
        $this->assertTrue($resp->isSucceed());
    }
}