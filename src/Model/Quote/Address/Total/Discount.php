<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Model\Quote\Address\Total;

use Praxigento\Pv\Config as Cfg;

class Discount
    extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /** Code for total itself */
    const CODE = Cfg::CODE_TOTAL_DISCOUNT;
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
        parent::collect($quote, $shippingAssignment, $total);
        return $this;
    }

}