<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Checkout\Model;

use Praxigento\Pv\Repo\Entity\Data\Quote\Item as EQuoteItem;

class CompositeConfigProvider
{
    const CFG_CAN_SEE_PV = 'prxgt_pv_can_see';
    const CFG_PV_TOTAL = 'prxgt_pv_total';

    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpPvCust;
    /** @var \Praxigento\Pv\Repo\Entity\Quote\Item */
    private $repoQuoteItem;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Quote\Item $repoQuoteItem,
        \Praxigento\Pv\Helper\Customer $hlpPvCust
    ) {
        $this->repoQuoteItem = $repoQuoteItem;
        $this->hlpPvCust = $hlpPvCust;
    }

    /**
     * Add PV data to cart/quote JSON used in checkout (step 01).
     *
     * @param \Magento\Checkout\Model\CompositeConfigProvider $subject
     * @param array $result
     * @return mixed
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\CompositeConfigProvider $subject,
        $result
    ) {
        if (
            isset($result['totalsData']) &&
            isset($result['totalsData']['items']) &&
            is_array($result['totalsData']['items'])
        ) {
            $canSeePv = $this->hlpPvCust->canSeePv();
            $items = $result['totalsData']['items'];
            foreach ($items as $key => $item) {
                $id = $item['item_id'];
                /** @var EQuoteItem $pvData */
                $pvData = $this->repoQuoteItem->getById($id);
                if ($pvData) {
                    $total = $pvData->getTotal();
                    $total = number_format($total, 2, '.', '');
                    $item[self::CFG_PV_TOTAL] = $total;
                }
                $item[self::CFG_CAN_SEE_PV] = $canSeePv;
                $items[$key] = $item;
            }
            $result['totalsData']['items'] = $items;
        }
        return $result;
    }
}