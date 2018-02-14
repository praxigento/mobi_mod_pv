<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Checkout\CustomerData;

/**
 * Add warehouse PV to mini-cart items.
 */
class Cart
{
    const CFG_PV_WRHS = 'prxgt_pv_wrhs';

    /** @var \Praxigento\Pv\Api\Helper\GetPv */
    private $hlpGetPv;
    /** @var \Praxigento\Pv\Repo\Entity\Stock\Item */
    private $repoPvStockItem;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Stock\Item $repoPvStockItem,
        \Praxigento\Pv\Api\Helper\GetPv $hlpGetPv
    ) {
        $this->repoPvStockItem = $repoPvStockItem;
        $this->hlpGetPv = $hlpGetPv;
    }

    public function afterGetSectionData(
        \Magento\Checkout\CustomerData\Cart $subject,
        $result
    ) {
        if ($result && isset($result['items']) && is_array($result['items'])) {
            foreach ($result['items'] as $key => $item) {
                $prodId = $item['product_id'];
                $pvWrhs = $this->hlpGetPv->product($prodId);
                $result['items'][$key][self::CFG_PV_WRHS] = $pvWrhs;
            }
        }
        return $result;
    }
}