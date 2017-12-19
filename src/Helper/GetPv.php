<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Helper;

use Praxigento\Pv\Repo\Query\Product\GetPv as QBGetPv;

class GetPv
    implements \Praxigento\Pv\Api\Helper\GetPv
{
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpWrhsStock;
    /** @var \Praxigento\Pv\Repo\Query\Product\GetPv */
    private $qbGetPv;

    public function __construct(
        \Praxigento\Pv\Repo\Query\Product\GetPv $qbGetPv,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpWrhsStock
    ) {
        $this->qbGetPv = $qbGetPv;
        $this->hlpWrhsStock = $hlpWrhsStock;
    }

    public function product($prodId, $stockId = null) {
        if (!$stockId) {
            $stockId = $this->hlpWrhsStock->getCurrentStockId();
        }
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