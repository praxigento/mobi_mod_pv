<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Catalog\Block\Product\Widget;


class NewWidget
{
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;

    public function __construct(
        \Praxigento\Pv\Helper\Customer $hlpCust
    ) {
        $this->hlpCust = $hlpCust;
    }

    public function aroundGetProductPriceHtml(
        \Magento\Catalog\Block\Product\Widget\NewWidget $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        $result = $proceed($product, $priceType, $renderZone, $arguments);
        $canSeePv = $this->hlpCust->canSeePv();
        if ($canSeePv) {
            $domId = "prxgt_pv_new_" . $product->getId();
            $pvWholesale = $product->getData(\Praxigento\Pv\Plugin\Catalog\Model\ResourceModel\Product\CollectionFactory::AS_ATTR_PV);
            $pvWholesale = number_format($pvWholesale, 2);
            $html = "<div id=\"$domId\"><span>$pvWholesale</span> PV</div>";
            $result = $html . $result;
        }
        return $result;
    }

}