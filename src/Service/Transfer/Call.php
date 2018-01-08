<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Service\Transfer;

use Praxigento\Accounting\Api\Service\Account\Get\Request as AccountGetRequest;
use Praxigento\Accounting\Api\Service\Operation\Request as OperationAddRequest;
use Praxigento\Accounting\Repo\Entity\Data\Account;
use Praxigento\Accounting\Repo\Entity\Data\Transaction;
use Praxigento\Pv\Config as Cfg;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Pv\Service\ITransfer
{
    /**
     * @var \Praxigento\Accounting\Api\Service\Account\Get
     */
    protected $callAccount;
    /** @var  \Praxigento\Accounting\Api\Service\Operation */
    protected $callOperation;
    /** @var  \Praxigento\Core\Api\Helper\Date */
    protected $hlpDate;
    /**
     * Other module's repositories adapter.
     *
     * @var \Praxigento\Pv\Repo\IModule
     */
    protected $repoMod;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Accounting\Api\Service\Account\Get $callAccount,
        \Praxigento\Accounting\Api\Service\Operation $callOperation,
        \Praxigento\Pv\Repo\IModule $repoMod
    ) {
        parent::__construct($logger, $manObj);
        $this->hlpDate = $hlpDate;
        $this->callAccount = $callAccount;
        $this->callOperation = $callOperation;
        $this->repoMod = $repoMod;
    }


    public function betweenCustomers(Request\BetweenCustomers $request)
    {
        $result = new Response\BetweenCustomers();
        /* constraints validation results */
        $isCountriesTheSame = false;
        $isTargetInDownline = false;
        /* extract input parameters */
        $custIdDebit = $request->getFromCustomerId();
        $custIdCredit = $request->getToCustomerId();
        $date = $request->getDateApplied();
        $value = $request->getValue();
        $condForceAll = $request->getConditionForceAll();
        $condForceCountry = $request->getConditionForceCountry();
        $condForceDownline = $request->getConditionForceDownline();
        $noteOper = $request->getNoteOperation();
        $noteTrans = $request->getNoteTransaction();
        if (is_null($date)) {
            $date = $this->hlpDate->getUtcNowForDb();
        }
        /* validate conditions */
        if (!$condForceAll) {
            /* validate customer countries */
            $downDebit = $this->repoMod->getDownlineCustomerById($custIdDebit);
            $downCredit = $this->repoMod->getDownlineCustomerById($custIdCredit);
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
                $isTargetInDownline = true;
            }
        }
        /* check validation results and perform transfer */
        if (
            $condForceAll ||
            ($isTargetInDownline && $isCountriesTheSame)
        ) {
            /* get PV-accounts */
            $reqAccGet = new AccountGetRequest();
            $reqAccGet->setCustomerId($custIdDebit);
            $reqAccGet->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
            $respAccDebit = $this->callAccount->exec($reqAccGet);
            $reqAccGet->setCustomerId($custIdCredit);
            $respAccCredit = $this->callAccount->exec($reqAccGet);
            /* add transfer operation */
            $reqAddOper = new OperationAddRequest();
            $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_PV_TRANSFER);
            $reqAddOper->setDatePerformed($date);
            $reqAddOper->setOperationNote($noteOper);
            $reqAddOper->setTransactions([
                [
                    Transaction::ATTR_DEBIT_ACC_ID => $respAccDebit->getId(),
                    Transaction::ATTR_CREDIT_ACC_ID => $respAccCredit->getId(),
                    Transaction::ATTR_VALUE => $value,
                    Transaction::ATTR_NOTE => $noteTrans
                ]
            ]);
            $respAddOper = $this->callOperation->exec($reqAddOper);
            if ($respAddOper->isSucceed()) {
                $result->setOperationId($respAddOper->getOperationId());
                $result->setTransactionsIds($respAddOper->getTransactionsIds());
                $result->markSucceed();
            }
        } else {
            /* some of the constraints are not satisfied */
            $result->setErrorCode(\Praxigento\Pv\Service\Transfer\Response\BetweenCustomers::ERR_VALIDATION);
            if (!$isCountriesTheSame) $result->setIsInvalidCountries(true);
            if (!$isTargetInDownline) $result->setIsInvalidDownline(true);
        }
        return $result;
    }

    /**
     * Reset cached data.
     */
    public function cacheReset()
    {
        $this->callAccount->cacheReset();
    }

    public function creditToCustomer(Request\CreditToCustomer $request)
    {
        $result = new Response\CreditToCustomer();
        /* get representative customer account for PV asset */
        $reqRepres = new AccountGetRequest();
        $reqRepres->setIsRepresentative(TRUE);
        $reqRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $respRepres = $this->callAccount->exec($reqRepres);
        /* extract input parameters */
        $requestData = $request->get();
        $reqBetween = new Request\BetweenCustomers($requestData);
        $fromCustId = $respRepres->get(Account::ATTR_CUST_ID);
        $reqBetween->setFromCustomerId($fromCustId);
        $reqBetween->setConditionForceAll(true);
        $respBetween = $this->betweenCustomers($reqBetween);
        if ($respBetween->isSucceed()) {
            $result->setOperationId($respBetween->getOperationId());
            $result->setTransactionsIds($respBetween->getTransactionsIds());
            $result->markSucceed();
        }
        return $result;
    }

    public function debitFromCustomer(Request\DebitFromCustomer $request)
    {
        $result = new Response\DebitFromCustomer();
        /* get representative customer account for PV asset */
        $reqRepres = new AccountGetRequest();
        $reqRepres->setIsRepresentative(TRUE);
        $reqRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $respRepres = $this->callAccount->exec($reqRepres);
        /* extract input parameters */
        $requestData = $request->get();
        $requestData[Request\BetweenCustomers::TO_CUSTOMER_ID] = $respRepres->get(Account::ATTR_CUST_ID);
        $requestData[Request\BetweenCustomers::COND_FORCE_ALL] = true;
        $reqBetween = new Request\BetweenCustomers($requestData);
        $respBetween = $this->betweenCustomers($reqBetween);
        if ($respBetween->isSucceed()) {
            $result->markSucceed();
        }
        return $result;
    }
}