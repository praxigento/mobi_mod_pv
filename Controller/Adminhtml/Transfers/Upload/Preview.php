<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Controller\Adminhtml\Transfers\Upload;

use Praxigento\Pv\Config as Cfg;

/**
 * Process previously saved CSV items and compose report for UI.
 */
class Preview
    extends \Praxigento\Core\App\Action\Back\Base
{
    const FIELDSET = 'pv_transfers_upload';
    const FIELD_CSV_FILE = 'csv_file';
    const MEDIA_SUBFOLDER = 'uploader/';

    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Pv\Helper\BatchIdStore */
    private $hlpBatchIdStore;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Pv\Helper\BatchIdStore $hlpBatchIdStore
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_TRANSFERS_UPLOAD;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_TRANSFERS_UPLOAD;
        $breadcrumbLabel = 'PV Transfers Preview';
        $breadcrumbTitle = 'PV Transfers Preview';
        $pageTitle = 'PV Transfers Preview';
        parent::__construct(
            $context,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
        $this->logger = $logger;
        $this->hlpBatchIdStore = $hlpBatchIdStore;
    }

    public function execute()
    {
        $result = parent::execute();
        return $result;
    }

}