<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Service\Transfer;

use Praxigento\Accounting\Api\Service\Account\Get\Request as AccountGetRequest;
use Praxigento\Accounting\Api\Service\Operation\Request as OperationAddRequest;
use Praxigento\Accounting\Repo\Entity\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Entity\Data\Transaction as Etrans;
use Praxigento\Pv\Config as Cfg;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Pv\Service\ITransfer
{
    /** @var  \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;
    /** @var \Praxigento\Accounting\Api\Service\Account\Get */
    private $servAccount;
    /** @var  \Praxigento\Accounting\Api\Service\Operation */
    private $servOperation;

    public function __construct(
        \Praxigento\Core\App\Api\Logger\Main $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Accounting\Api\Service\Account\Get $servAccount,
        \Praxigento\Accounting\Api\Service\Operation $servOperation,
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust
    ) {
        parent::__construct($logger, $manObj);
        $this->hlpDate = $hlpDate;
        $this->servAccount = $servAccount;
        $this->servOperation = $servOperation;
        $this->repoDwnlCust = $repoDwnlCust;
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
            $downDebit = $this->repoDwnlCust->getById($custIdDebit);
            $downCredit = $this->repoDwnlCust->getById($custIdCredit);
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
            $respAccDebit = $this->servAccount->exec($reqAccGet);
            $reqAccGet->setCustomerId($custIdCredit);
            $respAccCredit = $this->servAccount->exec($reqAccGet);
            /* add transfer operation */
            $reqAddOper = new OperationAddRequest();
            $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_PV_TRANSFER);
            $reqAddOper->setDatePerformed($date);
            $reqAddOper->setOperationNote($noteOper);
            $reqAddOper->setTransactions([
                [
                    ETrans::ATTR_DEBIT_ACC_ID => $respAccDebit->getId(),
                    ETrans::ATTR_CREDIT_ACC_ID => $respAccCredit->getId(),
                    ETrans::ATTR_VALUE => $value,
                    ETrans::ATTR_NOTE => $noteTrans
                ]
            ]);
            $respAddOper = $this->servOperation->exec($reqAddOper);
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
        $this->servAccount->cacheReset();
    }

    public function creditToCustomer(Request\CreditToCustomer $request)
    {
        $result = new Response\CreditToCustomer();
        /* get representative customer account for PV asset */
        $reqRepres = new AccountGetRequest();
        $reqRepres->setIsRepresentative(TRUE);
        $reqRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $respRepres = $this->servAccount->exec($reqRepres);
        /* extract input parameters */
        $requestData = $request->get();
        $reqBetween = new Request\BetweenCustomers($requestData);
        $fromCustId = $respRepres->get(EAccount::ATTR_CUST_ID);
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
        $respRepres = $this->servAccount->exec($reqRepres);
        /* extract input parameters */
        $requestData = $request->get();
        $requestData[Request\BetweenCustomers::TO_CUSTOMER_ID] = $respRepres->get(EAccount::ATTR_CUST_ID);
        $requestData[Request\BetweenCustomers::COND_FORCE_ALL] = true;
        $reqBetween = new Request\BetweenCustomers($requestData);
        $respBetween = $this->betweenCustomers($reqBetween);
        if ($respBetween->isSucceed()) {
            $result->markSucceed();
        }
        return $result;
    }
}