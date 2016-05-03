<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer;



include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase {

    public function test_betweenCustomers() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $toolbox \Praxigento\Core\IToolbox */
        $toolbox = $obm->get('Praxigento\Core\Lib\IToolbox');
        $toolDate = $toolbox->getDate();
        $toolFormat = $toolbox->getFormat();
        /** Test data  */
        $dateNow = $toolDate->getUtcNow();
        $formattedNow = $toolFormat->dateTimeForDb($dateNow);
        /** @var  $call Call */
        $call = $obm->get('Praxigento\Pv\Service\Transfer\Call');
        $req = new Request\BetweenCustomers();
        $req->setData(Request\BetweenCustomers::FROM_CUSTOMER_ID, 1);
        $req->setData(Request\BetweenCustomers::TO_CUSTOMER_ID, 2);
        $req->setData(Request\BetweenCustomers::VALUE, 25.43);
        $req->setData(Request\BetweenCustomers::DATE_APPLIED, $formattedNow);
        /** @var  $resp Response\BetweenCustomers */
        $resp = $call->betweenCustomers($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_creditToCustomer() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $toolbox \Praxigento\Core\Lib\IToolbox */
        $toolbox = $obm->get('Praxigento\Core\Lib\IToolbox');
        $toolDate = $toolbox->getDate();
        $toolFormat = $toolbox->getFormat();
        /** Test data  */
        $dateNow = $toolDate->getUtcNow();
        $formattedNow = $toolFormat->dateTimeForDb($dateNow);
        /** @var  $call Call */
        $call = $obm->get('Praxigento\Pv\Service\Transfer\Call');
        $req = new Request\CreditToCustomer();
        $req->setData(Request\CreditToCustomer::TO_CUSTOMER_ID, 4);
        $req->setData(Request\CreditToCustomer::VALUE, 23);
        $req->setData(Request\CreditToCustomer::DATE_APPLIED, $formattedNow);
        /** @var  $resp Response\CreditToCustomer */
        $resp = $call->creditToCustomer($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_debitFromCustomer() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $toolbox \Praxigento\Core\Lib\IToolbox */
        $toolbox = $obm->get('Praxigento\Core\Lib\IToolbox');
        $toolDate = $toolbox->getDate();
        $toolFormat = $toolbox->getFormat();
        /** Test data  */
        $dateNow = $toolDate->getUtcNow();
        $formattedNow = $toolFormat->dateTimeForDb($dateNow);
        /** @var  $call Call */
        $call = $obm->get('Praxigento\Pv\Service\Transfer\Call');
        $req = new Request\DebitFromCustomer();
        $req->setData(Request\DebitFromCustomer::FROM_CUSTOMER_ID, 2);
        $req->setData(Request\DebitFromCustomer::VALUE, 127.15);
        $req->setData(Request\DebitFromCustomer::DATE_APPLIED, $formattedNow);
        /** @var  $resp Response\DebitFromCustomer */
        $resp = $call->debitFromCustomer($req);
        $this->assertTrue($resp->isSucceed());
    }
}