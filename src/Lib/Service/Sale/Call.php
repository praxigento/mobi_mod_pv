<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Sale;

use Praxigento\Accounting\Data\Entity\Account;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Lib\Service\Account\Request\Get as GetAccountRequest;
use Praxigento\Accounting\Lib\Service\Account\Request\GetRepresentative as GetAccountRepresentativeRequest;
use Praxigento\Accounting\Lib\Service\Operation\Request\Add as AddOperationRequest;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Data\Entity\Sale;
use Praxigento\Pv\Data\Entity\Sale\Item as SaleItem;
use Praxigento\Pv\Lib\Service\ISale;

class Call extends \Praxigento\Core\Service\Base\Call implements ISale
{
    /** @var \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var \Praxigento\Core\Repo\IBasic */
    protected $_repoBasic;
    /** @var  \Praxigento\Accounting\Lib\Service\IAccount */
    protected $_callAccount;
    /** @var  \Praxigento\Accounting\Lib\Service\IOperation */
    protected $_callOperation;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Praxigento\Core\Repo\IBasic $repoBasic,
        \Praxigento\Accounting\Lib\Service\IAccount $callAccount,
        \Praxigento\Accounting\Lib\Service\IOperation $callOperation
    ) {
        parent::__construct($logger);
        $this->_manTrans = $manTrans;
        $this->_repoBasic = $repoBasic;
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
    public function accountPv(Request\AccountPv $request)
    {
        $result = new Response\AccountPv();
        $saleId = $request->getSaleOrderId();
        $customerId = $request->getCustomerId();
        $dateApplied = $request->getDateApplied();
        $this->_logger->info("PV accounting operation for sale order #$saleId is started.");
        $data = $this->_repoBasic->getEntityByPk(Sale::ENTITY_NAME, [Sale::ATTR_SALE_ID => $saleId]);
        $pvTotal = $data[Sale::ATTR_TOTAL];
        /* get customer for sale order */
        if (is_null($customerId)) {
            $this->_logger->info("There is no customer ID in request, select customer ID from sales order data.");
            $data = $this->_repoBasic->getEntityByPk(
                Cfg::ENTITY_MAGE_SALES_ORDER,
                [Cfg::E_COMMON_A_ENTITY_ID => $saleId],
                [Cfg::E_SALE_ORDER_A_CUSTOMER_ID]
            );
            $customerId = $data[Cfg::E_SALE_ORDER_A_CUSTOMER_ID];
            $this->_logger->info("Order #$saleId is created by customer #$customerId.");
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
            Transaction::ATTR_DEBIT_ACC_ID => $accRepres[Account::ATTR_ID],
            Transaction::ATTR_CREDIT_ACC_ID => $accCust[Account::ATTR_ID],
            Transaction::ATTR_VALUE => $pvTotal,
            Transaction::ATTR_DATE_APPLIED => $dateApplied
        ];
        $reqAddOper->setTransactions([$trans]);
        $respAddOper = $this->_callOperation->add($reqAddOper);
        if ($respAddOper->isSucceed()) {
            $operId = $respAddOper->getOperationId();
            $result->setData(Response\AccountPv::OPERATION_ID, $operId);
            $result->markSucceed();
        }
        $this->_logger->info("PV accounting operation for sale order #$saleId is completed.");
        return $result;
    }

    public function cacheReset()
    {
        $this->_callAccount->cacheReset();
    }

    /**
     * Save PV data on sale order save.
     *
     * @param Request\Save $request
     *
     * @return Response\Save
     */
    public function save(Request\Save $request)
    {
        $result = new Response\Save();
        $orderData = $request->getOrderData();
        $orderId = $orderData[Sale::ATTR_SALE_ID];
        $this->_logger->info("Save PV attributes for sale order #$orderId.");
        $trans = $this->_manTrans->transactionBegin();
        try {
            /* save order data */
            $this->_repoBasic->replaceEntity(Sale::ENTITY_NAME, $orderData);
            $items = $request->getOrderItemsData();
            foreach ($items as $one) {
                $this->_repoBasic->replaceEntity(SaleItem::ENTITY_NAME, $one);
            }
            $this->_manTrans->transactionCommit($trans);
            $result->markSucceed();
            $this->_logger->info("PV attributes for sale order #$orderId are saved.");
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }
}