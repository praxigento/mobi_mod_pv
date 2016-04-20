<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Entity\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Sale_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  Sale */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mConn = $this->_mockConn();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        /* setup mocks for constructor */
        // parent::__construct($resource);
        $mResource = $this->_mockResourceConnection($this->mConn);
        /* create object to test */
        $this->obj = new Sale(
            $mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /* === Call and asserts  === */
        $this->assertInstanceOf(Sale::class, $this->obj);
    }

}