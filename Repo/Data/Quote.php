<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Repo\Data;

class Quote
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_DISCOUNT = 'discount';
    const A_QUOTE_REF = 'quote_ref';
    const A_SUBTOTAL = 'subtotal';
    const A_TOTAL = 'total';
    const ENTITY_NAME = 'prxgt_pv_quote';

    /** @return float */
    public function getDiscount() {
        $result = parent::get(self::A_DISCOUNT);
        return $result;
    }

    public static function getPrimaryKeyAttrs() {
        return [self::A_QUOTE_REF];
    }

    /** @return int */
    public function getQuoteRef() {
        $result = parent::get(self::A_QUOTE_REF);
        return $result;
    }

    /** @return float */
    public function getSubtotal() {
        $result = parent::get(self::A_SUBTOTAL);
        return $result;
    }

    /** @return float */
    public function getTotal() {
        $result = parent::get(self::A_TOTAL);
        return $result;
    }

    /** @param float $data */
    public function setDiscount($data) {
        parent::set(self::A_DISCOUNT, $data);
    }

    /** @param int $data */
    public function setQuoteRef($data) {
        parent::set(self::A_QUOTE_REF, $data);
    }

    /** @param float $data */
    public function setSubtotal($data) {
        parent::set(self::A_SUBTOTAL, $data);
    }

    /** @param float $data */
    public function setTotal($data) {
        parent::set(self::A_TOTAL, $data);
    }

}