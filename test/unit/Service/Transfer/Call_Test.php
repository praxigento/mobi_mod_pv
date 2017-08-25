<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer;

use Praxigento\Accounting\Repo\Entity\Data\Account;
use Praxigento\Downline\Data\Entity\Customer;

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
    private $mRepoMod;
    /** @var \Mockery\MockInterface */
    private $mToolDate;
    /** @var  Call */
    private $obj;
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mToolDate = $this->_mock(\Praxigento\Core\Tool\IDate::class);
        $this->mCallAccount = $this->_mock(\Praxigento\Accounting\Service\IAccount::class);
        $this->mCallOperation = $this->_mock(\Praxigento\Accounting\Service\IOperation::class);
        $this->mRepoMod = $this->_mock(\Praxigento\Pv\Repo\IModule::class);
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $this->mLogger,
            $this->mManObj,
            $this->mToolDate,
            $this->mCallAccount,
            $this->mCallOperation,
            $this->mRepoMod
        ];
        /** create object to test */
        $this->obj = new Call(
            $this->mLogger,
            $this->mManObj,
            $this->mToolDate,
            $this->mCallAccount,
            $this->mCallOperation,
            $this->mRepoMod
        );
    }

    public function test_betweenCustomers()
    {
        /** === Test Data === */
        $custIdFrom = 123;
        $custIdTo = 321;
        $countryFrom = 'lv';
        $countryTo = 'ru';
        $pathTo = '/1/2/5/';
        $dateApplied = '2015-06-23 13:23:34';
        $value = '23.34';
        $accIdFrom = 43;
        $accIdTo = 65;
        $custDebit = new Customer([
            Customer::ATTR_CUSTOMER_ID => $custIdFrom,
            Customer::ATTR_COUNTRY_CODE => $countryFrom
        ]);
        $custCredit = new Customer([
            Customer::ATTR_CUSTOMER_ID => $custIdTo,
            Customer::ATTR_COUNTRY_CODE => $countryTo,
            Customer::ATTR_PATH => $pathTo
        ]);
        $accFrom = new Account([
            Account::ATTR_ID => $accIdFrom
        ]);
        $accTo = new Account([
            Account::ATTR_ID => $accIdTo
        ]);
        /** === Setup Mocks === */
        // $date = $this->_toolDate->getUtcNowForDb();
        $this->mToolDate
            ->shouldReceive('getUtcNowForDb')->once()
            ->andReturn($dateApplied);
        // $downDebit = $this->_repoMod->getDownlineCustomerById($custIdDebit);
        $this->mRepoMod
            ->shouldReceive('getDownlineCustomerById')->once()
            ->with($custIdFrom)
            ->andReturn($custDebit);
        // $downCredit = $this->_repoMod->getDownlineCustomerById($custIdCredit);
        $this->mRepoMod
            ->shouldReceive('getDownlineCustomerById')->once()
            ->with($custIdTo)
            ->andReturn($custCredit);
        // $respAccDebit = $this->_callAccount->get($reqAccGet);
        $this->mCallAccount
            ->shouldReceive('get')->once()
            ->andReturn($accFrom);
        // $respAccCredit = $this->_callAccount->get($reqAccGet);
        $this->mCallAccount
            ->shouldReceive('get')->once()
            ->andReturn($accTo);
        // $respAddOper = $this->_callOperation->add($reqAddOper);
        $mRespAddOper = new \Praxigento\Accounting\Service\Operation\Response\Add();
        $mRespAddOper->markSucceed();
        $this->mCallOperation
            ->shouldReceive('add')->once()
            ->andReturn($mRespAddOper);
        /** === Call and asserts  === */
        $req = new Request\BetweenCustomers();
        $req->setFromCustomerId($custIdFrom);
        $req->setToCustomerId($custIdTo);
        $req->setValue($value);
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
        /** === Test Data === */
        $custIdFrom = 123;
        $custIdTo = 321;
        $value = '23.34';
        $accFrom = new Account([
            Account::ATTR_CUST_ID => $custIdFrom
        ]);
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(Call::class . '[betweenCustomers]', $this->objArgs);
        /** === Setup Mocks === */
        // $respRepres = $this->_callAccount->getRepresentative($reqRepres);
        $this->mCallAccount
            ->shouldReceive('getRepresentative')->once()
            ->andReturn($accFrom);
        // $respBetween = $this->betweenCustomers($reqBetween);
        $mRespBetween = new Response\BetweenCustomers();
        $this->obj->shouldReceive('betweenCustomers')->once()
            ->andReturn($mRespBetween);
        // if ($respBetween->isSucceed()) {
        $mRespBetween->markSucceed();
        /** === Call and asserts  === */
        $req = new Request\CreditToCustomer();
        $req->setToCustomerId($custIdTo);
        $req->setValue($value);
        $res = $this->obj->creditToCustomer($req);
        $this->assertTrue($res->isSucceed());
        /* coverage for accessors */
        $req->getToCustomerId();
    }

    public function test_debitFromCustomer()
    {
        /** === Test Data === */
        $custIdFrom = 123;
        $custIdTo = 321;
        $value = '23.34';
        $accTo = new Account([
            Account::ATTR_CUST_ID => $custIdTo
        ]);
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(Call::class . '[betweenCustomers]', $this->objArgs);
        /** === Setup Mocks === */
        // $respRepres = $this->_callAccount->getRepresentative($reqRepres);
        $this->mCallAccount
            ->shouldReceive('getRepresentative')->once()
            ->andReturn($accTo);
        // $respBetween = $this->betweenCustomers($reqBetween);
        $mRespBetween = new Response\BetweenCustomers();
        $this->obj->shouldReceive('betweenCustomers')->once()
            ->andReturn($mRespBetween);
        // if ($respBetween->isSucceed()) {
        $mRespBetween->markSucceed();
        /** === Call and asserts  === */
        $req = new Request\DebitFromCustomer();
        $req->setFromCustomerId($custIdFrom);
        $req->setValue($value);
        $res = $this->obj->debitFromCustomer($req);
        $this->assertTrue($res->isSucceed());
        /* coverage for accessors */
        $req->getFromCustomerId();
    }

}