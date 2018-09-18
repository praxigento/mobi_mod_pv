<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Pv\Ui\DataProvider\Grid\Transfers;


include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class QueryTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{


    public function test_build()
    {
        /** @var \Magento\Framework\Api\Search\SearchCriteriaInterface $search */
        $search = $this->_manObj->get(\Magento\Framework\Api\Search\SearchCriteriaInterface::class);
        /** @var \Praxigento\Pv\Ui\DataProvider\Grid\Transfers\Query $builder */
        $builder = $this->_manObj->get(\Praxigento\Pv\Ui\DataProvider\Grid\Transfers\Query::class);
        $items = $builder->getItems($search);
        $this->assertTrue(true);
    }
}