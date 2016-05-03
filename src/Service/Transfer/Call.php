<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Service\Account\Request\Get as AccountGetRequest;
use Praxigento\Accounting\Service\Account\Request\GetRepresentative as AccountGetRepresentativeRequest;
use Praxigento\Accounting\Service\Operation\Request\Add as OperationAddRequest;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Service\ITransfer;

class Call extends \Praxigento\Core\Service\Base\Call implements ITransfer
{
    /**
     * @var \Praxigento\Accounting\Service\IAccount
     */
    protected $_callAccount;
    /** @var  \Praxigento\Accounting\Service\IOperation */
    protected $_callOperation;
    /**
     * Other module's repositories adapter.
     *
     * @var \Praxigento\Pv\Repo\IModule
     */
    protected $_repoMod;
    /** @var  \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Tool\IDate $toolDate,
        \Praxigento\Accounting\Service\IOperation $callOperation,
        \Praxigento\Pv\Repo\IModule $repoMod,
        \Praxigento\Accounting\Service\IAccount $repoAccount
    ) {
        parent::__construct($logger);
        $this->_toolDate = $toolDate;
        $this->_callOperation = $callOperation;
        $this->_repoMod = $repoMod;
        $this->_callAccount = $repoAccount;
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
            $downDebit = $this->_repoMod->getDownlineCustomerById($custIdDebit);
            $downCredit = $this->_repoMod->getDownlineCustomerById($custIdCredit);
            /* countries should be equals */
            $countryDebit = $downDebit->getCountryCode();
            $countryCredit = $downCredit->getCountryCode();
            if (
                ($countryDebit == $countryCredit) ||
                $condForceCountry
            ) {
                $isCountriesTheSame = true;
            }
            /* transfer is allowed to own subtree only */
            $path = $downCredit->getPath();
            $key = Cfg::DTPS . $downDebit->getCustomerId() . Cfg::DTPS;
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
                $result->markSucceed();
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
            $result->markSucceed();
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
            $result->markSucceed();
        }
        return $result;
    }
}