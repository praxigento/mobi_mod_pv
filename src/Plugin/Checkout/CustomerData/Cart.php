<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Checkout\CustomerData;

use Praxigento\Pv\Config as Cfg;

/**
 * Add warehouse PV to mini-cart items.
 */
class Cart
{
    const CFG_CAN_SEE_PV = 'prxgt_pv_can_see';
    const CFG_PV_TOTAL = 'prxgt_pv_total';
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpPvCust;
    /** @var \Praxigento\Core\App\Repo\IGeneric */
    private $repoGeneric;
    /** @var \Praxigento\Pv\Repo\Entity\Quote */
    private $repoQuote;
    /** @var \Praxigento\Pv\Repo\Entity\Quote\Item */
    private $repoQuoteItem;

    public function __construct(
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric,
        \Praxigento\Pv\Repo\Entity\Quote $repoQuote,
        \Praxigento\Pv\Repo\Entity\Quote\Item $repoQuoteItem,
        \Praxigento\Pv\Helper\Customer $hlpPvCust
    ) {
        $this->repoGeneric = $repoGeneric;
        $this->repoQuote = $repoQuote;
        $this->repoQuoteItem = $repoQuoteItem;
        $this->hlpPvCust = $hlpPvCust;
    }

    public function afterGetSectionData(
        \Magento\Checkout\CustomerData\Cart $subject,
        $result
    ) {
        if (is_array($result)) {
            $canSeePv = $this->hlpPvCust->canSeePv();
            $result[self::CFG_CAN_SEE_PV] = $canSeePv;
            if ($canSeePv) {
                if (isset($result['items']) && is_array($result['items'])) {
                    foreach ($result['items'] as $key => $item) {
                        $itemId = $item['item_id'];
                        $pvItem = $this->repoQuoteItem->getById($itemId);
                        $totalItem = $pvItem->getTotal();
                        $totalItem = number_format($totalItem, 2, '.', '');
                        $result['items'][$key][self::CFG_PV_TOTAL] = $totalItem;
                        /* this is not good idea, but "This is MAGENTA-A-A-A!!!!" */
                        /* it is not a big price to have a 'canSeePv' flag for each item */
                        $result['items'][$key][self::CFG_CAN_SEE_PV] = $canSeePv;
                    }
                    /* get total PV for quote itself */
                    $quoteId = $this->getQuoteIdByItemId($itemId);
                    $pvQuote = $this->repoQuote->getById($quoteId);
                    $totalQuote = $pvQuote->getTotal();
                    $totalQuote = number_format($totalQuote, 2, '.', '');
                    $result[self::CFG_PV_TOTAL] = $totalQuote;
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
        $rs = $this->repoGeneric->getEntityByPk($tbl, $id, $cols);
        $result = reset($rs);
        return $result;
    }
}