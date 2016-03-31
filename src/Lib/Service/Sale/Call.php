<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Sale;

use Praxigento\Accounting\Lib\Entity\Account;
use Praxigento\Accounting\Lib\Entity\Transaction;
use Praxigento\Accounting\Lib\Service\Account\Request\Get as GetAccountRequest;
use Praxigento\Accounting\Lib\Service\Account\Request\GetRepresentative as GetAccountRepresentativeRequest;
use Praxigento\Accounting\Lib\Service\Operation\Request\Add as AddOperationRequest;
use Praxigento\Core\Lib\Service\Repo\Request\GetEntityByPk as GetEntityByPkRequest;
use Praxigento\Core\Lib\Service\Repo\Request\ReplaceEntity as ReplaceEntityRequest;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Lib\Entity\Sale;
use Praxigento\Pv\Lib\Entity\Sale\Item as SaleItem;
use Praxigento\Pv\Lib\Service\ISale;

class Call extends \Praxigento\Core\Lib\Service\Base\Call implements ISale {

    /** @var  \Praxigento\Accounting\Lib\Service\IAccount */
    private $_callAccount;
    /** @var  \Praxigento\Accounting\Lib\Service\IOperation */
    private $_callOperation;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Lib\Context\IDbAdapter $dba,
        \Praxigento\Core\Lib\IToolbox $toolbox,
        \Praxigento\Core\Lib\Service\IRepo $callRepo,
        \Praxigento\Accounting\Lib\Service\IAccount $callAccount,
        \Praxigento\Accounting\Lib\Service\IOperation $callOperation
    ) {
        parent::__construct($logger, $dba, $toolbox, $callRepo);
        $this->_callAccount = $callAccount;
        $this->_callOperation = $callOperation;
    }

    /**
     * Account PV on sale done.
     *
     * @param Request\AccountPv $request
     *
     * @return Response\AccountPv
     */
    public function accountPv(Request\AccountPv $request) {
        $result = new Response\AccountPv();
        $saleId = $request->getSaleOrderId();
        $customerId = $request->getCustomerId();
        $dateApplied = $request->getDateApplied();
        $this->_logger->info("PV accounting operation for sale order #$saleId is started.");
        $reqGetSalePv = new GetEntityByPkRequest(Sale::ENTITY_NAME, [ Sale::ATTR_SALE_ID => $saleId ]);
        $respGetSalePv = $this->_callRepo->getEntityByPk($reqGetSalePv);
        $pvTotal = $respGetSalePv->getData(Sale::ATTR_TOTAL);
        /* get customer for sale order */
        if(is_null($customerId)) {
            $reqGetSaleOrder = new GetEntityByPkRequest(
                Cfg::ENTITY_MAGE_SALES_ORDER,
                [ Cfg::E_COMMON_A_ENTITY_ID => $saleId ],
                [ Cfg::E_SALE_ORDER_A_CUSTOMER_ID ]

            );
            $this->_logger->info("There is no customer ID in request, select customer ID from sales order data.");
            $respGetSaleOrder = $this->_callRepo->getEntityByPk($reqGetSaleOrder);
            if($respGetSaleOrder->isSucceed()) {
                $customerId = $respGetSaleOrder->getData(Cfg::E_SALE_ORDER_A_CUSTOMER_ID);
                $this->_logger->info("Order #$saleId is created by customer #$customerId.");
            }
        }
        /* get PV account data for customer */
        $reqGetAccCust = new GetAccountRequest();
        $reqGetAccCust->setData(GetAccountRequest::CUSTOMER_ID, $customerId);
        $reqGetAccCust->setData(GetAccountRequest::ASSET_TYPE_CODE, Cfg::CODE_TYPE_ASSET_PV);
        $reqGetAccCust->setData(GetAccountRequest::CREATE_NEW_ACCOUNT_IF_MISSED, true);
        $respGetAccCust = $this->_callAccount->get($reqGetAccCust);
        $accCust = $respGetAccCust->getData();
        /* get PV account data for representative */
        $reqGetAccRepres = new GetAccountRepresentativeRequest();
        $reqGetAccRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $respGetAccRepres = $this->_callAccount->getRepresentative($reqGetAccRepres);
        $accRepres = $respGetAccRepres->getData();
        /* create one operation with one transaction */
        $reqAddOper = new AddOperationRequest();
        $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_PV_SALE_PAID);
        $trans = [
            Transaction::ATTR_DEBIT_ACC_ID  => $accRepres[Account::ATTR_ID],
            Transaction::ATTR_CREDIT_ACC_ID => $accCust[Account::ATTR_ID],
            Transaction::ATTR_VALUE         => $pvTotal,
            Transaction::ATTR_DATE_APPLIED  => $dateApplied
        ];
        $reqAddOper->setTransactions([ $trans ]);
        $respAddOper = $this->_callOperation->add($reqAddOper);
        if($respAddOper->isSucceed()) {
            $operId = $respAddOper->getOperationId();
            $result->setData(Response\AccountPv::OPERATION_ID, $operId);
            $result->setAsSucceed();
        }
        $this->_logger->info("PV accounting operation for sale order #$saleId is completed.");
        return $result;
    }

    /**
     * Save PV data on sale order save.
     *
     * @param Request\Save $request
     *
     * @return Response\Save
     */
    public function save(Request\Save $request) {
        $result = new Response\Save();
        $orderData = $request->getOrderData();
        $orderId = $orderData[Sale::ATTR_SALE_ID];
        $this->_logger->info("Save PV attributes for sale order #$orderId.");
        $this->_getConn()->beginTransaction();
        try {
            /* save order data */
            $reqReplace = new ReplaceEntityRequest(Sale::ENTITY_NAME, $orderData);
            $respReplace = $this->_callRepo->replaceEntity($reqReplace);
            if($respReplace->isSucceed()) {
                $items = $request->getOrderItemsData();
                foreach($items as $one) {
                    $reqReplace = new ReplaceEntityRequest(SaleItem::ENTITY_NAME, $one);
                    $respReplace = $this->_callRepo->replaceEntity($reqReplace);
                    if(!$respReplace->isSucceed()) {
                        $itemId = $one[SaleItem::ATTR_SALE_ITEM_ID];
                        throw new \Exception("Cannot replace data for item #$itemId");
                    }
                }
                $this->_getConn()->commit();
                $result->setAsSucceed();
                $this->_logger->info("PV attributes for sale order #$orderId are saved.");
            }
        } catch(\Exception $e) {
            $this->_getConn()->rollback();
            $this->_logger->error("Cannot save PV attributes for sale order #$orderId. Exception: " . $e->getMessage());
        }
        return $result;
    }

    public function cacheReset() {
        $this->_callAccount->cacheReset();
    }
}