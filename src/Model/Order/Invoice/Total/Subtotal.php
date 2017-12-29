<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Model\Order\Invoice\Total;

use Praxigento\Pv\Config as Cfg;

/**
 * TODO: MOBI-1069, use it or clean it
 */
class Subtotal
    extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /** Code for total itself */
    const CODE = Cfg::CODE_TOTAL_SUBTOTAL;
    /** @var \Praxigento\Pv\Api\Helper\GetPv */
    private $hlpGetPv;
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface */
    private $hlpPriceCurrency;

    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $hlpPriceCurrency,
        \Praxigento\Pv\Api\Helper\GetPv $hlpGetPv
    ) {
        $this->hlpPriceCurrency = $hlpPriceCurrency;
        $this->hlpGetPv = $hlpGetPv;
    }

    public function collect(
        \Magento\Sales\Model\Order\Invoice $invoice
    ) {
        /* init total structure */
        parent::collect($invoice);
        return $this;
    }

}