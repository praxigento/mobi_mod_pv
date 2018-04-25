<?php
/**
 * File creator: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Pv\Service\Sale\Account;

use Praxigento\Accounting\Api\Service\Account\Get\Request as AAccountGetRequest;
use Praxigento\Accounting\Api\Service\Operation\Request as AOperationRequest;
use Praxigento\Accounting\Repo\Data\Transaction as ATransaction;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Service\Sale\Account\Pv\Request as ARequest;
use Praxigento\Pv\Service\Sale\Account\Pv\Response as AResponse;

class Pv
{
    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    private $daoGeneric;
    /** @var  \Praxigento\Pv\Repo\Dao\Sale */
    private $daoSale;
    /** @var  \Praxigento\Accounting\Api\Service\Account\Get */
    private $servAccount;
    /** @var \Praxigento\Accounting\Api\Service\Operation */
    private $servOper;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric,
        \Praxigento\Accounting\Api\Service\Account\Get $servAccount,
        \Praxigento\Accounting\Api\Service\Operation $servOper,
        \Praxigento\Pv\Repo\Dao\Sale $daoSale
    )
    {
        $this->daoGeneric = $daoGeneric;
        $this->servAccount = $servAccount;
        $this->servOper = $servOper;
        $this->daoSale = $daoSale;
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
        $sale = $this->daoSale->getById($saleId);
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
            /* get PV account data for system */
            $reqGetAccSys = new AAccountGetRequest();
            $reqGetAccSys->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
            $reqGetAccSys->setIsSystem(TRUE);
            $respGetAccSys = $this->servAccount->exec($reqGetAccSys);
            /* create one operation with one transaction */
            $reqAddOper = new AOperationRequest();
            $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_PV_SALE_PAID);
            $trans = [
                ATransaction::A_DEBIT_ACC_ID => $respGetAccSys->getId(),
                ATransaction::A_CREDIT_ACC_ID => $respGetAccCust->getId(),
                ATransaction::A_VALUE => $pvTotal,
                ATransaction::A_DATE_APPLIED => $dateApplied
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
        $data = $this->daoGeneric->getEntityByPk(
            Cfg::ENTITY_MAGE_SALES_ORDER,
            [Cfg::E_COMMON_A_ENTITY_ID => $saleId],
            [Cfg::E_SALE_ORDER_A_CUSTOMER_ID]
        );
        $result = $data[Cfg::E_SALE_ORDER_A_CUSTOMER_ID];
        return $result;
    }

}