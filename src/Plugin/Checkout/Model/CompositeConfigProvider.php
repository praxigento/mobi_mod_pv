<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Checkout\Model;

use Praxigento\Pv\Repo\Entity\Data\Quote as EPvQuote;
use Praxigento\Pv\Repo\Entity\Data\Quote\Item as EPvQuoteItem;

/**
 * Add PV related data to cart/quote (totals & items PV).
 */
class CompositeConfigProvider
{
    const JSON_CAN_SEE_PV = 'praxigentoCustomerCanSeePv';
    const JSON_ITEM_CAN_SEE_PV = 'prxgt_pv_can_see'; // flag bound to the item (TODO: use JSON_CAN_SEE_PV on the front)
    const JSON_ITEM_PV_TOTAL = 'prxgt_pv_total';
    const JSON_TOTAL_SEG_DISCOUNT = 'prxgt_pv_cart_discount';
    const JSON_TOTAL_SEG_GRAND = 'prxgt_pv_cart_grand';
    const JSON_TOTAL_SEG_SUBTOTAL = 'prxgt_pv_cart_subtotal';

    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpPvCust;
    /** @var \Praxigento\Pv\Repo\Entity\Quote */
    private $repoPvQuote;
    /** @var \Praxigento\Pv\Repo\Entity\Quote\Item */
    private $repoPvQuoteItem;
    /** @var \Praxigento\Pv\Helper\ConfigProvider */
    private $hlpCfgProvider;

    public function __construct(
        \Praxigento\Pv\Helper\ConfigProvider $hlpCfgProvider,
        \Praxigento\Pv\Repo\Entity\Quote $repoPvQuote,
        \Praxigento\Pv\Repo\Entity\Quote\Item $repoPvQuoteItem,
        \Praxigento\Pv\Helper\Customer $hlpPvCust
    ) {
        $this->hlpCfgProvider = $hlpCfgProvider;
        $this->repoPvQuote = $repoPvQuote;
        $this->repoPvQuoteItem = $repoPvQuoteItem;
        $this->hlpPvCust = $hlpPvCust;
    }

    /**
     * @param array $result
     * @param bool $canSeePv
     * @return array
     */
    private function addPvToItems($result, $canSeePv)
    {
        if (
            $canSeePv &&
            isset($result['totalsData']) &&
            isset($result['totalsData']['items']) &&
            is_array($result['totalsData']['items'])
        ) {
            $items = $result['totalsData']['items'];
            foreach ($items as $key => $item) {
                $id = $item['item_id'];
                /** @var EPvQuoteItem $pvData */
                $pvData = $this->repoPvQuoteItem->getById($id);
                if ($pvData) {
                    $total = $pvData->getTotal();
                    $total = number_format($total, 2, '.', '');
                    $item[self::JSON_ITEM_PV_TOTAL] = $total;
                }
                $item[self::JSON_ITEM_CAN_SEE_PV] = $canSeePv;
                $items[$key] = $item;
            }
            $result['totalsData']['items'] = $items;
        }
        return $result;
    }

    /**
     * @param array $result
     * @param bool $canSeePv
     * @return array
     */
    private function addPvTotalsToCart($result, $canSeePv)
    {
        if ($canSeePv) {
            $cartId = $result['quoteData']['entity_id'];
            /** @var EPvQuote $pvTotals */
            $pvTotals = $this->repoPvQuote->getById($cartId);
            if ($pvTotals) {
                $totals = $result['totalsData']['total_segments'];
                $sub = $total = number_format($pvTotals->getSubtotal(), 2, '.', '');
                $discount = $total = number_format($pvTotals->getDiscount(), 2, '.', '');
                $grand = $total = number_format($pvTotals->getTotal(), 2, '.', '');
                $segment = [
                    'code' => self::JSON_TOTAL_SEG_SUBTOTAL,
                    'value' => $sub
                ];
                $totals[] = $segment;
                $segment = [
                    'code' => self::JSON_TOTAL_SEG_DISCOUNT,
                    'value' => $discount
                ];
                $totals[] = $segment;
                $segment = [
                    'code' => self::JSON_TOTAL_SEG_GRAND,
                    'value' => $grand
                ];
                $totals[] = $segment;
                $result['totalsData']['total_segments'] = $totals;
            }
        }
        return $result;
    }

    /**
     * Add PV data to cart/quote JSON used in checkout configuration on front:
     * {code:js}
     *      window.checkoutConfig = {};
     * {code}
     *
     * @param \Magento\Checkout\Model\CompositeConfigProvider $subject
     * @param array $result
     * @return mixed
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\CompositeConfigProvider $subject,
        $result
    ) {
//        $canSeePv = $this->hlpPvCust->canSeePv();
//        /* add 'CanSeePv' flag to results */
//        $result [self::JSON_CAN_SEE_PV] = $canSeePv;
//        /* add PV totals to cart/quote */
//        $result = $this->addPvTotalsToCart($result, $canSeePv);
//        /* add PV to cart/quote items */
//        $result = $this->addPvToItems($result, $canSeePv);
        $result = $this->hlpCfgProvider->addPvData($result);
        return $result;
    }
}