<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity\Sale;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Entity\Data\Sale\Item as Entity;

class Item
    extends \Praxigento\Core\App\Repo\Def\Entity
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manOb,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
        $this->_manObj = $manOb;
    }

    /**
     * Get array of the PvSaleItems entities by Magento order ID.
     * @param int $orderId
     * @return \Praxigento\Pv\Repo\Entity\Data\Sale\Item[] index is a $saleItemId
     */
    public function getItemsByOrderId($orderId)
    {
        $result = [];
        /* aliases and tables */
        $asOrder = 'sale';
        $asPvItem = 'pv';
        $tblOrder = [$asOrder => $this->resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM)];
        $tblPvItem = [$asPvItem => $this->resource->getTableName(Entity::ENTITY_NAME)];
        /* SELECT FROM sales_order_item */
        $query = $this->conn->select();
        $cols = [];
        $query->from($tblOrder, $cols);
        /* LEFT JOIN prxgt_pv_sale_item pwq */
        $on = $asPvItem . '.' . Entity::ATTR_SALE_ITEM_ID . '=' . $asOrder . '.' . Cfg::E_SALE_ORDER_ITEM_A_ITEM_ID;
        $cols = '*'; // get all columns
        $query->joinLeft($tblPvItem, $on, $cols);
        /* WHERE */
        $where = $asOrder . '.' . Cfg::E_SALE_ORDER_ITEM_A_ORDER_ID . '=' . (int)$orderId;
        $query->where($where);
        /* fetch data */
        $rows = $this->conn->fetchAll($query);
        foreach ($rows as $row) {
            /** @var Entity $item */
            $item = $this->_manObj->create(Entity::class, $row);
            $result[$item->getSaleItemId()] = $item;
        }
        return $result;
    }

}