<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Helper;

/**
 * Populate cart/quote JSON configuration with PV data (JS globals on frontend: "window.checkoutConfig = {}").
 *
 * see
 *  * \Praxigento\Pv\Plugin\Checkout\Model\CompositeConfigProvider
 *  * \Praxigento\Pv\Plugin\Framework\Webapi\ServiceOutputProcessor
 */
class PvProvider
{
    const JSON_TOTAL_SEG_DISCOUNT = 'prxgt_pv_cart_discount';
    const JSON_TOTAL_SEG_GRAND = 'prxgt_pv_cart_grand';
    const JSON_TOTAL_SEG_SUBTOTAL = 'prxgt_pv_cart_subtotal';

    /** @var \Praxigento\Pv\Repo\Data\Quote[] */
    private $cachePvQuote = [];
    /** @var \Praxigento\Pv\Repo\Data\Quote\Item[] */
    private $cachePvQuoteItem = [];
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpPvCust;
    /** @var \Magento\Quote\Api\CartRepositoryInterface */
    private $daoCart;
    /** @var \Praxigento\Pv\Repo\Dao\Quote */
    private $daoPvQuote;
    /** @var \Praxigento\Pv\Repo\Dao\Quote\Item */
    private $daoPvQuoteItem;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $daoCart,
        \Praxigento\Pv\Repo\Dao\Quote $daoPvQuote,
        \Praxigento\Pv\Repo\Dao\Quote\Item $daoPvQuoteItem,
        \Praxigento\Pv\Helper\Customer $hlpPvCust
    ) {
        $this->daoCart = $daoCart;
        $this->daoPvQuote = $daoPvQuote;
        $this->daoPvQuoteItem = $daoPvQuoteItem;
        $this->hlpPvCust = $hlpPvCust;
    }

    public function addPvToTotalSegments($segments, $cartId)
    {
        /* set init values for totals */
        $subtotal = $discount = $grand = 0;
        /** @var \Praxigento\Pv\Repo\Data\Quote $quotePv */
        $quotePv = $this->loadPvForQuote($cartId);
        if ($quotePv) {
            $subtotal = number_format($quotePv->getSubtotal(), 2, '.', '');
            $discount = number_format($quotePv->getDiscount(), 2, '.', '');
            $grand = number_format($quotePv->getTotal(), 2, '.', '');
        }
        $one = [
            'code' => self::JSON_TOTAL_SEG_SUBTOTAL,
            'value' => $subtotal
        ];
        $segments[] = $one;
        $one = [
            'code' => self::JSON_TOTAL_SEG_DISCOUNT,
            'value' => $discount
        ];
        $segments[] = $one;
        $one = [
            'code' => self::JSON_TOTAL_SEG_GRAND,
            'value' => $grand
        ];
        $segments[] = $one;
        return $segments;
    }

    /**
     * @param int|null $custGroupId
     * @param int|null $cartId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCanSeePv($custGroupId = null, $cartId = null)
    {
        $result = false;
        /* try to get customer group ID using cart ID if group ID is missed */
        if (is_null($custGroupId)) {
            $cartData = $this->daoCart->get((int)$cartId);
            if ($cartData) {
                $custGroupId = $cartData->getCustomerGroupId();
            }
        }
        /* get 'Can See PV' attribute for the customer group */
        if (!is_null($custGroupId)) {
            $result = $this->hlpPvCust->canSeePv($custGroupId);
        }
        return $result;
    }

    /**
     * @param $itemId
     * @return string
     */
    public function getCartItemPv($itemId)
    {
        $grand = 0;
        $item = $this->loadPvForQuoteItem($itemId);
        if ($item) {
            $grand = $item->getTotal();
        }
        $result = number_format($grand, 2, '.', '');
        return $result;
    }

    /**
     * Cacheable loader for quote's PV.
     *
     * @param int $quoteId
     * @return \Praxigento\Pv\Repo\Data\Quote
     */
    private function loadPvForQuote($quoteId)
    {
        if (!isset($this->cachePvQuote[$quoteId])) {
            $found = $this->daoPvQuote->getById((int)$quoteId);
            $this->cachePvQuote[$quoteId] = $found;
        }
        return $this->cachePvQuote[$quoteId];
    }

    /**
     * Cacheable loader for quote item's PV.
     *
     * @param int $itemId
     * @return \Praxigento\Pv\Repo\Data\Quote\Item
     */
    private function loadPvForQuoteItem($itemId)
    {
        if (!isset($this->cachePvQuoteItem[$itemId])) {
            $found = $this->daoPvQuoteItem->getById((int)$itemId);
            $this->cachePvQuoteItem[$itemId] = $found;
        }
        return $this->cachePvQuoteItem[$itemId];
    }
}