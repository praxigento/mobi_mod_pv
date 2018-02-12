<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Checkout\Block\Cart\Item;

class Renderer
{
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;
    /** @var \Praxigento\Pv\Repo\Entity\Quote\Item */
    private $repoPvQuoteItem;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Quote\Item $repoPvQuoteItem,
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
            $entity = $this->repoPvQuoteItem->getById($itemId);
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
            $entity = $this->repoPvQuoteItem->getById($itemId);
            if ($entity) {
                $subtotal = $entity->getSubtotal();
                $qty = $item->getQty();
                $pvUnit = number_format($subtotal / $qty, 2);
                $result = "<span>$pvUnit PV</span>" . $result;
            }
        }
        return $result;
    }
}