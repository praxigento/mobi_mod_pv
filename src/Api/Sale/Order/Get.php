<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Api\Sale\Order;


class Get
    implements \Praxigento\Pv\Api\Sale\Order\GetInterface
{
    protected $repoPvSale;
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    protected $repoSaleOrder;
    /** @var \Magento\Framework\Api\SearchCriteriaBuilder */
    protected $searchCritBuilder;

    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCritBuilder,
        \Magento\Sales\Api\OrderRepositoryInterface $repoSaleOrder,
        \Praxigento\Pv\Repo\Entity\Sale $repoPvSale
    ) {
        $this->searchCritBuilder = $searchCritBuilder;
        $this->repoSaleOrder = $repoSaleOrder;
        $this->repoPvSale = $repoPvSale;
    }

    public function execute(\Praxigento\Pv\Api\Sale\Order\Get\Request $data)
    {
        $result = new \Praxigento\Pv\Api\Sale\Order\Get\Response();
        $orderIdInc = $data->getIdInc();
        /* load order by incremental id */
        $searchCriteria = $this->searchCritBuilder
            ->addFilter(\Magento\Sales\Api\Data\OrderInterface::INCREMENT_ID, $orderIdInc)
            ->create();
        $ordersList = $this->repoSaleOrder->getList($searchCriteria);
        /** @var \Magento\Sales\Api\Data\OrderInterface $saleOrder */
        $saleOrder = $ordersList->getFirstItem();
        $orderIdMage = $saleOrder->getId();
        /* load PV data by order ID */
        $salePv = $this->repoPvSale->getById($orderIdMage);
        $result->setData($salePv);
        $result->getResult()->setCode($result::CODE_SUCCESS);
        return $result;
    }

}