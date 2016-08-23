<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Repo\Entity\Sale\Def;

use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Data\Entity\Sale\Item as Entity;

class Item
    extends \Praxigento\Core\Repo\Def\Entity
    implements \Praxigento\Pv\Repo\Entity\Sale\IItem
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manOb,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
        $this->_manObj = $manOb;
    }

    /** @inheritdoc */
    public function getItemsByOrderId($orderId)
    {
        $result = [];
        /* aliases and tables */
        $asOrder = 'sale';
        $asPvItem = 'pv';
        $tblOrder = [$asOrder => $this->_resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM)];
        $tblPvItem = [$asPvItem => $this->_resource->getTableName(Entity::ENTITY_NAME)];
        /* SELECT FROM sales_order_item */
        $query = $this->_conn->select();
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
        $rows = $this->_conn->fetchAll($query);
        foreach ($rows as $row) {
            /** @var Entity $item */
            $item = $this->_manObj->create(Entity::class, $row);
            $result[$item->getSaleItemId()] = $item;
        }
        return $result;
    }

}