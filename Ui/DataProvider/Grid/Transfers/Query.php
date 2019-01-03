<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Ui\DataProvider\Grid\Transfers;

use Praxigento\Core\App\Repo\Query\Expression as AnExpression;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;
use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Data\Trans\Batch\Item as EItem;

class Query
    extends \Praxigento\Core\App\Ui\DataProvider\Grid\Query\Builder
{
    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_FROM = 'f';
    const AS_GROUP = 'g';
    const AS_ITEMS = 'i';
    const AS_NAME_FROM = 'nf';
    const AS_NAME_TO = 'nt';
    const AS_TO = 't';
    /**#@- */

    /**#@+ Columns/expressions aliases for external usage */
    const A_BATCH_ID = 'batchId';
    const A_FROM_COUNTRY = 'fromCountry';
    const A_FROM_ID = 'fromId';
    const A_FROM_MLM_ID = 'fromMlmId';
    const A_FROM_NAME = 'fromName';
    const A_ITEM_ID = 'itemId';
    const A_RESTRICTED = 'restricted';
    const A_TO_COUNTRY = 'toCountry';
    const A_TO_GROUP = 'toGroup';
    const A_TO_ID = 'toId';
    const A_TO_MLM_ID = 'toMlmId';
    const A_TO_NAME = 'toName';
    const A_TO_PATH = 'toPath';
    const A_VALUE = 'value';
    /**#@- */

    /**#@+ Entities are used in the query */
    const E_FROM = EDwnlCust::ENTITY_NAME;
    const E_GROUP = Cfg::ENTITY_MAGE_CUSTOMER_GROUP;
    const E_ITEMS = EItem::ENTITY_NAME;
    const E_NAME_FROM = Cfg::ENTITY_MAGE_CUSTOMER;
    const E_NAME_TO = Cfg::ENTITY_MAGE_CUSTOMER;
    const E_TO = EDwnlCust::ENTITY_NAME;
    /**#@- */

    /** @var \Praxigento\Pv\Helper\BatchIdStore */
    private $hlpBatchIdStore;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\Query\Criteria\IAdapter $critAdapter,
        \Praxigento\Pv\Helper\BatchIdStore $hlpBatchIdStore
    ) {
        parent::__construct($resource, $critAdapter);
        $this->hlpBatchIdStore = $hlpBatchIdStore;
    }

    private function expFullNameFrom()
    {
        $fullname = 'CONCAT(' . self::AS_NAME_FROM . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME . ', " ", '
            . self::AS_NAME_FROM . '.' . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $result = new AnExpression($fullname);
        return $result;
    }

    private function expFullNameTo()
    {
        $fullname = 'CONCAT(' . self::AS_NAME_TO . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME . ', " ", '
            . self::AS_NAME_TO . '.' . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $result = new AnExpression($fullname);
        return $result;
    }

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $expFullNameFrom = $this->expFullNameFrom();
            $expFullNameTo = $this->expFullNameTo();
            $map = [
                self::A_BATCH_ID => self::AS_ITEMS . '.' . EItem::A_BATCH_REF,
                self::A_FROM_COUNTRY => self::AS_FROM . '.' . EDwnlCust::A_COUNTRY_CODE,
                self::A_FROM_ID => self::AS_ITEMS . '.' . EItem::A_CUST_FROM_REF,
                self::A_FROM_MLM_ID => self::AS_FROM . '.' . EDwnlCust::A_MLM_ID,
                self::A_FROM_NAME => $expFullNameFrom,
                self::A_ITEM_ID => self::AS_ITEMS . '.' . EItem::A_ID,
                self::A_RESTRICTED => self::AS_ITEMS . '.' . EItem::A_RESTRICTED,
                self::A_TO_COUNTRY => self::AS_TO . '.' . EDwnlCust::A_COUNTRY_CODE,
                self::A_TO_GROUP => self::AS_GROUP . '.' . Cfg::E_CUSTGROUP_A_CODE,
                self::A_TO_ID => self::AS_ITEMS . '.' . EItem::A_CUST_TO_REF,
                self::A_TO_MLM_ID => self::AS_TO . '.' . EDwnlCust::A_MLM_ID,
                self::A_TO_NAME => $expFullNameTo,
                self::A_TO_PATH => self::AS_TO . '.' . EDwnlCust::A_PATH,
                self::A_VALUE => self::AS_ITEMS . '.' . EItem::A_VALUE
            ];
            $this->mapper = new \Praxigento\Core\App\Repo\Query\Criteria\Def\Mapper($map);
        }
        $result = $this->mapper;
        return $result;
    }

    /**
     * SELECT
     * `i`.`batch_ref` AS `batchId`,
     * `i`.`id` AS `itemId`,
     * `i`.`cust_from_ref` AS `fromId`,
     * `i`.`cust_to_ref` AS `toId`,
     * `i`.`restricted`,
     * `i`.`value`,
     * `f`.`country_code` AS `fromCountry`,
     * `f`.`mlm_id` AS `fromMlmId`,
     * (CONCAT(nf.firstname,
     * " ",
     * nf.lastname)) AS `fromName`,
     * `t`.`country_code` AS `toCountry`,
     * `t`.`mlm_id` AS `toMlmId`,
     * `t`.`path` AS `toPath`,
     * (CONCAT(nt.firstname,
     * " ",
     * nt.lastname)) AS `toName`,
     * `g`.`customer_group_code` AS `toGroup`
     * FROM
     * `prxgt_pv_trans_batch_item` AS `i`
     * LEFT JOIN `prxgt_dwnl_customer` AS `f` ON
     * f.customer_ref = i.cust_from_ref
     * LEFT JOIN `customer_entity` AS `nf` ON
     * nf.entity_id = f.customer_ref
     * LEFT JOIN `prxgt_dwnl_customer` AS `t` ON
     * t.customer_ref = i.cust_to_ref
     * LEFT JOIN `customer_entity` AS `nt` ON
     * nt.entity_id = t.customer_ref
     * LEFT JOIN `customer_group` AS `g` ON
     * g.customer_group_id = nt.group_id
     * WHERE
     * (i.batch_ref = :batchId)
     *
     */
    protected function getQueryItems()
    {
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asItems = self::AS_ITEMS;
        $asFrom = self::AS_FROM;
        $asGroup = self::AS_GROUP;
        $asFromName = self::AS_NAME_FROM;
        $asTo = self::AS_TO;
        $asToName = self::AS_NAME_TO;

        /* SELECT FROM prxgt_pv_trans_batch_item */
        $tbl = $this->resource->getTableName(self::E_ITEMS);
        $as = $asItems;
        $cols = [
            self::A_BATCH_ID => EItem::A_BATCH_REF,
            self::A_ITEM_ID => EItem::A_ID,
            self::A_FROM_ID => EItem::A_CUST_FROM_REF,
            self::A_TO_ID => EItem::A_CUST_TO_REF,
            self::A_RESTRICTED => EItem::A_RESTRICTED,
            self::A_VALUE => EItem::A_VALUE
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_dwnl_customer (from) */
        $tbl = $this->resource->getTableName(self::E_FROM);
        $as = $asFrom;
        $cols = [
            self::A_FROM_COUNTRY => EDwnlCust::A_COUNTRY_CODE,
            self::A_FROM_MLM_ID => EDwnlCust::A_MLM_ID
        ];
        $cond = $as . '.' . EDwnlCust::A_CUSTOMER_REF . '=' . $asItems . '.' . EItem::A_CUST_FROM_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_entity (from) */
        $tbl = $this->resource->getTableName(self::E_NAME_FROM);
        $as = $asFromName;
        $exp = $this->expFullNameFrom();
        $cols = [
            self::A_FROM_NAME => $exp
        ];
        $cond = $as . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asFrom . '.' . EDwnlCust::A_CUSTOMER_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer (to) */
        $tbl = $this->resource->getTableName(self::E_TO);
        $as = $asTo;
        $cols = [
            self::A_TO_COUNTRY => EDwnlCust::A_COUNTRY_CODE,
            self::A_TO_MLM_ID => EDwnlCust::A_MLM_ID,
            self::A_TO_PATH => EDwnlCust::A_PATH
        ];
        $cond = $as . '.' . EDwnlCust::A_CUSTOMER_REF . '=' . $asItems . '.' . EItem::A_CUST_TO_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_entity (to) */
        $tbl = $this->resource->getTableName(self::E_NAME_TO);
        $as = $asToName;
        $exp = $this->expFullNameTo();
        $cols = [
            self::A_TO_NAME => $exp
        ];
        $cond = $as . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID . '=' . $asTo . '.' . EDwnlCust::A_CUSTOMER_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_group (to) */
        $tbl = $this->resource->getTableName(self::E_GROUP);
        $as = $asGroup;
        $cols = [
            self::A_TO_GROUP => Cfg::E_CUSTGROUP_A_CODE
        ];
        $cond = $as . '.' . Cfg::E_CUSTGROUP_A_ID . '=' . $asToName . '.' . Cfg::E_CUSTOMER_A_GROUP_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /**
         * Add batch ID to the filter.
         * It'is not a good solution when query builder uses helper (inner layer calls outer layer).
         */
        $batchId = (int)$this->hlpBatchIdStore->restoreBatchId();
        $where = $asItems . '.' . EItem::A_BATCH_REF . '=' . $batchId;
        $result->where($where);

        /* return  result */
        return $result;
    }

    protected function getQueryTotal()
    {
        /* get query to select items */
        /** @var \Magento\Framework\DB\Select $result */
        $result = $this->getQueryItems();
        /* ... then replace "columns" part with own expression */
        $value = 'COUNT(' . self::AS_ITEMS . '.' . EItem::A_ID . ')';

        /**
         * See method \Magento\Framework\DB\Select\ColumnsRenderer::render:
         */
        /**
         * if ($column instanceof \Zend_Db_Expr) {...}
         */
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($value);
        /**
         *  list($correlationName, $column, $alias) = $columnEntry;
         */
        $entry = [null, $exp, null];
        $cols = [$entry];
        $result->setPart('columns', $cols);
        return $result;
    }
}