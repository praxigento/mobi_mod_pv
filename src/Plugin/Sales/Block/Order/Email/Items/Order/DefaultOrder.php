<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Sales\Block\Order\Email\Items\Order;

use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use \Praxigento\Pv\Repo\Entity\Data\Quote\Item as EPvQuoteItem;

class DefaultOrder
{
    /** @var \Praxigento\Pv\Repo\Entity\Quote\Item */
    private $repoPvQuoteItem;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Quote\Item $repoPvQuoteItem
    ) {
        $this->repoPvQuoteItem = $repoPvQuoteItem;
    }

    /**
     * @param \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder $subject
     * @param \Closure $proceed
     * @param OrderItem|InvoiceItem|CreditmemoItem $item
     * @return mixed
     */
    public function aroundGetItemPrice(
        \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder $subject,
        \Closure $proceed,
        $item
    ) {
        $result = $proceed($item);
        if ($item instanceof OrderItem) {
            $quoteItemId = $item->getQuoteItemId();
            $pk = [EPvQuoteItem::ATTR_ITEM_REF => $quoteItemId];
            $entity = $this->repoPvQuoteItem->getById($pk);
            if ($entity) {
                $total = $entity->getTotal();
                $total = number_format($total, 2);
                $html = "\n<br /><span class=\"label\">PV Total:</span> <span class=\"price\">$total</span>";
                $result .= $html;
            }
        }
        return $result;
    }
}