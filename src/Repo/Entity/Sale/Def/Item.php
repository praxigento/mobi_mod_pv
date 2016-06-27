<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Entity\Sale\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoGeneric;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Data\Entity\Sale\Item as Entity;
use Praxigento\Pv\Repo\Entity\Sale\IItem as IEntityRepo;

class Item extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IRepoGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /** @inheritdoc */
    public function getItemsByOrderId($orderId)
    {
        $result = [];
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $conn */
        $conn = $this->_repoGeneric->getConnection();
        /* aliases and tables */
        $asOrder = 'sale';
        $asPvItem = 'pv';
        $tblOrder = [$asOrder => $conn->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM)];
        $tblPvItem = [$asPvItem => $conn->getTableName(Entity::ENTITY_NAME)];
        /* SELECT FROM sales_order_item */
        $query = $conn->select();
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
        $rows = $conn->fetchAll($query);
        foreach ($rows as $row) {
            $item = new Entity($row);
            $result[$item->getSaleItemId()] = $item;
        }
        return $result;
    }

}