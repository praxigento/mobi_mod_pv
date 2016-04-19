<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Entity\Sale\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Item_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mRepoBasic;
    /** @var  Item */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mRepoBasic = $this->_mockRepoBasic();
        /* create object to test */
        $this->obj = new Item(
            $this->mRepoBasic
        );
    }

    public function test_constructor()
    {
        /* === Call and asserts  === */
        $this->assertInstanceOf(Item::class, $this->obj);
    }

}