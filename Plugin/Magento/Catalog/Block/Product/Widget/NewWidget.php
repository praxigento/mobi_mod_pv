<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Magento\Catalog\Block\Product\Widget;

use Praxigento\Pv\Plugin\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as AProdCollFactory;

/**
 * Insert warehouse PV before price HTML for new products (in widget).
 */
class NewWidget
{
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;
    /** @var \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;
    /** @var \Praxigento\Pv\Helper\GetPv */
    private $hlpGetPv;

    public function __construct(
        \Praxigento\Pv\Helper\Customer $hlpCust,
        \Praxigento\Pv\Helper\GetPv $hlpGetPv,
        \Praxigento\Core\Api\Helper\Format $hlpFormat
    ) {
        $this->hlpCust = $hlpCust;
        $this->hlpGetPv = $hlpGetPv;
        $this->hlpFormat = $hlpFormat;
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
            /* did PV added in collection before? */
            $pvWrhs = $product->getData(AProdCollFactory::A_PV_PRODUCT);
            if (!$pvWrhs) {
                /* if not then get PV directly */
                $pvWrhs = $this->hlpGetPv->product($prodId);
            }
            /* format PV and insert before price */
            $pvWrhs = $this->hlpFormat->toNumber($pvWrhs);
            $html = "<div id=\"$domId\"><span>$pvWrhs</span> PV</div>";
            $result = $html . $result;
        }
        return $result;
    }

}