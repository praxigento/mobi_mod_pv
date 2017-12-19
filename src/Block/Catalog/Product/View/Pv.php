<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Block\Catalog\Product\View;

class Pv
    extends \Magento\Framework\View\Element\Template
{
    /** @var \Praxigento\Pv\Api\Helper\GetPv */
    private $hlpGetPv;
    /** @var \Magento\Framework\Registry */
    private $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Magento\Framework\Registry $registry,
        \Praxigento\Pv\Api\Helper\GetPv $hlpGetPv
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->hlpGetPv = $hlpGetPv;
    }

    public function getWarehousePv() {
        $product = $this->registry->registry('product');
        $prodId = $product->getId();
        $result = $this->hlpGetPv->product($prodId);
        return $result;
    }
}