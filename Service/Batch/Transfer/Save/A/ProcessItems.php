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
    /** @var \Praxigento\Pv\Api\Helper\Validate\Transfer */
    private $hlpValidTrans;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $sessAdmin,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Pv\Repo\Dao\Trans\Batch $daoBatch,
        \Praxigento\Pv\Repo\Dao\Trans\Batch\Item $daoBatchItem,
        \Praxigento\Pv\Api\Helper\Validate\Transfer $hlpValidTrans,
        \Praxigento\Downline\Api\Helper\Tree $hlpTree
    ) {
        $this->sessAdmin = $sessAdmin;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->daoBatch = $daoBatch;
        $this->daoBatchItem = $daoBatchItem;
        $this->hlpValidTrans = $hlpValidTrans;
        $this->hlpTree = $hlpTree;
    }

    /**
     * Remove all existing batches for currently logged in admin user.
     *
     * @return int
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
     * @return int
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
     * @return array [$batchId, $senderErrors, $receiverErrors]
     */
    public function exec($items)
    {
        $user = $this->sessAdmin->getUser();
        $userId = $user->getId();

        $this->cleanBatches($userId);
        $batchId = $this->createBatch($userId);
        $senderErrors = $receiverErrors = [];

        $map = $this->getMapByMlmId();
        foreach ($items as $item) {
            $mlmIdFrom = $item->from;
            $mlmIdTo = $item->to;
            $value = $item->value;

            /* errors flag for one iteration */
            $errors = false;
            if (isset($map[$mlmIdFrom])) {
                $idFrom = $map[$mlmIdFrom];
            } else {
                $senderErrors[] = $mlmIdFrom;
                $errors = true;
            }
            if (isset($map[$mlmIdTo])) {
                $idTo = $map[$mlmIdTo];
            } else {
                $receiverErrors[] = $mlmIdTo;
                $errors = true;
            }

            if (!$errors) {
                $amount = abs($value);

                $entity = new EBatchItem();
                $entity->setBatchRef($batchId);
                $entity->setCustFromRef($idFrom);
                $entity->setCustToRef($idTo);
                $entity->setValue($amount);

                $restricted = $this->hlpValidTrans->isRestricted($entity);
                $entity->setRestricted($restricted);

                $this->daoBatchItem->create($entity);
            }
        }
        return [$batchId, $senderErrors, $receiverErrors];
    }

    private function getMapByMlmId()
    {
        $all = $this->daoDwnlCust->get();
        $result = $this->hlpTree->mapValueById($all, EDwnlCust::A_MLM_ID, EDwnlCust::A_CUSTOMER_ID);
        return $result;
    }
}