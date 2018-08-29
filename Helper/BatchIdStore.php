<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Helper;


/**
 * Save restore PV transfers batch ID between processing steps.
 */
class BatchIdStore
{
    const SESS_BATCH_ID = 'prxgtPvTransfersBatchId';
    /** @var \Magento\Backend\Model\Auth\Session */
    private $sessAdmin;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $sessAdmin
    ) {
        $this->sessAdmin = $sessAdmin;
    }

    public function restoreBatchId()
    {
        $result = $this->sessAdmin->getData(self::SESS_BATCH_ID);
        return $result;
    }

    public function saveBatchId($id)
    {
        $this->sessAdmin->setData(self::SESS_BATCH_ID, $id);
    }

    public function cleanBatchId()
    {
        $this->sessAdmin->getData(self::SESS_BATCH_ID, true);
    }
}