<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Controller\Adminhtml\Transfers\Upload;

use Magento\Framework\App\Filesystem\DirectoryList as ADirList;
use Praxigento\Pv\Service\Batch\Transfer\Save\Request as ARequest;
use Praxigento\Pv\Service\Batch\Transfer\Save\Response as AResponse;

/**
 * Save uploaded CSV file to disk, then parse items and save to DB to process it later.
 */
class Save
    extends \Magento\Backend\App\Action
{
    const ERR_VALIDATION = 100;
    const FIELDSET = 'pv_transfers_upload';
    const FIELD_CSV_FILE = 'csv_file';
    const MEDIA_SUBFOLDER = 'uploader/';

    /** @var \Magento\MediaStorage\Model\File\UploaderFactory */
    private $factUploader;
    /** @var \Magento\Framework\Filesystem */
    private $filesystem;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Pv\Service\Batch\Transfer\Save */
    private $srvSave;
    /** @var \Praxigento\Pv\Helper\BatchIdStore  */
    private $hlpBatchIdStore;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $factUploader,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Pv\Service\Batch\Transfer\Save $srvSave,
        \Praxigento\Pv\Helper\BatchIdStore $hlpBatchIdStore
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->factUploader = $factUploader;
        $this->logger = $logger;
        $this->srvSave = $srvSave;
        $this->hlpBatchIdStore = $hlpBatchIdStore;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        try {
            /* save uploaded file into 'media' folder */
            $data = $this->saveFile();
            /* parse uploaded file then remove it */
            $fullpath = $data['path'] . $data['file'];
            $errors = $this->placeToDb($fullpath);
            if (!empty($errors)) {
                $errors = str_replace("\n", "<br/>", $errors);
                $errors = str_replace("\t", "&nbsp;&nbsp;", $errors);
                $data = ['error' => $errors, 'errorcode' => self::ERR_VALIDATION];
            }
            $this->removeFile($fullpath);
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /* return uploaded file data to UI for preview */
        $result->setData($data);
        return $result;
    }

    private function placeToDb($fullpath)
    {
        $result = '';
        $req = new ARequest();
        $req->setFile($fullpath);
        /** @var AResponse $resp */
        $resp = $this->srvSave->exec($req);
        $batchId = $resp->getBatchId();
        $senderErrors = $resp->getSenderMlmIdError();
        $receiverErrors = $resp->getReceiverMlmIdError();

        /* log batch ID and save it to further processin (in next controllers) */
        $this->logger->info("New batch #$batchId is created for uploaded CSV file.");
        $this->hlpBatchIdStore->saveBatchId($batchId);

        /* analyze response and compose error message for logs & admin UI */
        if (is_array($senderErrors) && count($senderErrors)) {
            $result .= "Unknown sender MLM ID:\n";
            foreach ($senderErrors as $one) {
                $result .= "\t$one\n";
            }
        }
        if (is_array($receiverErrors) && count($receiverErrors)) {
            $result .= "Unknown receiver MLM ID:\n";
            foreach ($receiverErrors as $one) {
                $result .= "\t$one\n";
            }
        }
        if (!empty($result)) {
            $this->logger->error($result);
        }
        return $result;
    }

    private function removeFile($fullpath)
    {
        $dirMedia = $this->filesystem->getDirectoryWrite(ADirList::MEDIA);
        $dirMedia->delete($fullpath);
        $this->logger->info("Uploaded file '$fullpath' is removed after processing.");
    }

    /**
     * Save uploading file to the 'media' folder
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function saveFile()
    {
        $dirMedia = $this->filesystem->getDirectoryWrite(ADirList::MEDIA);
        $dirTarget = $dirMedia->getAbsolutePath(self::MEDIA_SUBFOLDER);
        $fileId = self::FIELDSET . '[' . self::FIELD_CSV_FILE . ']';
        $uploader = $this->factUploader->create(['fileId' => $fileId]);
        $uploader->setAllowRenameFiles(true);
        $result = $uploader->save($dirTarget);
        if (isset($result['path']) && isset($result['file'])) {
            $fullpath = $result['path'] . $result['file'];
            $this->logger->info("Uploaded file is saved into '$fullpath'.");
        }
        return $result;
    }
}