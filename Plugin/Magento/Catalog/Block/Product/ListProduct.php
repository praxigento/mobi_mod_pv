<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Magento\Catalog\Block\Product;

use Praxigento\Pv\Plugin\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as AProdCollFactory;

class ListProduct
{
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;
    /** @var \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;

    public function __construct(
        \Praxigento\Pv\Helper\Customer $hlpCust,
        \Praxigento\Core\Api\Helper\Format $hlpFormat
    ) {
        $this->hlpCust = $hlpCust;
        $this->hlpFormat = $hlpFormat;
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
            $pvProd = $product->getData(AProdCollFactory::A_PV_PRODUCT);
            $pvProd = $this->hlpFormat->toNumber($pvProd);
            $html = "<div id=\"$domId\"><span>$pvProd</span> PV</div>";
            $result = $html . $result;
        }
        return $result;
    }
}