<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Sale;


use Praxigento\Pv\Data\Entity\Sale;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery
{

    public function test_accountPv()
    {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call Call */
        $call = $obm->get('Praxigento\Pv\Service\Sale\Call');
        $req = new Request\AccountPv();
        $req->setSaleOrderId(1);
        /** @var  $resp Response\AccountPv */
        $resp = $call->accountPv($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_save()
    {
        /** === Test Data === */
        $ORDER_ID = 2;
        $ITEMS = [];
        $ITEM = new \Praxigento\Pv\Service\Sale\Data\Item();
        $ITEM->setItemId(4);
        $ITEM->setProductId(1);
        $ITEM->setStockId(1);
        $ITEM->setQuantity(10);
        $ITEMS[] = $ITEM;
        /** === Call and asserts  === */
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call Call */
        $call = $obm->get(\Praxigento\Pv\Service\ISale::class);
        $req = new Request\Save();
        $req->setSaleOrderId($ORDER_ID);
        $req->setOrderItems($ITEMS);
        /** @var  $resp Response\Save */
        $resp = $call->save($req);
        $this->assertTrue($resp->isSucceed());
    }
}