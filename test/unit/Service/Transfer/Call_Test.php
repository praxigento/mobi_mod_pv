<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Downline\Data\Entity\Customer;
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
    private $mRepoMod;
    /** @var \Mockery\MockInterface */
    private $mToolDate;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mToolDate = $this->_mock(\Praxigento\Core\Tool\IDate::class);
        $this->mCallAccount = $this->_mock(\Praxigento\Accounting\Service\IAccount::class);
        $this->mCallOperation = $this->_mock(\Praxigento\Accounting\Service\IOperation::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Pv\Repo\IModule::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mToolDate,
            $this->mCallAccount,
            $this->mCallOperation,
            $this->mRepoMod
        );
    }

    public function test_betweenCustomers()
    {
        /** === Test Data === */
        $CUSTOMER_ID_FROM = 123;
        $CUSTOMER_ID_TO = 321;
        $COUNTRY_FROM = 'lv';
        $COUNTRY_TO = 'ru';
        $PATH_TO = '/1/2/5/';
        $DATE_APPLIED = '2015-06-23 13:23:34';
        $VALUE = '23.34';
        $ACC_ID_FROM = 43;
        $ACC_ID_TO = 65;
        $CUSTOMER_DEBIT = new Customer([
            Customer::ATTR_CUSTOMER_ID => $CUSTOMER_ID_FROM,
            Customer::ATTR_COUNTRY_CODE => $COUNTRY_FROM
        ]);
        $CUSTOMER_CREDIT = new Customer([
            Customer::ATTR_CUSTOMER_ID => $CUSTOMER_ID_TO,
            Customer::ATTR_COUNTRY_CODE => $COUNTRY_TO,
            Customer::ATTR_PATH => $PATH_TO
        ]);
        $ACCOUNT_FROM = new Account([
            Account::ATTR_ID => $ACC_ID_FROM
        ]);
        $ACCOUNT_TO = new Account([
            Account::ATTR_ID => $ACC_ID_TO
        ]);
        /** === Setup Mocks === */
        // $date = $this->_toolDate->getUtcNowForDb();
        $this->mToolDate
            ->shouldReceive('getUtcNowForDb')->once()
            ->andReturn($DATE_APPLIED);
        // $downDebit = $this->_repoMod->getDownlineCustomerById($custIdDebit);
        $this->mRepoMod
            ->shouldReceive('getDownlineCustomerById')->once()
            ->with($CUSTOMER_ID_FROM)
            ->andReturn($CUSTOMER_DEBIT);
        // $downCredit = $this->_repoMod->getDownlineCustomerById($custIdCredit);
        $this->mRepoMod
            ->shouldReceive('getDownlineCustomerById')->once()
            ->with($CUSTOMER_ID_TO)
            ->andReturn($CUSTOMER_CREDIT);
        // $respAccDebit = $this->_callAccount->get($reqAccGet);
        $this->mCallAccount
            ->shouldReceive('get')->once()
            ->andReturn($ACCOUNT_FROM);
        // $respAccCredit = $this->_callAccount->get($reqAccGet);
        $this->mCallAccount
            ->shouldReceive('get')->once()
            ->andReturn($ACCOUNT_TO);
        // $respAddOper = $this->_callOperation->add($reqAddOper);
        $mRespAddOper = new \Praxigento\Accounting\Service\Operation\Response\Add();
        $mRespAddOper->markSucceed();
        $this->mCallOperation
            ->shouldReceive('add')->once()
            ->andReturn($mRespAddOper);
        /** === Call and asserts  === */
        $req = new Request\BetweenCustomers();
        $req->setFromCustomerId($CUSTOMER_ID_FROM);
        $req->setToCustomerId($CUSTOMER_ID_TO);
        $req->setValue($VALUE);
        $req->setConditionForceCountry(true);
        $req->setConditionForceDownline(true);
        $resp = $this->obj->betweenCustomers($req);
        $this->assertTrue($resp->isSucceed());
        /* code coverage */
        $req->setConditionForceAll(true);
        $req->setDateApplied(null);
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

    public function test_creditToCustomer()
    {
        /* partially mocked object */
        $obj = \Mockery::mock(
            Call::class . '[betweenCustomers]',
            [
                $this->mLogger,
                $this->mToolDate,
                $this->mCallAccount,
                $this->mCallOperation,
                $this->mRepoMod
            ]
        );
        /** === Test Data === */
        $CUSTOMER_ID_FROM = 123;
        $CUSTOMER_ID_TO = 321;
        $VALUE = '23.34';
        $ACCOUNT_FROM = new Account([
            Account::ATTR_CUST_ID => $CUSTOMER_ID_FROM
        ]);
        /** === Setup Mocks === */
        // $respRepres = $this->_callAccount->getRepresentative($reqRepres);
        $this->mCallAccount
            ->shouldReceive('getRepresentative')->once()
            ->andReturn($ACCOUNT_FROM);
        // $respBetween = $this->betweenCustomers($reqBetween);
        $mRespBetween = new Response\BetweenCustomers();
        $obj->shouldReceive('betweenCustomers')->once()
            ->andReturn($mRespBetween);
        // if ($respBetween->isSucceed()) {
        $mRespBetween->markSucceed();
        /** === Call and asserts  === */
        $req = new Request\CreditToCustomer();
        $req->setToCustomerId($CUSTOMER_ID_TO);
        $req->setValue($VALUE);
        $res = $obj->creditToCustomer($req);
        $this->assertTrue($res->isSucceed());
        /* coverage for accessors */
        $req->getToCustomerId();
    }

    public function test_debitFromCustomer()
    {
        /* partially mocked object */
        $obj = \Mockery::mock(
            Call::class . '[betweenCustomers]',
            [
                $this->mLogger,
                $this->mToolDate,
                $this->mCallAccount,
                $this->mCallOperation,
                $this->mRepoMod
            ]
        );
        /** === Test Data === */
        $CUSTOMER_ID_FROM = 123;
        $CUSTOMER_ID_TO = 321;
        $VALUE = '23.34';
        $ACCOUNT_TO = new Account([
            Account::ATTR_CUST_ID => $CUSTOMER_ID_TO
        ]);
        /** === Setup Mocks === */
        // $respRepres = $this->_callAccount->getRepresentative($reqRepres);
        $this->mCallAccount
            ->shouldReceive('getRepresentative')->once()
            ->andReturn($ACCOUNT_TO);
        // $respBetween = $this->betweenCustomers($reqBetween);
        $mRespBetween = new Response\BetweenCustomers();
        $obj->shouldReceive('betweenCustomers')->once()
            ->andReturn($mRespBetween);
        // if ($respBetween->isSucceed()) {
        $mRespBetween->markSucceed();
        /** === Call and asserts  === */
        $req = new Request\DebitFromCustomer();
        $req->setFromCustomerId($CUSTOMER_ID_FROM);
        $req->setValue($VALUE);
        $res = $obj->debitFromCustomer($req);
        $this->assertTrue($res->isSucceed());
        /* coverage for accessors */
        $req->getFromCustomerId();
    }


}