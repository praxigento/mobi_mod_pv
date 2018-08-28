<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Controller\Adminhtml\Transfers\Upload\Z;


/**
 * Save restore batch ID between processing steps (controlles).
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
}