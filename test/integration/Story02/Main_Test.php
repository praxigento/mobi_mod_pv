<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Test\Story02;

use Praxigento\Accounting\Api\Service\Account\Get\Request as RequestAccountGet;
use Praxigento\Core\Test\BaseIntegrationTest;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Service\Transfer\Request\BetweenCustomers as RequestTransferBetweenCustomers;
use Praxigento\Pv\Service\Transfer\Request\CreditToCustomer as RequestTransferCreditToCustomer;
use Praxigento\Pv\Service\Transfer\Request\DebitFromCustomer as RequestTransferDebitFromCustomer;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Main_IntegrationTest extends BaseIntegrationTest
{
    /** @var \Praxigento\Accounting\Api\Service\Account\Get */
    private $_callAccount;
    /** @var \Praxigento\Pv\Service\ITransfer */
    private $_callTransfer;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $_manTrans;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_manTrans = $this->_manObj->get(\Praxigento\Core\Api\App\Repo\Transaction\Manager::class);
        $this->_callTransfer = $this->_manObj->get(\Praxigento\Pv\Service\ITransfer::class);
        $this->_callAccount = $this->_manObj->get(\Praxigento\Accounting\Api\Service\Account\Get::class);
    }

    private function _checkAccount($custId, $value)
    {
        $req = new RequestAccountGet();
        $req->setCustomerId($custId);
        $req->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $resp = $this->_callAccount->exec($req);
        $this->assertTrue($resp->isSucceed());
        $actualValue = $resp->getBalance();
        $this->assertEquals($value, $actualValue);
    }

    private function _transferBetweenCustomers($custIdFrom, $custIdTo, $value)
    {
        $req = new RequestTransferBetweenCustomers();
        $req->setFromCustomerId($custIdFrom);
        $req->setToCustomerId($custIdTo);
        $req->setValue($value);
        $resp = $this->_callTransfer->betweenCustomers($req);
        $this->assertTrue($resp->isSucceed());
    }

    private function _transferFromCustomer($custId, $value)
    {
        $req = new RequestTransferDebitFromCustomer();
        $req->setConditionForceAll(true);
        $req->setFromCustomerId($custId);
        $req->setValue($value);
        $resp = $this->_callTransfer->debitFromCustomer($req);
        $this->assertTrue($resp->isSucceed());
    }

    private function _transferToCustomer($custId, $value)
    {
        $req = new RequestTransferCreditToCustomer();
        $req->setConditionForceAll(true);
        $req->setToCustomerId($custId);
        $req->setValue($value);
        $resp = $this->_callTransfer->creditToCustomer($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_main()
    {

        $this->_logger->debug('Story02 in PV Integration tests is started.');
        $def = $this->_manTrans->begin();
        try {
            $this->_createMageCustomers();
            $this->_createDownlineCustomers();
            $CUST_1 = $this->_mapCustomerMageIdByIndex[1];
            $CUST_2 = $this->_mapCustomerMageIdByIndex[2];
            $VAL_1 = 12.34;
            $VAL_2 = 6.54;
            $VAL_3 = 5.80;
            $VAL_4 = 6;
            $VAL_5 = 0.54;
            $this->_transferToCustomer($CUST_1, $VAL_1);
            $this->_checkAccount($CUST_1, $VAL_1);
            $this->_transferBetweenCustomers($CUST_1, $CUST_2, $VAL_2);
            $this->_checkAccount($CUST_1, $VAL_3);
            $this->_checkAccount($CUST_2, $VAL_2);
            $this->_transferFromCustomer($CUST_2, $VAL_4);
            $this->_checkAccount($CUST_2, $VAL_5);
        } finally {
            $this->_manTrans->rollback($def);

        }
        $this->_logger->debug('Story02 in PV Integration tests is completed, all transactions are rolled back.');
    }
}