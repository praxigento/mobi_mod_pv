<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Magento\Checkout\Block\Cart\Item;

use Praxigento\Pv\Repo\Data\Quote\Item as EPvQuoteItem;

class Renderer
{
    /** @var array */
    private $cacheQuoteItems = [];
    /** @var \Praxigento\Pv\Repo\Dao\Quote\Item */
    private $daoPvQuoteItem;
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;
    /** @var \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;

    public function __construct(
        \Praxigento\Pv\Repo\Dao\Quote\Item $daoPvQuoteItem,
        \Praxigento\Pv\Helper\Customer $hlpCust,
        \Praxigento\Core\Api\Helper\Format $hlpFormat
    ) {
        $this->daoPvQuoteItem = $daoPvQuoteItem;
        $this->hlpCust = $hlpCust;
        $this->hlpFormat = $hlpFormat;
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
                $subtotal = $this->hlpFormat->toNumber($subtotal);
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
                $pvUnit = $this->hlpFormat->toNumber($subtotal / $qty);
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
            $entity = $this->daoPvQuoteItem->getById($itemId);
            $this->cacheQuoteItems[$itemId] = $entity;
        }
        return $this->cacheQuoteItems[$itemId];
    }
}