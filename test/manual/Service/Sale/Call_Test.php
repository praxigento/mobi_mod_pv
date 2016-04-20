<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Sale;


use Praxigento\Pv\Data\Entity\Sale;
use Praxigento\Pv\Data\Entity\Sale\Item as SaleItem;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase {

    public function test_save() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call Call */
        $call = $obm->get('Praxigento\Pv\Lib\Service\Sale\Call');
        $req = new Request\Save();
        $req->data = [
            Sale::ATTR_SALE_ID       => 1,
            Sale::ATTR_SUBTOTAL      => 500,
            Sale::ATTR_DISCOUNT      => 50,
            Sale::ATTR_TOTAL         => 450,
            Request\Save::DATA_ITEMS => [
                1 => [
                    SaleItem::ATTR_SALE_ITEM_ID => 1,
                    Sale::ATTR_SUBTOTAL         => 250,
                    Sale::ATTR_DISCOUNT         => 50,
                    Sale::ATTR_TOTAL            => 200,
                ],
                2 => [
                    SaleItem::ATTR_SALE_ITEM_ID => 2,
                    Sale::ATTR_SUBTOTAL         => 250,
                    Sale::ATTR_DISCOUNT         => 0,
                    Sale::ATTR_TOTAL            => 250,
                ]
            ]
        ];
        /** @var  $resp Response\Save */
        $resp = $call->save($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_accountPv() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call Call */
        $call = $obm->get('Praxigento\Pv\Lib\Service\Sale\Call');
        $req = new Request\AccountPv();
        $req->setSaleOrderId(1);
        /** @var  $resp Response\AccountPv */
        $resp = $call->accountPv($req);
        $this->assertTrue($resp->isSucceed());
    }
}