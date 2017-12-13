<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Sale;

use Praxigento\Accounting\Api\Service\Account\Get\Request as GetAccountRequest;
use Praxigento\Accounting\Repo\Entity\Data\Transaction;
use Praxigento\Accounting\Service\Operation\Request\Add as AddOperationRequest;
use Praxigento\Pv\Config as Cfg;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Pv\Service\ISale
{
    /** @var  \Praxigento\Accounting\Api\Service\Account\Get */
    protected $_callAccount;
    /** @var  \Praxigento\Accounting\Service\IOperation */
    protected $_callOperation;
    /** @var \Praxigento\Core\App\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var  \Praxigento\Pv\Repo\IModule */
    protected $_repoMod;
    /** @var  \Praxigento\Pv\Repo\Entity\Sale */
    protected $_repoSale;
    /** @var  \Praxigento\Pv\Repo\Entity\Sale\Item */
    protected $_repoSaleItem;
    /** @var  \Praxigento\Pv\Repo\Entity\Stock\Item */
    protected $_repoStockItem;
    /** @var \Praxigento\Core\Tool\IDate */
    protected $_toolDate;

    /**
     * Call constructor.
     * @param \Praxigento\Core\App\Logger\App $logger
     * @param \Magento\Framework\ObjectManagerInterface $manObj
     * @param \Praxigento\Core\App\Transaction\Database\IManager $manTrans
     * @param \Praxigento\Accounting\Service\IAccount $callAccount
     * @param \Praxigento\Accounting\Service\IOperation $callOperation
     * @param \Praxigento\Pv\Repo\IModule $repoMod
     * @param \Praxigento\Pv\Repo\Entity\Sale $repoSale
     * @param \Praxigento\Pv\Repo\Entity\Sale\Item $repoSaleItem
     * @param \Praxigento\Pv\Repo\Entity\Stock\Item $repoStockItem
     * @param \Praxigento\Core\Tool\IDate $toolDate
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Praxigento\Core\App\Logger\App $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Accounting\Api\Service\Account\Get $callAccount,
        \Praxigento\Accounting\Service\IOperation $callOperation,
        \Praxigento\Pv\Repo\IModule $repoMod,
        \Praxigento\Pv\Repo\Entity\Sale $repoSale,
        \Praxigento\Pv\Repo\Entity\Sale\Item $repoSaleItem,
        \Praxigento\Pv\Repo\Entity\Stock\Item $repoStockItem,
        \Praxigento\Core\Tool\IDate $toolDate
    ) {
        parent::__construct($logger, $manObj);
        $this->_manTrans = $manTrans;
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
        $this->logger->info("PV accounting operation for sale order #$saleId is started.");
        $sale = $this->_repoSale->getById($saleId);
        $pvTotal = $sale->getTotal();
        /* get customer for sale order */
        if (is_null($customerId)) {
            $this->logger->info("There is no customer ID in request, select customer ID from sale order data.");
            $customerId = $this->_repoMod->getSaleOrderCustomerId($saleId);
            $this->logger->info("Order #$saleId is created by customer #$customerId.");
        }
        if (!is_null($customerId)) {
            /* get PV account data for customer */
            $reqGetAccCust = new GetAccountRequest();
            $reqGetAccCust->setCustomerId($customerId);
            $reqGetAccCust->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
            $respGetAccCust = $this->_callAccount->exec($reqGetAccCust);
            /* get PV account data for representative */
            $reqGetAccRepres = new GetAccountRequest();
            $reqGetAccRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
            $reqGetAccRepres->setIsRepresentative(TRUE);
            $respGetAccRepres = $this->_callAccount->exec($reqGetAccRepres);
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
            $this->logger->info("PV accounting operation for sale order #$saleId is completed.");
        } else {
            $this->logger->info("PV accounting operation for sale order #$saleId cannot be completed. Customer is not defined (guest?).");
        }
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
        $datePaid = $req->getSaleOrderDatePaid();
        $items = $req->getOrderItems();
        $this->logger->info("Save PV attributes for sale order #$orderId.");
        $def = $this->_manTrans->begin();
        try {
            /* for all items get PV data by warehouse */
            $orderTotal = 0;
            foreach ($items as $item) {
                $prodId = $item->getProductId();
                $stockId = $item->getStockId();
                $itemId = $item->getItemId();
                $pv = $this->_repoStockItem->getPvByProductAndStock($prodId, $stockId);
                $qty = $item->getQuantity();
                $total = $pv * $qty;
                $eItem = new \Praxigento\Pv\Repo\Entity\Data\Sale\Item();
                $eItem->setSaleItemId($itemId);
                $eItem->setSubtotal($total);
                $eItem->setDiscount(0);
                $eItem->setTotal($total);
                $this->_repoSaleItem->replace($eItem);
                $orderTotal += $total;
            }
            /* save order data */
            $eOrder = new \Praxigento\Pv\Repo\Entity\Data\Sale();
            $eOrder->setSaleId($orderId);
            $eOrder->setSubtotal($orderTotal);
            $eOrder->setDiscount(0);
            $eOrder->setTotal($orderTotal);
            $eOrder->setDatePaid($datePaid);
            $this->_repoSale->replace($eOrder);
            $this->_manTrans->commit($def);
            $result->markSucceed();
            $this->logger->info("PV attributes for sale order #$orderId are saved.");
        } finally {
            $this->_manTrans->end($def);
        }
        return $result;
    }

}