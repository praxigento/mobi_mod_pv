<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Magento\Sales\Block\Order\Email\Items\Order;

use Magento\Sales\Model\Order\Item as OrderItem;
use Praxigento\Pv\Repo\Data\Quote\Item as EPvQuoteItem;

/**
 * Add PV total to order item in sale order email.
 */
class DefaultOrder
{
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
            $gid = $item->getOrder()->getCustomerGroupId();
            $canSeePv = $this->hlpCust->canSeePv($gid);
            if ($canSeePv) {
                $quoteItemId = $item->getQuoteItemId();
                $pk = [EPvQuoteItem::A_ITEM_REF => $quoteItemId];
                $entity = $this->daoPvQuoteItem->getById($pk);
                if ($entity) {
                    $total = $entity->getTotal();
                    $total = $this->hlpFormat->toNumber($total);
                    $html = "\n<br /><span class=\"label\">PV Total:</span> <span class=\"price\">$total</span>";
                    $result .= $html;
                }
            }
        }
        return $result;
    }
}