<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Checkout\CustomerData;

use Praxigento\Pv\Config as Cfg;

/**
 * Add warehouse PV to mini-cart items (top right corner).
 */
class Cart
{
    const JSON_PV_MINI_CART_CAN_SEE = 'prxgt_pv_mini_cart_can_see';
    const JSON_PV_MINI_CART_ITEM_CAN_SEE = 'prxgt_pv_mini_cart_item_can_see';
    const JSON_PV_MINI_CART_ITEM_TOTAL = 'prxgt_pv_mini_cart_item_total';
    const JSON_PV_MINI_CART_TOTAL = 'prxgt_pv_mini_cart_total';

    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpPvCust;
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    private $daoGeneric;
    /** @var \Praxigento\Pv\Repo\Dao\Quote */
    private $daoQuote;
    /** @var \Praxigento\Pv\Repo\Dao\Quote\Item */
    private $daoQuoteItem;

    public function __construct(
        \Praxigento\Core\App\Repo\IGeneric $daoGeneric,
        \Praxigento\Pv\Repo\Dao\Quote $daoQuote,
        \Praxigento\Pv\Repo\Dao\Quote\Item $daoQuoteItem,
        \Praxigento\Pv\Helper\Customer $hlpPvCust
    ) {
        $this->daoGeneric = $daoGeneric;
        $this->daoQuote = $daoQuote;
        $this->daoQuoteItem = $daoQuoteItem;
        $this->hlpPvCust = $hlpPvCust;
    }

    public function afterGetSectionData(
        \Magento\Checkout\CustomerData\Cart $subject,
        $result
    ) {
        if (is_array($result)) {
            $canSeePv = $this->hlpPvCust->canSeePv();
            $result[self::JSON_PV_MINI_CART_CAN_SEE] = $canSeePv;
            if ($canSeePv) {
                if (isset($result['items']) && is_array($result['items'])) {
                    $itemId = false;
                    foreach ($result['items'] as $key => $item) {
                        $itemId = $item['item_id'];
                        $pvItem = $this->daoQuoteItem->getById($itemId);
                        $totalItem = $pvItem->getTotal();
                        $totalItem = number_format($totalItem, 2, '.', '');
                        $result['items'][$key][self::JSON_PV_MINI_CART_ITEM_TOTAL] = $totalItem;
                        /* this is not good idea, but "This is MAGENTA-A-A-A!!!!" */
                        /* it is not a big price to have a 'canSeePv' flag for each item */
                        $result['items'][$key][self::JSON_PV_MINI_CART_ITEM_CAN_SEE] = $canSeePv;
                    }
                    if ($itemId) {
                        /* this is not empty quote, get total PV for quote itself */
                        $quoteId = $this->getQuoteIdByItemId($itemId);
                        $pvQuote = $this->daoQuote->getById($quoteId);
                        $totalQuote = $pvQuote->getTotal();
                        $totalQuote = number_format($totalQuote, 2, '.', '');
                    } else {
                        /* this is quote w/o items */
                        $totalQuote = '0.00';
                    }
                    $result[self::JSON_PV_MINI_CART_TOTAL] = $totalQuote;
                }
            }
        }
        return $result;
    }

    private function getQuoteIdByItemId($itemId)
    {
        $tbl = Cfg::ENTITY_MAGE_QUOTE_ITEM;
        $id = [Cfg::E_QUOTE_ITEM_A_ITEM_ID => $itemId];
        $cols = [Cfg::E_QUOTE_ITEM_A_QUOTE_ID];
        $rs = $this->daoGeneric->getEntityByPk($tbl, $id, $cols);
        $result = reset($rs);
        return $result;
    }
}