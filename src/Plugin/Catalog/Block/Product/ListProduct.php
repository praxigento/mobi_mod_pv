<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Catalog\Block\Product;


class ListProduct
{

    public function aroundGetProductPrice(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);
        $domId = "prxgt_pv_" . $product->getId();
        $pvWholesale = $product->getData(\Praxigento\Pv\Plugin\Catalog\Model\Layer::AS_PV_WRHS);
        $pvWholesale = number_format($pvWholesale, 2);
        $html = "<div id=\"$domId\"><span>$pvWholesale</span> PV</div>";
        $result = $html . $result;
        return $result;
    }
}