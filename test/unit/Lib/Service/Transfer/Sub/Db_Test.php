<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Transfer\Sub;


include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Db_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase {

    public function test_getDownlineCustomer() {
        /** === Test Data === */
        $CUSTOMER_ID = 43;
        $DOWNLINE_DATA = [ 'record' ];
        /** === Mocks === */
        $mLogger = $this->_mockLogger();
        $mConn = $this->_mockConnection();
        $mDba = $this->_mockDbAdapter(null, $mConn);
        $mToolbox = $this->_mockToolbox();
        $mCallRepo = $this->_mockCallRepo();

        // $resp = $this->_callRepo->getEntityByPk($req);
        $mRespByPk = new \Praxigento\Core\Lib\Service\Repo\Response\GetEntityByPk();
        $mRespByPk->setData($DOWNLINE_DATA);
        $mCallRepo
            ->expects($this->once())
            ->method('getEntityByPk')
            ->willReturn($mRespByPk);
        /**
         * Prepare request and perform call.
         */
        /** @var  $sub Db */
        $sub = new Db($mLogger, $mDba, $mToolbox, $mCallRepo);
        $data = $sub->getDownlineCustomer($CUSTOMER_ID);
        $this->assertTrue(is_array($data));
    }
}