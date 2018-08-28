<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Controller\Adminhtml\Transfers\Upload;

use Magento\Framework\App\Filesystem\DirectoryList as ADirList;
use Praxigento\Pv\Service\Batch\Transfer\Save\Request as ARequest;

/**
 * Save uploaded CSV file to disk, then parse items and save to DB to process it later.
 */
class Save
    extends \Magento\Backend\App\Action
{
    const FIELDSET = 'pv_transfers_upload';
    const FIELD_CSV_FILE = 'csv_file';
    const MEDIA_SUBFOLDER = 'uploader/';

    /** @var \Magento\MediaStorage\Model\File\UploaderFactory */
    private $factUploader;
    /** @var \Magento\Framework\Filesystem */
    private $filesystem;
    /** @var \Praxigento\Pv\Service\Batch\Transfer\Save */
    private $srvSave;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $factUploader,
        \Praxigento\Pv\Service\Batch\Transfer\Save $srvSave
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->factUploader = $factUploader;
        $this->srvSave = $srvSave;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        try {
            /* save uploaded file into 'media' folder */
            $data = $this->saveFile();
            /* parse uploaded file then remove it */
            $fullpath = $data['path'] . $data['file'];
            $this->placeToDb($fullpath);
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
        $req = new ARequest();
        $req->setFile($fullpath);
        /** @var AResponse $resp */
        $resp = $this->srvSave->exec($req);
        /* we don't use service response yet */
    }

    private function removeFile($fullpath)
    {
        $dirMedia = $this->filesystem->getDirectoryWrite(ADirList::MEDIA);
        $dirMedia->delete($fullpath);
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
        return $result;
    }
}