<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Dao\Sale;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Data\Sale\Item as Entity;

class Item
    extends \Praxigento\Core\App\Repo\Def\Entity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /**
     * @param Entity|array $data
     * @return Entity
     */
    public function create($data) {
        $result = parent::create($data);
        return $result;
    }

    /**
     * Generic method to get data from repository.
     *
     * @param null $where
     * @param null $order
     * @param null $limit
     * @param null $offset
     * @param null $columns
     * @param null $group
     * @param null $having
     * @return Entity[] Found data or empty array if no data found.
     */
    public function get(
        $where = null,
        $order = null,
        $limit = null,
        $offset = null,
        $columns = null,
        $group = null,
        $having = null
    ) {
        $result = parent::get($where, $order, $limit, $offset, $columns, $group, $having);
        return $result;
    }

    /**
     * Get the data instance by ID.
     *
     * @param int $id
     * @return Entity|bool Found instance data or 'false'
     */
    public function getById($id) {
        $result = parent::getById($id);
        return $result;
    }

    /**
     * Get array of the PvSaleItems entities by Magento order ID.
     * @param int $orderId
     * @return Entity[] index is a $saleItemId
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
        $on = $asPvItem . '.' . Entity::ATTR_ITEM_REF . '=' . $asOrder . '.' . Cfg::E_SALE_ORDER_ITEM_A_ITEM_ID;
        $cols = '*'; // get all columns
        $query->joinLeft($tblPvItem, $on, $cols);
        /* WHERE */
        $where = $asOrder . '.' . Cfg::E_SALE_ORDER_ITEM_A_ORDER_ID . '=' . (int)$orderId;
        $query->where($where);
        /* fetch data */
        $rows = $this->conn->fetchAll($query);
        foreach ($rows as $row) {
            /** @var Entity $item */
            $item = new Entity($row);
            $result[$item->getItemRef()] = $item;
        }
        return $result;
    }

}