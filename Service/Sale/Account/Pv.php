<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Pv\Service\Sale\Account;

use Praxigento\Accounting\Api\Service\Account\Get\Request as AAccountGetRequest;
use Praxigento\Accounting\Api\Service\Operation\Request as AOperationRequest;
use Praxigento\Accounting\Repo\Entity\Data\Transaction as ATransaction;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Service\Sale\Account\Pv\Request as ARequest;
use Praxigento\Pv\Service\Sale\Account\Pv\Response as AResponse;

class Pv
{
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    private $repoGeneric;
    /** @var  \Praxigento\Pv\Repo\Entity\Sale */
    private $repoSale;
    /** @var  \Praxigento\Accounting\Api\Service\Account\Get */
    private $servAccount;
    /** @var \Praxigento\Accounting\Api\Service\Operation */
    private $servOper;

    public function __construct(
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric,
        \Praxigento\Accounting\Api\Service\Account\Get $servAccount,
        \Praxigento\Accounting\Api\Service\Operation $servOper,
        \Praxigento\Pv\Repo\Entity\Sale $repoSale
    )
    {
        $this->repoGeneric = $repoGeneric;
        $this->servAccount = $servAccount;
        $this->servOper = $servOper;
        $this->repoSale = $repoSale;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec(ARequest $request)
    {
        $result = new AResponse();
        $saleId = $request->getSaleOrderId();
        $customerId = $request->getCustomerId();
        $dateApplied = $request->getDateApplied();
        $sale = $this->repoSale->getById($saleId);
        $pvTotal = $sale->getTotal();
        /* get customer for sale order */
        if (is_null($customerId)) {
            $customerId = $this->getSaleOrderCustomerId($saleId);
        }
        if (!is_null($customerId)) {
            /* get PV account data for customer */
            $reqGetAccCust = new AAccountGetRequest();
            $reqGetAccCust->setCustomerId($customerId);
            $reqGetAccCust->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
            $respGetAccCust = $this->servAccount->exec($reqGetAccCust);
            /* get PV account data for representative */
            $reqGetAccRepres = new AAccountGetRequest();
            $reqGetAccRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
            $reqGetAccRepres->setIsRepresentative(TRUE);
            $respGetAccRepres = $this->servAccount->exec($reqGetAccRepres);
            /* create one operation with one transaction */
            $reqAddOper = new AOperationRequest();
            $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_PV_SALE_PAID);
            $trans = [
                ATransaction::ATTR_DEBIT_ACC_ID => $respGetAccRepres->getId(),
                ATransaction::ATTR_CREDIT_ACC_ID => $respGetAccCust->getId(),
                ATransaction::ATTR_VALUE => $pvTotal,
                ATransaction::ATTR_DATE_APPLIED => $dateApplied
            ];
            $reqAddOper->setTransactions([$trans]);
            $respAddOper = $this->servOper->exec($reqAddOper);
            $operId = $respAddOper->getOperationId();
            $result->setOperationId($operId);
            $result->markSucceed();
        } else {
        }
        return $result;
    }

    private function getSaleOrderCustomerId($saleId)
    {
        $data = $this->repoGeneric->getEntityByPk(
            Cfg::ENTITY_MAGE_SALES_ORDER,
            [Cfg::E_COMMON_A_ENTITY_ID => $saleId],
            [Cfg::E_SALE_ORDER_A_CUSTOMER_ID]
        );
        $result = $data[Cfg::E_SALE_ORDER_A_CUSTOMER_ID];
        return $result;
    }

}