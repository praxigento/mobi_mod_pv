<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Controller\Adminhtml\Transfers\Upload;

/**
 * Process previously saved CSV items and compose report for UI.
 */
class Preview
    extends \Magento\Backend\App\Action
{
    const FIELDSET = 'pv_transfers_upload';
    const FIELD_CSV_FILE = 'csv_file';
    const MEDIA_SUBFOLDER = 'uploader/';

    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Pv\Controller\Adminhtml\Transfers\Upload\Z\BatchIdStore */
    private $zBatchIdStore;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Pv\Controller\Adminhtml\Transfers\Upload\Z\BatchIdStore $zBatchIdStore
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->zBatchIdStore = $zBatchIdStore;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        try {
            $batchId = $this->zBatchIdStore->restoreBatchId();
            $this->logger->info("Processing PV transfers batch #$batchId.");
            $data = ['result' => true];
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /* return uploaded file data to UI for preview */
        $result->setData($data);
        return $result;
    }

}