<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Praxigento\Pv\Observer\Z;


include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class RegisterTest
    extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  \Praxigento\Pv\Observer\Z\Register */
    private $obj;
    /** @var  \Magento\Sales\Api\OrderRepositoryInterface */
    private $repoSaleOrder;

    protected function setUp()
    {
        $this->repoSaleOrder = $this->_manObj->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $this->obj = $this->_manObj->create(\Praxigento\Pv\Observer\Z\Register::class);
    }

    public function test_execute()
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $this->repoSaleOrder->get(89);
        $this->obj->accountPv($order);
    }
}