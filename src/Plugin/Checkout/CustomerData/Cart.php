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
    const CFG_CAN_SEE_PV = 'prxgt_pv_can_see';
    const CFG_ITEM_PV_WRHS = 'prxgt_pv_wrhs';

    /** @var \Praxigento\Pv\Api\Helper\GetPv */
    private $hlpGetPv;
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpPvCust;
    /** @var \Praxigento\Pv\Repo\Entity\Stock\Item */
    private $repoPvStockItem;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Stock\Item $repoPvStockItem,
        \Praxigento\Pv\Helper\Customer $hlpPvCust,
        \Praxigento\Pv\Api\Helper\GetPv $hlpGetPv
    ) {
        $this->repoPvStockItem = $repoPvStockItem;
        $this->hlpPvCust = $hlpPvCust;
        $this->hlpGetPv = $hlpGetPv;
    }

    public function afterGetSectionData(
        \Magento\Checkout\CustomerData\Cart $subject,
        $result
    ) {
        if (is_array($result)) {
            $canSeePv = $this->hlpPvCust->canSeePv();
            $result[self::CFG_CAN_SEE_PV] = $canSeePv;
            if ($canSeePv) {
                if (isset($result['items']) && is_array($result['items'])) {
                    foreach ($result['items'] as $key => $item) {
                        $prodId = $item['product_id'];
                        $pvWrhs = $this->hlpGetPv->product($prodId);
                        $result['items'][$key][self::CFG_ITEM_PV_WRHS] = $pvWrhs;
                        /* this is not good idea, but "This is MAGENTA-A-A-A!!!!" */
                        /* it is not a big price to have a 'canSeePv' flag for each item */
                        $result['items'][$key][self::CFG_CAN_SEE_PV] = $canSeePv;
                    }
                }
            }
        }
        return $result;
    }
}