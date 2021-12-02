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
    const A_TO_COUNTRY = 'toCountry';
    const A_TO_GROUP = 'toGroup';
    const A_TO_ID = 'toId';
    const A_TO_MLM_ID = 'toMlmId';
    const A_TO_NAME = 'toName';
    const A_TO_PATH = 'toPath';
    const A_VALUE = 'value';
    const A_WARN_BALANCE = 'warn_balance';
    const A_WARN_COUNTRY = 'warn_country';
    const A_WARN_DATE_APPLIED = 'warn_date_applied';
    const A_WARN_DWNL = 'warn_dwnl';
    const A_WARN_GROUP = 'warn_group';
    const A_WARN_SAME_IDS = 'warn_same_ids';
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
                self::A_WARN_DATE_APPLIED => self::AS_ITEMS . '.' . EItem::A_WARN_DATE_APPLIED,
                self::A_WARN_BALANCE => self::AS_ITEMS . '.' . EItem::A_WARN_BALANCE,
                self::A_WARN_COUNTRY => self::AS_ITEMS . '.' . EItem::A_WARN_COUNTRY,
                self::A_WARN_DWNL => self::AS_ITEMS . '.' . EItem::A_WARN_DWNL,
                self::A_WARN_GROUP => self::AS_ITEMS . '.' . EItem::A_WARN_GROUP,
                self::A_WARN_SAME_IDS => self::AS_ITEMS . '.' . EItem::A_WARN_SAME_IDS,
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
            self::A_WARN_DATE_APPLIED => EItem::A_WARN_DATE_APPLIED,
            self::A_WARN_BALANCE => EItem::A_WARN_BALANCE,
            self::A_WARN_COUNTRY => EItem::A_WARN_COUNTRY,
            self::A_WARN_DWNL => EItem::A_WARN_DWNL,
            self::A_WARN_GROUP => EItem::A_WARN_GROUP,
            self::A_WARN_SAME_IDS => EItem::A_WARN_SAME_IDS,
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
