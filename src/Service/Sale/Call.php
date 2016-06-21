<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Sale;

use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Service\Account\Request\Get as GetAccountRequest;
use Praxigento\Accounting\Service\Account\Request\GetRepresentative as GetAccountRepresentativeRequest;
use Praxigento\Accounting\Service\Operation\Request\Add as AddOperationRequest;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Data\Entity\Sale;
use Praxigento\Pv\Service\ISale;

class Call extends \Praxigento\Core\Service\Base\Call implements ISale
{
    /** @var  \Praxigento\Accounting\Service\IAccount */
    protected $_callAccount;
    /** @var  \Praxigento\Accounting\Service\IOperation */
    protected $_callOperation;
    /** @var \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoGeneric;
    /** @var  \Praxigento\Pv\Repo\IModule */
    protected $_repoMod;
    /** @var  \Praxigento\Pv\Repo\Entity\ISale */
    protected $_repoSale;
    /** @var  \Praxigento\Pv\Repo\Entity\Sale\IItem */
    protected $_repoSaleItem;
    /** @var  \Praxigento\Pv\Repo\Entity\Stock\IItem */
    protected $_repoStockItem;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Praxigento\Accounting\Service\IAccount $callAccount,
        \Praxigento\Accounting\Service\IOperation $callOperation,
        \Praxigento\Pv\Repo\IModule $repoMod,
        \Praxigento\Pv\Repo\Entity\ISale $repoSale,
        \Praxigento\Pv\Repo\Entity\Sale\IItem $repoSaleItem,
        \Praxigento\Pv\Repo\Entity\Stock\IItem $repoStockItem,
        \Praxigento\Core\Tool\IDate $toolDate
    ) {
        parent::__construct($logger);
        $this->_manTrans = $manTrans;
        $this->_repoGeneric = $repoGeneric;
        $this->_callAccount = $callAccount;
        $this->_callOperation = $callOperation;
        $this->_repoMod = $repoMod;
        $this->_repoSale = $repoSale;
        $this->_repoSaleItem = $repoSaleItem;
        $this->_repoStockItem = $repoStockItem;
        $this->_toolDate = $toolDate;
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
        $sale = $this->_repoSale->getById($saleId);
        $pvTotal = $sale->getTotal();
        /* get customer for sale order */
        if (is_null($customerId)) {
            $this->_logger->info("There is no customer ID in request, select customer ID from sale order data.");
            $customerId = $this->_repoMod->getSaleOrderCustomerId($saleId);
            $this->_logger->info("Order #$saleId is created by customer #$customerId.");
        }
        /* get PV account data for customer */
        $reqGetAccCust = new GetAccountRequest();
        $reqGetAccCust->setCustomerId($customerId);
        $reqGetAccCust->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $reqGetAccCust->setCreateNewAccountIfMissed(true);
        $respGetAccCust = $this->_callAccount->get($reqGetAccCust);
        /* get PV account data for representative */
        $reqGetAccRepres = new GetAccountRepresentativeRequest();
        $reqGetAccRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $respGetAccRepres = $this->_callAccount->getRepresentative($reqGetAccRepres);
        /* create one operation with one transaction */
        $reqAddOper = new AddOperationRequest();
        $reqAddOper->setOperationTypeCode(Cfg::CODE_TYPE_OPER_PV_SALE_PAID);
        $trans = [
            Transaction::ATTR_DEBIT_ACC_ID => $respGetAccRepres->getId(),
            Transaction::ATTR_CREDIT_ACC_ID => $respGetAccCust->getId(),
            Transaction::ATTR_VALUE => $pvTotal,
            Transaction::ATTR_DATE_APPLIED => $dateApplied
        ];
        $reqAddOper->setTransactions([$trans]);
        $respAddOper = $this->_callOperation->add($reqAddOper);
        $operId = $respAddOper->getOperationId();
        $result->setOperationId($operId);
        $result->markSucceed();
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
     * @param Request\Save $req
     *
     * @return Response\Save
     */
    public function save(Request\Save $req)
    {
        $result = new Response\Save();
        $orderId = $req->getSaleOrderId();
        $items = $req->getOrderItems();
        $this->_logger->info("Save PV attributes for sale order #$orderId.");
        $trans = $this->_manTrans->transactionBegin();
        try {
            /* for all items get PV data by warehouse */
            $orderTotal = 0;
            foreach ($items as $item) {
                $prodId = $item->getProductId();
                $stockId = $item->getStockId();
                $pv = $this->_repoStockItem->getPvByProductAndStock($prodId, $stockId);
                $qty = $item->getQuantity();
                $total = $pv * $qty;
                $eItem = new \Praxigento\Pv\Data\Entity\Sale\Item();
                $eItem->setSaleItemId($item->getItemId());
                $eItem->setSubtotal($total);
                $eItem->setDiscount(0);
                $eItem->setTotal($total);
                $eItem->setSaleItemId($item->getItemId());
                $this->_repoSaleItem->replace($eItem);
                $orderTotal += $total;
            }
            /* save order data */
            $eOrder = new \Praxigento\Pv\Data\Entity\Sale();
            $eOrder->setSaleId($orderId);
            $eOrder->setSubtotal($orderTotal);
            $eOrder->setDiscount(0);
            $eOrder->setTotal($orderTotal);
            $datePaid = $this->_toolDate->getUtcNowForDb();
            $eOrder->setDatePaid($datePaid);
            $this->_repoSale->replace($eOrder);
            $this->_manTrans->transactionCommit($trans);
            $result->markSucceed();
            $this->_logger->info("PV attributes for sale order #$orderId are saved.");
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }

}