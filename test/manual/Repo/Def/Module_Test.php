<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Def;


use Magento\Framework\App\ObjectManager;
use Praxigento\Pv\Data\Entity\Sale;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Module_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Module */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $repoDownlineCustomer = ObjectManager::getInstance()->get(\Praxigento\Downline\Repo\Entity\ICustomer::class);
        /* create object to test */
        $this->obj = new Module(
            $repoDownlineCustomer
        );
    }

    public function test_getDownlineCustomerById()
    {
        $res = $this->obj->getDownlineCustomerById(99);
        $this->assertNotNull($res);
    }

}