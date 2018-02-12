<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Catalog\Block\Product;


class ListProduct
{
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;

    public function __construct(
        \Praxigento\Pv\Helper\Customer $hlpCust
    ) {
        $this->hlpCust = $hlpCust;
    }

    /**
     * Insert PV HTML before product price HTML.
     *
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed|string
     */
    public function aroundGetProductPrice(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);
        $canSeePv = $this->hlpCust->canSeePv();
        if ($canSeePv) {
            $domId = "prxgt_pv_" . $product->getId();
            $pvWholesale = $product->getData(\Praxigento\Pv\Plugin\Catalog\Model\Layer::AS_ATTR_PV_WRHS);
            $pvWholesale = number_format($pvWholesale, 2);
            $html = "<div id=\"$domId\"><span>$pvWholesale</span> PV</div>";
            $result = $html . $result;
        }
        return $result;
    }
}