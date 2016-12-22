<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Service\Transfer;



include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseCase\Mockery {

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
        $req->set(Request\BetweenCustomers::FROM_CUSTOMER_ID, 1);
        $req->set(Request\BetweenCustomers::TO_CUSTOMER_ID, 2);
        $req->set(Request\BetweenCustomers::VALUE, 25.43);
        $req->set(Request\BetweenCustomers::DATE_APPLIED, $formattedNow);
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
        $req->set(Request\CreditToCustomer::TO_CUSTOMER_ID, 4);
        $req->set(Request\CreditToCustomer::VALUE, 23);
        $req->set(Request\CreditToCustomer::DATE_APPLIED, $formattedNow);
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
        $req->set(Request\DebitFromCustomer::FROM_CUSTOMER_ID, 2);
        $req->set(Request\DebitFromCustomer::VALUE, 127.15);
        $req->set(Request\DebitFromCustomer::DATE_APPLIED, $formattedNow);
        /** @var  $resp Response\DebitFromCustomer */
        $resp = $call->debitFromCustomer($req);
        $this->assertTrue($resp->isSucceed());
    }
}