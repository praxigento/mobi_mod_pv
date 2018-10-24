<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Controller\Adminhtml\Transfers\Upload;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Data\Trans\Batch as EBatch;
use Praxigento\Pv\Service\Batch\Transfer\Process\Request as ARequest;
use Praxigento\Pv\Service\Batch\Transfer\Process\Response as AResponse;

/**
 * Create operation with transactions and clean up batch items.
 */
class Process
    extends \Praxigento\Core\App\Action\Back\Base
{
    const BLOCK = 'prxgt_pv_transfer_result';

    /** @var \Praxigento\Pv\Repo\Dao\Trans\Batch */
    private $daoBatch;
    /** @var \Praxigento\Pv\Helper\BatchIdStore */
    private $hlpBatchIdStore;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Pv\Service\Batch\Transfer\Process */
    private $servProcess;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Pv\Repo\Dao\Trans\Batch $daoBatch,
        \Praxigento\Pv\Helper\BatchIdStore $hlpBatchIdStore,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Pv\Service\Batch\Transfer\Process $servProcess
    ) {
        $aclResource = Cfg::MODULE . '::' . Cfg::ACL_TRANSFERS_UPLOAD;
        $activeMenu = Cfg::MODULE . '::' . Cfg::MENU_TRANSFERS_UPLOAD;
        $breadcrumbLabel = 'PV Transfers Result';
        $breadcrumbTitle = 'PV Transfers Result';
        $pageTitle = 'PV Transfers Result';
        parent::__construct(
            $context,
            $aclResource,
            $activeMenu,
            $breadcrumbLabel,
            $breadcrumbTitle,
            $pageTitle
        );
        $this->logger = $logger;
        $this->daoBatch = $daoBatch;
        $this->hlpBatchIdStore = $hlpBatchIdStore;
        $this->hlpDate = $hlpDate;
        $this->servProcess = $servProcess;
    }

    private function cleanBatchFromDb($batchId)
    {
        $where = EBatch::A_ID . '=' . (int)$batchId;
        $result = $this->daoBatch->delete($where);
        return $result;
    }

    public function execute()
    {
        /** define local working data */
        $result = parent::execute();
        $layout = $result->getLayout();
        /** @var \Praxigento\Pv\Block\Adminhtml\Transfers\Upload\Result $block */
        $block = $layout->getBlock(self::BLOCK);

        /** perform processing */
        $batchId = $this->hlpBatchIdStore->restoreBatchId();
        $block->setBatchId($batchId);
        $block->setIsSucceed(false);
        if ($batchId) {
            /* PV transfer for current month */
            $dateApplied = $this->hlpDate->getUtcNowForDb();
            $req = new ARequest();
            $req->setBatchId($batchId);
            $req->setDateApplied($dateApplied);

            /** @var AResponse $resp */
            $resp = $this->servProcess->exec($req);
            /* analyze service response */
            if ($resp->isSucceed()) {
                $operId = $resp->getOperationId();
                $block->setIsSucceed(true);
                $block->setOperationId($operId);
                $this->cleanBatchFromDb($batchId);
                $this->hlpBatchIdStore->cleanBatchId();
            }
        } else {
            $block->setIsBatchMissed(true);
        }
        return $result;
    }
}