<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Transfer;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Lib\Service\Account\Request\Get as AccountGetRequest;
use Praxigento\Accounting\Lib\Service\Account\Request\GetRepresentative as AccountGetRepresentativeRequest;
use Praxigento\Accounting\Lib\Service\Operation\Request\Add as OperationAddRequest;
use Praxigento\Downline\Data\Entity\Customer as DownlineCustomer;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Lib\Service\ITransfer;

class Call extends \Praxigento\Core\Service\Base\Call implements ITransfer
{
    /**
     * Database sub functions for this service.
     *
     * @var Sub\Db
     */
    protected $_subDb;
    /**
     * @var \Praxigento\Accounting\Lib\Service\IAccount
     */
    protected $_callAccount;
    /** @var  \Praxigento\Accounting\Lib\Service\IOperation */
    protected $_callOperation;
    /** @var  \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Accounting\Lib\Service\IAccount $repoAccount,
        \Praxigento\Accounting\Lib\Service\IOperation $callOperation,
        Sub\Db $subDb
    ) {
        parent::__construct($logger);
        $this->_toolDate = $toolDate;
        $this->_callAccount = $repoAccount;
        $this->_callOperation = $callOperation;
        $this->_subDb = $subDb;
    }


    public function betweenCustomers(Request\BetweenCustomers $request)
    {
        $result = new Response\BetweenCustomers();
        $toolDate = $this->_toolDate;
        /* constraints validation results */
        $isCountriesTheSame = false;
        $isTargetPlacedInTheDownline = false;
        /* extract input parameters */
        $custIdDebit = $request->getData(Request\BetweenCustomers::FROM_CUSTOMER_ID);
        $custIdCredit = $request->getData(Request\BetweenCustomers::TO_CUSTOMER_ID);
        $date = $request->getData(Request\BetweenCustomers::DATE_APPLIED);
        $value = $request->getData(Request\BetweenCustomers::VALUE);
        $condForceAll = $request->getData(Request\BetweenCustomers::COND_FORCE_ALL);
        $condForceCountry = (boolean)$request->getData(Request\BetweenCustomers::COND_FORCE_COUNTRY);
        $condForceDownline = (boolean)$request->getData(Request\BetweenCustomers::COND_FORCE_DOWNLINE);
        if (is_null($date)) {
            $date = $toolDate->getUtcNowForDb();
        }
        /* validate conditions */
        if (!$condForceAll) {
            /* validate customer countries */
            $downDebit = $this->_subDb->getDownlineCustomer($custIdDebit);
            $downCredit = $this->_subDb->getDownlineCustomer($custIdCredit);
            /* countries should be equals */
            if (
                ($downDebit[DownlineCustomer::ATTR_COUNTRY_CODE] == $downCredit[DownlineCustomer::ATTR_COUNTRY_CODE]) ||
                $condForceCountry
            ) {
                $isCountriesTheSame = true;
            }
            /* transfer is allowed to own subtree only */
            $path = $downCredit[DownlineCustomer::ATTR_PATH];
            $key = Cfg::DTPS . $downDebit[DownlineCustomer::ATTR_CUSTOMER_ID] . Cfg::DTPS;
            if (
                (strpos($path, $key) !== false) ||
                $condForceDownline
            ) {
                $isTargetPlacedInTheDownline = true;
            }
        }
        /* check validation results and perform transfer */
        if (
            $condForceAll ||
            ($isTargetPlacedInTheDownline && $isCountriesTheSame)
        ) {
            /* get PV-accounts */
            $reqAccGet = new AccountGetRequest();
            $reqAccGet->setCustomerId($custIdDebit);
            $reqAccGet->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
            $reqAccGet->setCreateNewAccountIfMissed(true);
            $respAccDebit = $this->_callAccount->get($reqAccGet);
            $reqAccGet->setCustomerId($custIdCredit);
            $respAccCredit = $this->_callAccount->get($reqAccGet);
            /* add transfer operation */
            $reqAddOper = new OperationAddRequest();
            $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_PV_TRANSFER);
            $reqAddOper->setDatePerformed($date);
            $reqAddOper->setTransactions([
                [
                    Transaction::ATTR_DEBIT_ACC_ID => $respAccDebit->getData(Account::ATTR_ID),
                    Transaction::ATTR_CREDIT_ACC_ID => $respAccCredit->getData(Account::ATTR_ID),
                    Transaction::ATTR_VALUE => $value
                ]
            ]);
            $respAddOper = $this->_callOperation->add($reqAddOper);
            if ($respAddOper->isSucceed()) {
                $result->setAsSucceed();
            }
        }
        return $result;
    }

    /**
     * Reset cached data.
     */
    public function cacheReset()
    {
        $this->_callAccount->cacheReset();
    }

    public function creditToCustomer(Request\CreditToCustomer $request)
    {
        $result = new Response\CreditToCustomer();
        /* get representative customer account for PV asset */
        $reqRepres = new AccountGetRepresentativeRequest();
        $reqRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $respRepres = $this->_callAccount->getRepresentative($reqRepres);
        /* extract input parameters */
        $requestData = $request->getData();
        $requestData[Request\BetweenCustomers::FROM_CUSTOMER_ID] = $respRepres->getData(Account::ATTR_CUST_ID);
        $requestData[Request\BetweenCustomers::COND_FORCE_ALL] = true;
        $reqBetween = new Request\BetweenCustomers($requestData);
        $respBetween = $this->betweenCustomers($reqBetween);
        if ($respBetween->isSucceed()) {
            $result->setAsSucceed();
        }
        return $result;
    }

    public function debitFromCustomer(Request\DebitFromCustomer $request)
    {
        $result = new Response\DebitFromCustomer();
        /* get representative customer account for PV asset */
        $reqRepres = new AccountGetRepresentativeRequest();
        $reqRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $respRepres = $this->_callAccount->getRepresentative($reqRepres);
        /* extract input parameters */
        $requestData = $request->getData();
        $requestData[Request\BetweenCustomers::TO_CUSTOMER_ID] = $respRepres->getData(Account::ATTR_CUST_ID);
        $requestData[Request\BetweenCustomers::COND_FORCE_ALL] = true;
        $reqBetween = new Request\BetweenCustomers($requestData);
        $respBetween = $this->betweenCustomers($reqBetween);
        if ($respBetween->isSucceed()) {
            $result->setAsSucceed();
        }
        return $result;
    }
}