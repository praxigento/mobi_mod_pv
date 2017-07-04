<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Product_Test
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_get()
    {
        /** @var \Praxigento\Pv\Repo\Entity\Def\Product $obj */
        $obj = $this->manObj->create(\Praxigento\Pv\Repo\Entity\Def\Product::class);
        $res = $obj->get();
        $this->assertNotNull($res);
    }

}