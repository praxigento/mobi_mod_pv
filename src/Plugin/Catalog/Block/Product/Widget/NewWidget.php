<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Catalog\Block\Product\Widget;

/**
 * Insert warehouse PV before price HTML for new products (in widget).
 */
class NewWidget
{
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;
    /** @var \Praxigento\Pv\Helper\GetPv */
    private $hlpGetPv;
    public function __construct(
        \Praxigento\Pv\Helper\Customer $hlpCust,
        \Praxigento\Pv\Helper\GetPv $hlpGetPv
    ) {
        $this->hlpCust = $hlpCust;
        $this->hlpGetPv = $hlpGetPv;
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
            $prodId = $product->getId();
            $domId = "prxgt_pv_new_" . $prodId;
            $pvWrhs = $this->hlpGetPv->product($prodId);
            $pvWrhs = number_format($pvWrhs, 2, '.', '');
            $html = "<div id=\"$domId\"><span>$pvWrhs</span> PV</div>";
            $result = $html . $result;
        }
        return $result;
    }

}