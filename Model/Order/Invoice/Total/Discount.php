<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Model\Order\Invoice\Total;

use Praxigento\Pv\Config as Cfg;

/**
 * TODO: MOBI-1069, use it or clean it
 */
class Discount
    extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
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
        \Magento\Sales\Model\Order\Invoice $invoice
    ) {
        /* init total structure */
        parent::collect($invoice);
        return $this;
    }

}