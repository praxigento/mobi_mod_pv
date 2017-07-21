<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Product_Test
    extends \Praxigento\Core\Test\BaseCase\Manual
{

    public function test_create()
    {
        /** @var \Praxigento\Pv\Repo\Entity\Product $obj */
        $obj = $this->manObj->create(\Praxigento\Pv\Repo\Entity\Product::class);

        $data = [\Praxigento\Pv\Data\Entity\Product::ATTR_PV => 54.34,
            \Praxigento\Pv\Data\Entity\Product::ATTR_PROD_REF => 5];

        $do = new \Flancer32\Lib\Data();
        $do->set(\Praxigento\Pv\Data\Entity\Product::ATTR_PV, 54.34);
        $do->set(\Praxigento\Pv\Data\Entity\Product::ATTR_PROD_REF, 5);

        $product = new \Praxigento\Pv\Data\Entity\Product();
        $product->setProductRef(5);
        $product->setPv(5.3500);
        $res = $obj->create($product);
        $this->assertNotNull($data);
        $this->assertNotNull($do);
        $this->assertNotNull($product);
        $this->assertNotNull($res);
    }

    public function test_delete()
    {
        /** @var \Praxigento\Pv\Repo\Entity\Product $obj */
        $obj = $this->manObj->create(\Praxigento\Pv\Repo\Entity\Product::class);
        $where = "prod_ref = 5";
        $deleted_rows = $obj->delete($where);
        $this->assertNotNull($deleted_rows);
    }

    public function test_get()
    {
        /** @var \Praxigento\Pv\Repo\Entity\Product $obj */
        $obj = $this->manObj->create(\Praxigento\Pv\Repo\Entity\Product::class);
        /** @var \Praxigento\Pv\Data\Entity\Product[] $res */
        $res = $obj->get();
        /** @var \Praxigento\Pv\Data\Entity\Product $product */
        $product = $res[0];
        $pv = $product->getPv();
        $this->assertNotNull($res);
        $this->assertNotNull($pv);
    }

    public function test_getById()
    {
        /** @var \Praxigento\Pv\Repo\Entity\Product $obj */
        $obj = $this->manObj->create(\Praxigento\Pv\Repo\Entity\Product::class);
        /** @var \Praxigento\Pv\Data\Entity\Product $product */
        $product = $obj->getById(2);
        $pv = $product->getPv();
        $prodref = $product->getProductRef();
        $this->assertNotNull($prodref);
        $this->assertNotNull($pv);
    }

    public function test_repo()
    {
        /* get transaction manager & begin database transaction */
        /** @var \Praxigento\Core\Transaction\Database\IManager $manTrans */
        $manTrans = $this->manObj->get(\Praxigento\Core\Transaction\Database\IManager::class);
        $trans = $manTrans->begin();
        try {
            /* perform DB operations */
            /** @var \Praxigento\Pv\Repo\Entity\Product $obj */
            $obj = $this->manObj->create(\Praxigento\Pv\Repo\Entity\Product::class);

            /* get all entities from repo */
            $rows = $obj->get();
            /** @var \Praxigento\Pv\Data\Entity\Product $row */
            $row = reset($rows);
            if (!($row instanceof \Praxigento\Pv\Data\Entity\Product)) {
                $row = new \Praxigento\Pv\Data\Entity\Product($row);
            }
            $prodId = $row->getProductRef();
            $pv = $row->getPv();

            /* update entity  */
            $row->setPv($pv + 5);
            $obj->updateById($prodId, $row);

            /* delete entity */
            $obj->deleteById($prodId);

            /* validate deletion */
            $res = $obj->getById($prodId);
            $this->assertFalse($res);

            /* create entity and validate */
            $obj->create($row);
            $res = $obj->getById($prodId);
            $this->assertTrue($res !== false);

        } finally {
            /* rollback changes using transaction definition */
            $manTrans->rollback($trans);
        }
    }

    public function test_updateById()
    {
        /** @var \Praxigento\Pv\Repo\Entity\Product $obj */
        $obj = $this->manObj->create(\Praxigento\Pv\Repo\Entity\Product::class);
        $product = new \Praxigento\Pv\Data\Entity\Product();
        $product->setPv(21.347);
        $res = $obj->updateById(5, $product);
        $this->assertNotNull($res);
    }

}