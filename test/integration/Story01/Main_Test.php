<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Test\Story01;

use Magento\Catalog\Api\Data\ProductInterface as MageProd;
use Magento\CatalogInventory\Api\Data\StockItemInterface as MageStockItem;
use Magento\Sales\Api\Data\OrderItemInterface as MageOrderItem;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Core\Test\BaseIntegrationTest;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Service\Sale\Request\AccountPv as SaleAccountPvRequest;
use Praxigento\Pv\Service\Sale\Request\Save as SaleSaveRequest;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Main_IntegrationTest extends BaseIntegrationTest
{
    const ATTR_CUSTOMER_EMAIL = Cfg::E_COMMON_A_ENTITY_ID;
    const ATTR_ITEM_ID = 'item_id';
    const ATTR_ITEM_ORDER_ID = 'order_id';
    const ATTR_ORDER_ID = Cfg::E_COMMON_A_ENTITY_ID;
    const DATA_EMAIL = 'some_customer_email@test.com';
    const DATA_PV_TOTAL = 400;
    /** @var \Praxigento\Pv\Service\Sale\Call */
    private $_callSale;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoBasic;
    private $customerId;
    private $operationId;
    private $orderId;
    private $orderItemsIds = [];
    private $prodIds = [];
    private $stockItemIds = [];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        /* services */
        $this->_repoBasic = $this->_manObj->get(\Praxigento\Core\Repo\IGeneric::class);
        /* services */
        $this->_callSale = $this->_manObj->get(\Praxigento\Pv\Service\ISale::class);
    }

    private function _accountPv()
    {
        $req = new SaleAccountPvRequest();
        $req->setCustomerId($this->customerId);
        $req->setSaleOrderId($this->orderId);
        $resp = $this->_callSale->accountPv($req);
        $this->operationId = $resp->getOperationId();
        $this->assertTrue($resp->isSucceed());
        $this->_logger->debug("PV for order #{$this->orderId} is accounted as for paid sale order as operation #{$this->operationId}.");
    }

    private function _checkOperation()
    {
        $data = $this->_repoBasic->getEntityByPk(
            Transaction::ENTITY_NAME,
            [
                Transaction::ATTR_OPERATION_ID => $this->operationId
            ]
        );
        $pvAccounted = isset($data[Transaction::ATTR_VALUE]) ? $data[Transaction::ATTR_VALUE] : null;
        $this->assertEquals(self::DATA_PV_TOTAL, $pvAccounted);
        $this->_logger->debug("Total '$pvAccounted' PV is accounted for the order #{$this->orderId}.");
    }

    private function _createMageCustomer()
    {
        $tblCustomer = $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        /* add new customer */
        $this->_conn->insert(
            $tblCustomer,
            [
                self::ATTR_CUSTOMER_EMAIL => self::DATA_EMAIL
            ]
        );
        $this->customerId = $this->_conn->lastInsertId($tblCustomer);
        $this->_logger->debug("New customer #{$this->customerId} is added to Magento.");
    }

    private function _createMageProducts()
    {
        $tblProd = $this->_resource->getTableName(Cfg::ENTITY_MAGE_PRODUCT);
        /* add new 2 products */
        for ($i = 0; $i < 2; $i++) {
            $this->_conn->insert(
                $tblProd,
                [
                    MageProd::SKU => 'unit_test_product_' . $i,
                    MageProd::ATTRIBUTE_SET_ID => 4 // default ATTR SET for product in the empty DB
                ]
            );
            $this->prodIds[$i] = $this->_conn->lastInsertId($tblProd);
            $this->_logger->debug("New product #{$this->prodIds[$i]} is added to Magento.");
        }
    }

    private function _createMageSaleOrder()
    {
        $tblOrder = $this->_resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER);
        $tblOrderItem = $this->_resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM);
        /* add new sale order*/
        $this->_conn->insert(
            $tblOrder,
            [
                Cfg::E_SALE_ORDER_A_CUSTOMER_ID => $this->customerId
            ]
        );
        $this->orderId = $this->_conn->lastInsertId($tblOrder);
        $this->_logger->debug("New order #{$this->orderId} is added to Magento customer #{$this->customerId}.");
        /* add 2 new sale order items */
        for ($i = 0; $i < 2; $i++) {
            $this->_conn->insert(
                $tblOrderItem,
                [
                    MageOrderItem::ORDER_ID => $this->orderId,
                    MageOrderItem::PRODUCT_ID => $this->prodIds[$i]
                ]
            );
            $this->orderItemsIds[$i] = $this->_conn->lastInsertId($tblOrderItem);
            $this->_logger->debug("New order item #{$this->orderItemsIds[$i]} is added to Magento order #{$this->orderId}.");
        }
    }

    private function _createWarehousePv()
    {
        $tblStockItem = $this->_resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        /** @var \Praxigento\Warehouse\Repo\Entity\Stock\IItem $repoWrhs */
        $repoWrhs = $this->_manObj->get(\Praxigento\Warehouse\Repo\Entity\Stock\IItem::class);
        /** @var \Praxigento\Pv\Repo\Entity\Stock\Item $repo */
        $repoPv = $this->_manObj->get(\Praxigento\Pv\Repo\Entity\Stock\Item::class);
        for ($i = 0; $i < 2; $i++) {
            /* add stock item */
            $this->_conn->insert(
                $tblStockItem,
                [
                    MageStockItem::PRODUCT_ID => $this->prodIds[$i],
                    MageStockItem::STOCK_ID => 1 // default stock (exists in empty DB)
                ]
            );
            $this->stockItemIds[$i] = $this->_conn->lastInsertId($tblStockItem);
            /* add warehouse stock item data */
            $data = new \Praxigento\Warehouse\Data\Entity\Stock\Item();
            $data->setStockItemRef($this->stockItemIds[$i]);
            $data->setPrice(10.20);
            $repoWrhs->create($data);
            /* add pv stock item data */
            $data = new \Praxigento\Pv\Data\Entity\Stock\Item();
            $data->setStockItemRef($this->stockItemIds[$i]);
            $data->setPv(200);
            $repoPv->create($data);
        }
    }

    private function _savePv()
    {
        $orderId = $this->orderId;
        $item0 = new \Praxigento\Pv\Service\Sale\Data\Item();
        $item0->setItemId($this->orderItemsIds[0]);
        $item0->setProductId($this->prodIds[0]);
        $item0->setStockId(1);
        $item0->setQuantity(1);
        $item1 = new \Praxigento\Pv\Service\Sale\Data\Item();
        $item1->setItemId($this->orderItemsIds[1]);
        $item1->setProductId($this->prodIds[1]);
        $item1->setStockId(1);
        $item1->setQuantity(1);
        $items = [$item0, $item1];
        $req = new SaleSaveRequest();
        $req->setSaleOrderId($orderId);
        $req->setOrderItems($items);
        $resp = $this->_callSale->save($req);
        $this->assertTrue($resp->isSucceed());
        $this->_logger->debug("PV attributes for order #{$this->orderId} are saved.");
    }


    public function test_main()
    {
        $this->_logger->debug('Story01 in PV Integration tests is started.');
        $this->_callSale->cacheReset();
        $this->_conn->beginTransaction();
        try {
            $this->_createMageCustomer();
            $this->_createMageProducts();
            $this->_createWarehousePv();
            $this->_createMageSaleOrder();
            $this->_savePv();
            $this->_accountPv();
            $this->_checkOperation();
        } finally {
            //         $this->_conn->commit();
            $this->_conn->rollBack();
        }
        $this->_logger->debug('Story01 in PV Integration tests is completed, all transactions are rolled back.');
    }
}