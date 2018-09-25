<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Magento\Checkout\Model;

use Praxigento\Pv\Plugin\Magento\Framework\Webapi\ServiceOutputProcessor as ARestProc;

/**
 * Add PV related data to cart/quote (totals & items PV) in checkout configuration (JSON, controllers).
 */
class CompositeConfigProvider
{
    /** @var \Praxigento\Pv\Helper\PvProvider */
    private $hlpCfgProvider;

    public function __construct(
        \Praxigento\Pv\Helper\PvProvider $hlpCfgProvider
    ) {
        $this->hlpCfgProvider = $hlpCfgProvider;
    }

    /**
     * Add PV data to cart/quote JSON used in checkout configuration on front (/checkout/):
     * {code:js}
     *      window.checkoutConfig = {};
     * {code}
     *
     * @param \Magento\Checkout\Model\CompositeConfigProvider $subject
     * @param array $result
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\CompositeConfigProvider $subject,
        $result
    ) {
        if (
            isset($result['quoteData']) &&
            isset($result['quoteData']['entity_id']) &&
            isset($result['quoteData']['customer_group_id'])
        ) {
            $cartId = (int)$result['quoteData']['entity_id'];
            $groupId = (int)$result['quoteData']['customer_group_id'];
            $canSeePv = $this->hlpCfgProvider->getCanSeePv($groupId);
            /* add 'Can See PV' flag to the whole structure */
            $result[ARestProc::JSON_CAN_SEE_PV] = $canSeePv;
            if ($canSeePv) {
                /* add PV totals to cart */
                $segments = $result['totalsData']['total_segments'];
                $segments = $this->hlpCfgProvider->addPvToTotalSegments($segments, $cartId);
                $result['totalsData']['total_segments'] = $segments;
                /* add PV totals to items */
                $items = $result['totalsData']['items'];
                $updated = [];
                foreach ($items as $item) {
                    $itemId = $item['item_id'];
                    $total = $this->hlpCfgProvider->getCartItemPv($itemId);
                    $item[ARestProc::JSON_ITEM_CAN_SEE_PV] = $canSeePv;
                    $item[ARestProc::JSON_ITEM_PV_TOTAL] = $total;
                    $updated[] = $item;
                }
                $result['totalsData']['items'] = $updated;
            }
        }
        return $result;
    }
}