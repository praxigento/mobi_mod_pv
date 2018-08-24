<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Batch\Transfer\Save\A;

use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;
use Praxigento\Pv\Repo\Data\Trans\Batch as EBatch;
use Praxigento\Pv\Repo\Data\Trans\Batch\Item as EBatchItem;
use Praxigento\Pv\Service\Batch\Transfer\Save\A\Data\Item as DItem;

/**
 * Analyze parsed CSV entries, prepare transfers batch data & save it to DB.
 */
class ProcessItems
{
    /** @var \Praxigento\Pv\Repo\Dao\Trans\Batch */
    private $daoBatch;
    /** @var \Praxigento\Pv\Repo\Dao\Trans\Batch\Item */
    private $daoBatchItem;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Downline\Api\Helper\Tree */
    private $hlpTree;
    /** @var \Magento\Backend\Model\Auth\Session */
    private $sessAdmin;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $sessAdmin,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Pv\Repo\Dao\Trans\Batch $daoBatch,
        \Praxigento\Pv\Repo\Dao\Trans\Batch\Item $daoBatchItem,
        \Praxigento\Downline\Api\Helper\Tree $hlpTree
    ) {
        $this->sessAdmin = $sessAdmin;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->daoBatch = $daoBatch;
        $this->daoBatchItem = $daoBatchItem;
        $this->hlpTree = $hlpTree;
    }

    /**
     * Remove all existing batches for currently logged in admin user.
     *
     * @return \Praxigento\Pv\Repo\Data\Trans\Batch
     * @throws \Exception
     */
    private function cleanBatches($userId)
    {
        $where = EBatch::A_USER_REF . '=' . (int)$userId;
        $result = $this->daoBatch->delete($where);
        return $result;
    }

    /**
     * Register new batch for currently logged in admin user.
     *
     * @return \Praxigento\Pv\Repo\Data\Trans\Batch
     * @throws \Exception
     */
    private function createBatch($userId)
    {
        $entity = new EBatch();
        $entity->setUserRef($userId);
        $result = $this->daoBatch->create($entity);
        return $result;
    }

    /**
     * @param DItem[] $items
     */
    public function exec($items)
    {
        $user = $this->sessAdmin->getUser();
        $userId = $user->getId();

        $this->cleanBatches($userId);
        $batchId = $this->createBatch($userId);

        $map = $this->getMapByMlmId();
        foreach ($items as $item) {
            $mlmIdFrom = $item->from;
            $mlmIdTo = $item->to;
            $value = $item->value;

            $idFrom = $map[$mlmIdFrom];
            $idTo = $map[$mlmIdTo];
            $amount = abs($value);

            $entity = new EBatchItem();
            $entity->setBatchRef($batchId);
            $entity->setCustFromRef($idFrom);
            $entity->setCustToRef($idTo);
            $entity->setValue($amount);

            $this->daoBatchItem->create($entity);
        }
    }

    private function getMapByMlmId()
    {
        $all = $this->daoDwnlCust->get();
        $result = $this->hlpTree->mapValueById($all, EDwnlCust::A_MLM_ID, EDwnlCust::A_CUSTOMER_ID);
        return $result;
    }
}