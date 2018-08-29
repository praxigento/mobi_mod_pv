<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Service\Batch\Transfer;

use Praxigento\Accounting\Api\Service\Operation\Request as AReqOper;
use Praxigento\Accounting\Api\Service\Operation\Response as ARespOper;
use Praxigento\Accounting\Repo\Data\Transaction as ETrans;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Service\Batch\Transfer\Process\Request as ARequest;
use Praxigento\Pv\Service\Batch\Transfer\Process\Response as AResponse;

/**
 * Process batch items saved in DB.
 */
class Process
{
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAcc;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoAssetType;
    /** @var \Praxigento\Pv\Repo\Dao\Trans\Batch\Item */
    private $daoBatchItem;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Downline\Api\Helper\Tree */
    private $hlpTree;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Accounting\Api\Service\Operation */
    private $servOper;
    /** @var \Magento\Backend\Model\Auth\Session */
    private $sessAdmin;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $sessAdmin,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Account $daoAcc,
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoAssetType,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Pv\Repo\Dao\Trans\Batch\Item $daoBatchItem,
        \Praxigento\Downline\Api\Helper\Tree $hlpTree,
        \Praxigento\Accounting\Api\Service\Operation $servOper
    ) {
        $this->sessAdmin = $sessAdmin;
        $this->logger = $logger;
        $this->daoAcc = $daoAcc;
        $this->daoAssetType = $daoAssetType;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->daoBatchItem = $daoBatchItem;
        $this->hlpTree = $hlpTree;
        $this->servOper = $servOper;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();

        $batchId = $request->getBatchId();
        $dateApplied = $request->getDateApplied();

        $user = $this->sessAdmin->getUser();
        $userId = $user->getId();
        $mapMlmIdById = $this->getMlmIdsMap();
        $items = $this->daoBatchItem->getByBatchId($batchId);
        $assetTypeId = $this->daoAssetType->getIdByCode(Cfg::CODE_TYPE_ASSET_PV);

        /* create transactions */
        $trans = [];
        foreach ($items as $item) {
            $custIdFrom = $item->getCustFromRef();
            $custIdTo = $item->getCustToRef();
            $value = $item->getValue();
            $custMlmIdFrom = $mapMlmIdById[$custIdFrom];
            $custMlmIdTo = $mapMlmIdById[$custIdTo];

            if ($value > Cfg::DEF_ZERO) {
                $accDebit = $this->daoAcc->getByCustomerId($custIdFrom, $assetTypeId);
                $accDebitId = $accDebit->getId();
                $accCredit = $this->daoAcc->getByCustomerId($custIdTo, $assetTypeId);
                $accCreditId = $accCredit->getId();
                $note = "batch: $custMlmIdFrom/$custIdFrom => $custMlmIdTo/$custIdTo";
                $tran = new ETrans();
                $tran->setDebitAccId($accDebitId);
                $tran->setCreditAccId($accCreditId);
                $tran->setValue($value);
                $tran->setDateApplied($dateApplied);
                $tran->setNote($note);
                $trans[] = $tran;
            }
        }

        /* create operation */
        if (count($trans)) {
            $note = 'batch';
            $req = new AReqOper();
            $req->setAdminUserId($userId);
            $req->setOperationTypeCode(Cfg::CODE_TYPE_OPER_PV_TRANSFER);
            $req->setTransactions($trans);
            $req->setOperationNote($note);
            /** @var ARespOper $resp */
            $resp = $this->servOper->exec($req);
            $operId = $resp->getOperationId();

            /** compose result */
            $result->setOperationId($operId);
            $result->markSucceed();
        }

        return $result;
    }

    /**
     * @return array [id => mlmId]
     */
    private function getMlmIdsMap()
    {
        $result = [];
        /** @var EDwnlCust[] $all */
        $all = $this->daoDwnlCust->get();
        foreach ($all as $one) {
            $id = $one->getCustomerId();
            $mlmId = $one->getMlmId();
            $result[$id] = $mlmId;
        }
        return $result;
    }
}