<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Test\Story01;

use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Core\Lib\Context;
use Praxigento\Core\Lib\Service\Repo\Request\GetEntityByPk as RepoGetEntityByPkRequest;
use Praxigento\Core\Lib\Test\BaseIntegrationTest;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Lib\Entity\Sale;
use Praxigento\Pv\Lib\Entity\Sale\Item as SaleItem;
use Praxigento\Pv\Lib\Service\Sale\Request\AccountPv as SaleAccountPvRequest;
use Praxigento\Pv\Lib\Service\Sale\Request\Save as SaleSaveRequest;
use Praxigento\Pv\Lib\Service\Sale\Response\AccountPv as SaleAccountPvResponse;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class Main_IntegrationTest extends BaseIntegrationTest {
    const ATTR_CUSTOMER_EMAIL = Cfg::E_COMMON_A_ENTITY_ID;
    const ATTR_ITEM_ID = 'item_id';
    const ATTR_ITEM_ORDER_ID = 'order_id';
    const ATTR_ORDER_ID = Cfg::E_COMMON_A_ENTITY_ID;
    const DATA_EMAIL = 'some_customer_email@test.com';
    const DATA_PV_TOTAL = 300;
    /** @var \Praxigento\Core\Lib\Service\IRepo */
    private $_callRepo;
    /** @var \Praxigento\Pv\Lib\Service\Sale\Call */
    private $_callSale;
    private $customerId;
    private $operationId;
    private $orderId;
    private $orderItemsIds = [ ];

    /**
     * Main_FunctionalTest constructor.
     */
    public function __construct() {
        parent::__construct();
        /* services */
        $this->_callRepo = $this->_obm->get(\Praxigento\Core\Lib\Service\IRepo::class);
        $this->_callSale = $this->_obm->get(\Praxigento\Pv\Lib\Service\ISale::class);
    }

    private function _accountPv() {
        $req = new SaleAccountPvRequest();
        $req->setCustomerId($this->customerId);
        $req->setSaleOrderId($this->orderId);
        $resp = $this->_callSale->accountPv($req);
        $this->operationId = $resp->getData(SaleAccountPvResponse::OPERATION_ID);
        $this->assertTrue($resp->isSucceed());
        $this->_logger->debug("PV for order #{$this->orderId} is accounted as for paid sale order as operation #{$this->operationId}.");
    }

    private function _checkOperation() {
        $req = new RepoGetEntityByPkRequest(Transaction::ENTITY_NAME, [ Transaction::ATTR_OPERATION_ID => $this->operationId ]);
        $resp = $this->_callRepo->getEntityByPk($req);
        $this->assertTrue($resp->isSucceed());
        $pvAccounted = $resp->getData(Transaction::ATTR_VALUE);
        $this->assertEquals(self::DATA_PV_TOTAL, $pvAccounted);
        $this->_logger->debug("Total '$pvAccounted' PV is accounted for the order #{$this->orderId}.");
    }

    private function _createMageCustomer() {
        $mappedCustomer = Context::getMappedEntityName(Cfg::ENTITY_MAGE_CUSTOMER);
        $tblCustomer = $this->_resource->getTableName($mappedCustomer);
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

    private function _createMageSaleOrder() {
        $mappedOrder = Context::getMappedEntityName(Cfg::ENTITY_MAGE_SALES_ORDER);
        $mappedOrderItem = Context::getMappedEntityName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM);
        $tblOrder = $this->_resource->getTableName($mappedOrder);
        $tblOrderItem = $this->_resource->getTableName($mappedOrderItem);
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
        for($i = 0; $i < 2; $i++) {
            $this->_conn->insert(
                $tblOrderItem,
                [
                    self::ATTR_ITEM_ORDER_ID => $this->orderId
                ]
            );
            $this->orderItemsIds[$i] = $this->_conn->lastInsertId($tblOrderItem);
            $this->_logger->debug("New order item #{$this->orderItemsIds[$i]} is added to Magento order #{$this->orderId}.");
        }
    }

    private function _savePv() {
        $orderId = $this->orderId;
        $orderItemFirstId = $this->orderItemsIds[0];
        $orderItemSecondId = $this->orderItemsIds[1];
        $data = [
            Sale::ATTR_SALE_ID          => $orderId,
            Sale::ATTR_SUBTOTAL         => 500,
            Sale::ATTR_DISCOUNT         => 50,
            Sale::ATTR_TOTAL            => 450,
            SaleSaveRequest::DATA_ITEMS => [
                $orderItemFirstId  => [
                    SaleItem::ATTR_SALE_ITEM_ID => $orderItemFirstId,
                    Sale::ATTR_SUBTOTAL         => 250,
                    Sale::ATTR_DISCOUNT         => 50,
                    Sale::ATTR_TOTAL            => 200,
                ],
                $orderItemSecondId => [
                    SaleItem::ATTR_SALE_ITEM_ID => $orderItemSecondId,
                    Sale::ATTR_SUBTOTAL         => 250,
                    Sale::ATTR_DISCOUNT         => 0,
                    Sale::ATTR_TOTAL            => 250,
                ]
            ]
        ];
        $req = new SaleSaveRequest();
        $req->setData($data);
        $resp = $this->_callSale->save($req);
        $this->assertTrue($resp->isSucceed());
        $this->_logger->debug("PV attributes for order #{$this->orderId} are saved.");
    }

    private function _updatePv() {
        $orderId = $this->orderId;
        $orderItemFirstId = $this->orderItemsIds[0];
        $orderItemSecondId = $this->orderItemsIds[1];
        $data = [
            Sale::ATTR_SALE_ID          => $orderId,
            Sale::ATTR_SUBTOTAL         => 400,
            Sale::ATTR_DISCOUNT         => 100,
            Sale::ATTR_TOTAL            => self::DATA_PV_TOTAL,
            SaleSaveRequest::DATA_ITEMS => [
                $orderItemFirstId  => [
                    SaleItem::ATTR_SALE_ITEM_ID => $orderItemFirstId,
                    Sale::ATTR_SUBTOTAL         => 200,
                    Sale::ATTR_DISCOUNT         => 50,
                    Sale::ATTR_TOTAL            => 150,
                ],
                $orderItemSecondId => [
                    SaleItem::ATTR_SALE_ITEM_ID => $orderItemSecondId,
                    Sale::ATTR_SUBTOTAL         => 200,
                    Sale::ATTR_DISCOUNT         => 50,
                    Sale::ATTR_TOTAL            => 150,
                ]
            ]
        ];
        $req = new SaleSaveRequest();
        $req->setData($data);
        $resp = $this->_callSale->save($req);
        $this->assertTrue($resp->isSucceed());
        $this->_logger->debug("PV attributes for order #{$this->orderId} are updated.");
    }

    public function test_main() {
        $this->_logger->debug('Story01 in PV Integration tests is started.');
        $this->_callSale->cacheReset();
        $this->_conn->beginTransaction();
        try {
            $this->_createMageCustomer();
            $this->_createMageSaleOrder();
            $this->_savePv();
            $this->_updatePv();
            $this->_accountPv();
            $this->_checkOperation();
        } finally {
            //         $this->_conn->commit();
            $this->_conn->rollBack();
        }
        $this->_logger->debug('Story01 in PV Integration tests is completed, all transactions are rolled back.');
    }
}