<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Quote\Model\ResourceModel\Quote;

use Praxigento\Pv\Repo\Entity\Data\Quote\Item as EPvQuoteItem;

class Item
{
    private $repoPvQuoteItem;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Quote\Item $repoPvQuoteItem
    ) {
        $this->repoPvQuoteItem = $repoPvQuoteItem;
    }

    /**
     * Create/update PV for quote item if item is saved.
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item $object
     * @return mixed
     */
    public function aroundSave(
        \Magento\Quote\Model\ResourceModel\Quote\Item $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item $object
    ) {
        $result = $proceed($object);
        /* create/update PV quote for quote item (if changed) */
        $id = $object->getId();
        $pk = [EPvQuoteItem::ATTR_ITEM_REF => $id];
        $found = $this->repoPvQuoteItem->getById($pk);
        if ($found) {
            /* update PV data if different */
            if (
                ($found->getSubtotal() != 125) ||
                ($found->getDiscount() != 23) ||
                ($found->getTotal() != 100)) {
                $found->setSubtotal(125);
                $found->setDiscount(23);
                $found->setTotal(100);
                $this->repoPvQuoteItem->updateById($pk, $found);
            }
        } else {
            /* create new record */
            $entity = new EPvQuoteItem();
            $entity->setItemRef($id);
            /* TODO: extract PV info from quote item */
            $entity->setSubtotal(123);
            $entity->setDiscount(23);
            $entity->setTotal(100);
            $this->repoPvQuoteItem->create($entity);
        }
        return $result;
    }
}