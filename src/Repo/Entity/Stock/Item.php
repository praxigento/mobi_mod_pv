<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Entity\Stock;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Entity\Data\Stock\Item as Entity;

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
     * Get Warehouse PV by $productId & $stockId.
     *
     * @param int $productId
     * @param int $stockId
     * @return double
     */
    public function getPvByProductAndStock($productId, $stockId)
    {
        /* aliases and tables */
        $asStockItem = 'csi';
        $asPv = 'ppsi';
        $tblStockItem = [$asStockItem => $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM)];
        $tblPv = [$asPv => $this->resource->getTableName(Entity::ENTITY_NAME)];
        /* SELECT FROM cataloginventory_stock_item */
        $query = $this->conn->select();
        $cols = [];
        $query->from($tblStockItem, $cols);
        /* LEFT JOIN prxgt_pv_stock_item */
        $on = $asPv . '.' . Entity::ATTR_ITEM_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $cols = [Entity::ATTR_PV];
        $query->joinLeft($tblPv, $on, $cols);
        /* WHERE */
        $where = $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . (int)$productId;
        $where .= ' AND ' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '=' . (int)$stockId;
        $query->where($where);
        /* fetch data */
        $result = $this->conn->fetchOne($query);
        return $result;
    }
}