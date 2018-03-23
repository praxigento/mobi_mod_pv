<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Plugin\Catalog\Model\ResourceModel\Product;

use Praxigento\Pv\Repo\Data\Product as EPvProd;
use Praxigento\Pv\Repo\Data\Stock\Item as EPvStockItem;
use Praxigento\Warehouse\Config as Cfg;

/**
 * Add warehouse PV data to product collection on front and base PV data and fields mapping in admin.
 */
class CollectionFactory
{
    /** alias for joined table with PV data */
    const AS_CATINV_STOCK_ITEM = 'prxgtPvStockItem';
    const AS_PRXGT_PV_PRODUCT = 'prxgtPvProd';
    const AS_PRXGT_PV_WRHS = 'prxgtPvWrhs';

    /** alias for PV data in the result set (used in ./view/adminhtml/ui_component/product_listing.xml) */
    const A_PV_PRODUCT = 'prxgt_pv_product';

    /** combination of the table alias & table field for filtering & ordering in admin grid (Fully Qualified Name) */
    const FQN_PV = self::AS_PRXGT_PV_PRODUCT . '.' . EPvProd::A_PV;

    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;
    /** @var \Magento\Framework\Config\ScopeInterface */
    private $scope;
    /** @var \Magento\Authorization\Model\UserContextInterface */
    private $userContext;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Config\ScopeInterface $scope,
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock
    ) {
        $this->resource = $resource;
        $this->scope = $scope;
        $this->userContext = $userContext;
        $this->hlpStock = $hlpStock;
    }

    public function aroundCreate(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $subject,
        \Closure $proceed,
        array $data = []
    ) {
        $result = $proceed($data);
        if ($result instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {
            $query = $result->getSelect();
            /* detect running mode (front/back) */
            $scope = $this->scope->getCurrentScope();
            $asProd = 'e'; // this is default alias for main entity table in Magento.
            if ($scope == \Magento\Framework\App\Area::AREA_ADMINHTML) {
                /** backend mode: join base PV for admin */
                /* LEFT JOIN `prxgt_pv_prod` AS `prxgtPvProd` */
                $tbl = $this->resource->getTableName(EPvProd::ENTITY_NAME);
                $as = self::AS_PRXGT_PV_PRODUCT;
                $cols = [
                    self::A_PV_PRODUCT => EPvProd::A_PV
                ];
                $cond = "$as." . EPvProd::A_PROD_REF . "=$asProd." . Cfg::E_PRODUCT_A_ENTITY_ID;
                $query->joinLeft([$as => $tbl], $cond, $cols);
                /* add fields mapping */
                $result->addFilterToMap(self::A_PV_PRODUCT, self::FQN_PV);
                $result->addFilterToMap('`e`.`' . self::A_PV_PRODUCT . '`', self::FQN_PV);
            } elseif ($scope == \Magento\Framework\App\Area::AREA_FRONTEND) {
                /** frontend mode: join warehouse PV for frontend */
                $stockId = $this->hlpStock->getCurrentStockId();
                $asStockItem = self::AS_CATINV_STOCK_ITEM;
                $asPvWrhs = self::AS_PRXGT_PV_WRHS;

                /* JOIN cataloginventory_stock_item */
                $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
                $as = $asStockItem;
                $cols = [];
                $cond = "$as." . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . "$asProd." . Cfg::E_PRODUCT_A_ENTITY_ID;
                $query->joinLeft([$as => $tbl], $cond, $cols);

                /* JOIN prxgt_pv_stock_item */
                $tbl = $this->resource->getTableName(EPvStockItem::ENTITY_NAME);
                $as = $asPvWrhs;
                $cols = [
                    self::A_PV_PRODUCT => EPvStockItem::A_PV
                ];
                $cond = "$as." . EPvStockItem::A_ITEM_REF . '='
                    . "$asStockItem." . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
                $query->joinLeft([$as => $tbl], $cond, $cols);
                $cond = "$asStockItem." . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . '=' . (int)$stockId;
                $query->where($cond);
            } else {
                /* webapi_rest, cron or smth. else */
                $breakpoint = true;
            }
        }
        return $result;
    }
}