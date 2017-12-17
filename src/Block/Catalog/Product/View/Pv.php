<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Block\Catalog\Product\View;

use Praxigento\Pv\Repo\Query\Product\GetPv as QBGetPv;

class Pv
    extends \Magento\Framework\View\Element\Template
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpWrhsStock;
    /** @var \Praxigento\Pv\Repo\Query\Product\GetPv */
    private $qbGetPv;
    /** @var \Magento\Framework\Registry */
    private $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Magento\Framework\Registry $registry,
        \Praxigento\Pv\Repo\Query\Product\GetPv $qbGetPv,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpWrhsStock
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->qbGetPv = $qbGetPv;
        $this->hlpWrhsStock = $hlpWrhsStock;
    }

    public function getWarehousePv() {

        $product = $this->registry->registry('product');
        $prodId = $product->getId();
        $stockId = $this->hlpWrhsStock->getCurrentStockId();
        $query = $this->qbGetPv->build();
        $bind = [
            QBGetPv::BND_PROD_ID => $prodId,
            QBGetPv::BND_STOCK_ID => $stockId
        ];
        $conn = $query->getConnection();
        $result = $conn->fetchOne($query, $bind);
        $result = number_format($result, 2);
        return $result;
    }
}