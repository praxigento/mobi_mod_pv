<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Checkout\Block\Cart\Item;

use Praxigento\Pv\Repo\Data\Quote\Item as EPvQuoteItem;

class Renderer
{
    /** @var array */
    private $cacheQuoteItems = [];
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;
    /** @var \Praxigento\Pv\Repo\Dao\Quote\Item */
    private $repoPvQuoteItem;

    public function __construct(
        \Praxigento\Pv\Repo\Dao\Quote\Item $repoPvQuoteItem,
        \Praxigento\Pv\Helper\Customer $hlpCust
    ) {
        $this->repoPvQuoteItem = $repoPvQuoteItem;
        $this->hlpCust = $hlpCust;
    }

    public function aroundGetRowTotalHtml(
        \Magento\Checkout\Block\Cart\Item\Renderer $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item
    ) {
        $result = $proceed($item);
        $canSeePv = $this->hlpCust->canSeePv();
        if ($canSeePv) {
            $itemId = $item->getId();
            $entity = $this->getCachedItemPv($itemId);
            if ($entity) {
                $subtotal = $entity->getSubtotal();
                $subtotal = number_format($subtotal, 2);
                $result = "<span>$subtotal PV</span>" . $result;
            }
        }
        return $result;
    }

    public function aroundGetUnitPriceHtml(
        \Magento\Checkout\Block\Cart\Item\Renderer $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item
    ) {
        $result = $proceed($item);
        $canSeePv = $this->hlpCust->canSeePv();
        if ($canSeePv) {
            $itemId = $item->getId();
            $entity = $this->getCachedItemPv($itemId);
            if ($entity) {
                $subtotal = $entity->getSubtotal();
                $qty = $item->getQty();
                $pvUnit = number_format($subtotal / $qty, 2);
                $result = "<span>$pvUnit PV</span>" . $result;
            }
        }
        return $result;
    }

    /**
     * @param $itemId
     * @return EPvQuoteItem|false
     */
    private function getCachedItemPv($itemId)
    {
        if (!isset($this->cacheQuoteItems[$itemId])) {
            $entity = $this->repoPvQuoteItem->getById($itemId);
            $this->cacheQuoteItems[$itemId] = $entity;
        }
        return $this->cacheQuoteItems[$itemId];
    }
}