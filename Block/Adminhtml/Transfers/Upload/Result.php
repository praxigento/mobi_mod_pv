<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Block\Adminhtml\Transfers\Upload;

/**
 * Block with results of the processing the PV Transfers Batch.
 */
class Result
    extends \Magento\Backend\Block\Template
{
    /** @var int */
    private $batchId;
    /** @var bool */
    private $isBatchMissed;
    /** @var int */
    private $operationId;
    /** @var bool */
    private $succeed;

    public function getBatchId()
    {
        return $this->batchId;
    }

    public function setBatchId($data)
    {
        $this->batchId = $data;
    }

    public function getIsBatchMissed()
    {
        return $this->isBatchMissed;
    }

    public function setIsBatchMissed($data)
    {
        $this->isBatchMissed = $data;
    }

    public function getIsSucceed()
    {
        return $this->succeed;
    }

    public function getOperationId()
    {
        return $this->operationId;
    }

    public function setOperationId($data)
    {
        $this->operationId = $data;
    }

    public function setIsSucceed($data)
    {
        $this->succeed = $data;
    }
}