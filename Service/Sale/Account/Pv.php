<?php
/**
 * File creator: dmitriimakhov@gmail.com
 */

namespace Praxigento\Pv\Service\Sale\Account;

use Praxigento\Accounting\Api\Service\Account\Get\Request as AAccountGetRequest;
use Praxigento\Accounting\Api\Service\Operation\Create\Request as AOperationRequest;
use Praxigento\Accounting\Repo\Data\Transaction as ATransaction;
use Praxigento\Pv\Api\Service\Sale\Account\Pv\Request as ARequest;
use Praxigento\Pv\Api\Service\Sale\Account\Pv\Response as AResponse;
use Praxigento\Pv\Config as Cfg;

/**
 * Transfer sale order's PV to customer account. PV is paid to the customer itself by default.
 */
class Pv
    implements \Praxigento\Pv\Api\Service\Sale\Account\Pv
{
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    private $daoGeneric;
    /** @var  \Praxigento\Pv\Repo\Dao\Sale */
    private $daoSale;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var  \Praxigento\Accounting\Api\Service\Account\Get */
    private $servAccount;
    /** @var \Praxigento\Accounting\Api\Service\Operation\Create */
    private $servOper;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Accounting\Api\Service\Account\Get $servAccount,
        \Praxigento\Accounting\Api\Service\Operation\Create $servOper,
        \Praxigento\Pv\Repo\Dao\Sale $daoSale
    )
    {
        $this->logger = $logger;
        $this->daoGeneric = $daoGeneric;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->daoSale = $daoSale;
        $this->servAccount = $servAccount;
        $this->servOper = $servOper;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        $result = new AResponse();
        $saleId = $request->getSaleOrderId();
        $customerId = $request->getCustomerId();
        $dateApplied = $request->getDateApplied();
        $sale = $this->daoSale->getById($saleId);
        $tranId = $sale->getTransRef();
        if (is_null($tranId)) {
            $pvTotal = $sale->getTotal();
            /* get customer for sale order */
            list($saleCustId, $saleIncId) = $this->getSaleOrderData($saleId);
            if (is_null($customerId)) {
                $customerId = $saleCustId;
            }
            if (!is_null($customerId)) {
                $mlmId = $this->getMlmId($customerId);
                $note = "PV for sale #$mlmId";
                /* get PV account data for customer */
                $reqGetAccCust = new AAccountGetRequest();
                $reqGetAccCust->setCustomerId($customerId);
                $reqGetAccCust->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
                $respGetAccCust = $this->servAccount->exec($reqGetAccCust);
                /* get PV account data for system */
                $reqGetAccSys = new AAccountGetRequest();
                $reqGetAccSys->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
                $reqGetAccSys->setIsSystem(TRUE);
                $respGetAccSys = $this->servAccount->exec($reqGetAccSys);
                /* create one operation with one transaction */
                $reqAddOper = new AOperationRequest();
                $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_PV_SALE_PAID);
                $reqAddOper->setOperationNote($note);
                $trans = [
                    ATransaction::A_DEBIT_ACC_ID => $respGetAccSys->getId(),
                    ATransaction::A_CREDIT_ACC_ID => $respGetAccCust->getId(),
                    ATransaction::A_VALUE => $pvTotal,
                    ATransaction::A_DATE_APPLIED => $dateApplied,
                    ATransaction::A_NOTE => $note
                ];
                $reqAddOper->setTransactions([$trans]);
                $respAddOper = $this->servOper->exec($reqAddOper);
                $operId = $respAddOper->getOperationId();
                $tranIds = $respAddOper->getTransactionsIds();
                $tranId = reset($tranIds);
                $sale->setTransRef($tranId);
                $this->daoSale->updateById($saleId, $sale);
                $result->setOperationId($operId);
                $result->markSucceed();
            } else {
            }
        } else {
            $this->logger->error("PV accounting error: there is transaction #$tranId for sale #$saleId.");
        }
        return $result;
    }

    private function getMlmId($custId)
    {
        $entity = $this->daoDwnlCust->getById($custId);
        $result = $entity->getMlmId();
        return $result;
    }

    /**
     * Get significant attributes of the sale order.
     *
     * @param int $saleId
     * @return array [$custId, $incId]
     */
    private function getSaleOrderData($saleId)
    {
        /* get referral customer ID */
        $entity = $this->daoGeneric->getEntityByPk(
            Cfg::ENTITY_MAGE_SALES_ORDER,
            [Cfg::E_COMMON_A_ENTITY_ID => $saleId],
            [Cfg::E_SALE_ORDER_A_CUSTOMER_ID, Cfg::E_SALE_ORDER_A_INCREMENT_ID]
        );
        $custId = $entity[Cfg::E_SALE_ORDER_A_CUSTOMER_ID];
        $incId = $entity[Cfg::E_SALE_ORDER_A_INCREMENT_ID];
        return [$custId, $incId];
    }
}