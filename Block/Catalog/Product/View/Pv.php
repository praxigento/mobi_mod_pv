<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Block\Catalog\Product\View;

/**
 * Display PV using template "./view/frontend/templates/catalog/product/view/pv.phtml"
 */
class Pv
    extends \Magento\Framework\View\Element\Template
{
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;
    /** @var \Praxigento\Pv\Api\Helper\GetPv */
    private $hlpGetPv;
    /** @var \Magento\Framework\Registry */
    private $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Magento\Framework\Registry $registry,
        \Praxigento\Pv\Helper\Customer $hlpCust,
        \Praxigento\Pv\Api\Helper\GetPv $hlpGetPv
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->hlpCust = $hlpCust;
        $this->hlpGetPv = $hlpGetPv;
    }

    /**
     * 'true' - if customer has permissions to see PV on the front.
     * @return bool
     */
    public function canSeePv()
    {
        $result = $this->hlpCust->canSeePv();
        return $result;
    }

    public function getWarehousePv()
    {
        $product = $this->registry->registry('product');
        $prodId = $product->getId();
        $result = $this->hlpGetPv->product($prodId);
        return $result;
    }
}