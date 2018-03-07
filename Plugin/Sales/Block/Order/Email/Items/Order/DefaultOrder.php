<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Sales\Block\Order\Email\Items\Order;

use Magento\Sales\Model\Order\Item as OrderItem;
use Praxigento\Pv\Repo\Entity\Data\Quote\Item as EPvQuoteItem;

/**
 * Add PV total to order item in sale order email.
 */
class DefaultOrder
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

    /**
     * @param \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Model\Order\Item $item
     * @return mixed
     */
    public function aroundGetItemPrice(
        \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder $subject,
        \Closure $proceed,
        $item
    ) {
        $result = $proceed($item);
        if ($item instanceof OrderItem) {
            $canSeePv = $this->hlpCust->canSeePv();
            if ($canSeePv) {
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
        }
        return $result;
    }
}