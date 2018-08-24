<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Controller\Adminhtml\Transfers\Upload;

use Magento\Framework\App\Filesystem\DirectoryList as ADirList;
use Praxigento\Pv\Service\Batch\Transfer\Save\Request as ARequest;

/**
 * Save uploaded CSV file to disk/db to process it later.
 */
class Save
    extends \Magento\Backend\App\Action
{
    const FIELDSET = \Praxigento\Santegra\Block\Adminhtml\Bonus\Overview\Report::FIELDSET;
    const FIELD_CSV_FILE = 'csv_file';

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
        $dirMedia = $this->filesystem->getDirectoryWrite(ADirList::MEDIA);
        $dirTarget = $dirMedia->getAbsolutePath('uploader/');
        $fileId = self::FIELDSET . '[' . self::FIELD_CSV_FILE . ']';
        $uploader = $this->factUploader->create(['fileId' => $fileId]);
        $uploader->setAllowRenameFiles(true);
        $saved = $uploader->save($dirTarget);
        $path = $saved['path'];
        $file = $saved['file'];

        $req = new ARequest();
        $req->setFile($path . $file);
        /** @var AResponse $resp */
        $resp = $this->srvSave->exec($req);
        return parent::execute();
    }

}