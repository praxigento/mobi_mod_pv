<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Service\Sale;

use Praxigento\Accounting\Api\Service\Account\Get\Request as GetAccountRequest;
use Praxigento\Accounting\Api\Service\Operation\Request as AddOperationRequest;
use Praxigento\Accounting\Repo\Entity\Data\Transaction;
use Praxigento\Pv\Config as Cfg;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Pv\Service\ISale
{
    /**
     * @var \Praxigento\Core\App\Api\Repo\Transaction\Manager
     * @deprecated transaction should not be used on the service layer (nested transactions are prohibited in MySQL)
     */
    private $manTrans;
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    private $repoGeneric;
    /** @var  \Praxigento\Pv\Repo\Entity\Sale */
    private $repoSale;
    /** @var  \Praxigento\Pv\Repo\Entity\Sale\Item */
    private $repoSaleItem;
    /** @var  \Praxigento\Pv\Repo\Entity\Stock\Item */
    private $repoStockItem;
    /** @var  \Praxigento\Accounting\Api\Service\Account\Get */
    private $servAccount;
    /** @var \Praxigento\Accounting\Api\Service\Operation */
    private $servOper;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\App\Api\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Api\Service\Account\Get $servAccount,
        \Praxigento\Accounting\Api\Service\Operation $servOper,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric,
        \Praxigento\Pv\Repo\Entity\Sale $repoSale,
        \Praxigento\Pv\Repo\Entity\Sale\Item $repoSaleItem,
        \Praxigento\Pv\Repo\Entity\Stock\Item $repoStockItem
    ) {
        parent::__construct($logger, $manObj);
        $this->manTrans = $manTrans;
        $this->servAccount = $servAccount;
        $this->servOper = $servOper;
        $this->repoGeneric = $repoGeneric;
        $this->repoSale = $repoSale;
        $this->repoSaleItem = $repoSaleItem;
        $this->repoStockItem = $repoStockItem;
    }

    /**
     * Account PV on sale done.
     *
     * @param \Praxigento\Pv\Service\Sale\Request\AccountPv $request
     * @return \Praxigento\Pv\Service\Sale\Response\AccountPv
     * @throws \Exception
     */
    public function accountPv(Request\AccountPv $request)
    {
        $result = new Response\AccountPv();
        $saleId = $request->getSaleOrderId();
        $customerId = $request->getCustomerId();
        $dateApplied = $request->getDateApplied();
        $this->logger->info("PV accounting operation for sale order #$saleId is started.");
        $sale = $this->repoSale->getById($saleId);
        $pvTotal = $sale->getTotal();
        /* get customer for sale order */
        if (is_null($customerId)) {
            $this->logger->info("There is no customer ID in request, select customer ID from sale order data.");
            $customerId = $this->getSaleOrderCustomerId($saleId);
            $this->logger->info("Order #$saleId is created by customer #$customerId.");
        }
        if (!is_null($customerId)) {
            /* get PV account data for customer */
            $reqGetAccCust = new GetAccountRequest();
            $reqGetAccCust->setCustomerId($customerId);
            $reqGetAccCust->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
            $respGetAccCust = $this->servAccount->exec($reqGetAccCust);
            /* get PV account data for representative */
            $reqGetAccRepres = new GetAccountRequest();
            $reqGetAccRepres->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
            $reqGetAccRepres->setIsRepresentative(TRUE);
            $respGetAccRepres = $this->servAccount->exec($reqGetAccRepres);
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
            $respAddOper = $this->servOper->exec($reqAddOper);
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
        $this->servAccount->cacheReset();
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

    /**
     * Save PV data on sale order save.
     *
     * @param \Praxigento\Pv\Service\Sale\Request\Save $req
     * @return \Praxigento\Pv\Service\Sale\Response\Save
     * @throws \Exception
     */
    public function save(Request\Save $req)
    {
        $result = new Response\Save();
        $orderId = $req->getSaleOrderId();
        $datePaid = $req->getSaleOrderDatePaid();
        $items = $req->getOrderItems();
        $this->logger->info("Save PV attributes for sale order #$orderId.");
        $def = $this->manTrans->begin();
        try {
            /* for all items get PV data by warehouse */
            $orderTotal = 0;
            foreach ($items as $item) {
                $prodId = $item->getProductId();
                $stockId = $item->getStockId();
                $itemId = $item->getItemId();
                $pv = $this->repoStockItem->getPvByProductAndStock($prodId, $stockId);
                $qty = $item->getQuantity();
                $total = $pv * $qty;
                $eItem = new \Praxigento\Pv\Repo\Entity\Data\Sale\Item();
                $eItem->setItemRef($itemId);
                $eItem->setSubtotal($total);
                $eItem->setDiscount(0);
                $eItem->setTotal($total);
                $this->repoSaleItem->replace($eItem);
                $orderTotal += $total;
            }
            /* save order data */
            $eOrder = new \Praxigento\Pv\Repo\Entity\Data\Sale();
            $eOrder->setSaleRef($orderId);
            $eOrder->setSubtotal($orderTotal);
            $eOrder->setDiscount(0);
            $eOrder->setTotal($orderTotal);
            $eOrder->setDatePaid($datePaid);
            $this->repoSale->replace($eOrder);
            $this->manTrans->commit($def);
            $result->markSucceed();
            $this->logger->info("PV attributes for sale order #$orderId are saved.");
        } finally {
            $this->manTrans->end($def);
        }
        return $result;
    }

}