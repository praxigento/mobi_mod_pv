<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Model\Quote\Address\Total;

use Praxigento\Pv\Config as Cfg;

class Grand
    extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /** Code for total itself */
    const CODE = Cfg::CODE_TOTAL_GRAND;
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    protected $hlpPriceCurrency;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $hlpPriceCurrency
    ) {
        $this->hlpPriceCurrency = $hlpPriceCurrency;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        /* init total structure */
        parent::collect($quote, $shippingAssignment, $total);
        /* reset these totals values */
        $quoteGrand = 0;
        /* get fresh grands from calculating totals */
        $grandBase = $total->getData(\Magento\Quote\Api\Data\TotalsInterface::KEY_BASE_GRAND_TOTAL);
        if ($grandBase > 0) {
            /* this is shipping address, compose result (skip processing for billing address)*/
            $subtotal = $total->getBaseTotalAmount(Cfg::CODE_TOTAL_SUBTOTAL);
            $discount = $total->getBaseTotalAmount(Cfg::CODE_TOTAL_DISCOUNT);
            $quoteGrand = $subtotal - $discount;
        }
        /* there is no difference between PV and base PV values */
        $total->setBaseTotalAmount(self::CODE, $quoteGrand);
        $total->setTotalAmount(self::CODE, $quoteGrand);
        return $this;
    }

}