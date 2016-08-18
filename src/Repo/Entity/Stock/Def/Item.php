<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Entity\Stock\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Data\Entity\Stock\Item as Entity;
use Praxigento\Pv\Repo\Entity\Stock\IItem as IEntityRepo;

class Item extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /** @inheritdoc */
    public function getPvByProductAndStock($productId, $stockId)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $conn */
        $conn = $this->_resource->getConnection();
        /* aliases and tables */
        $asStockItem = 'csi';
        $asPv = 'ppsi';
        $tblStockItem = [$asStockItem => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM)];
        $tblPv = [$asPv => $this->_resource->getTableName(Entity::ENTITY_NAME)];
        /* SELECT FROM cataloginventory_stock_item */
        $query = $conn->select();
        $cols = [];
        $query->from($tblStockItem, $cols);
        /* LEFT JOIN prxgt_pv_stock_item */
        $on = $asPv . '.' . Entity::ATTR_STOCK_ITEM_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $cols = [Entity::ATTR_PV];
        $query->joinLeft($tblPv, $on, $cols);
        /* WHERE */
        $where = $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . (int)$productId;
        $where .= ' AND ' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '=' . (int)$stockId;
        $query->where($where);
        /* fetch data */
        $result = $conn->fetchOne($query);
        return $result;
    }
}