<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Quote\Model\ResourceModel\Quote;

use Praxigento\Pv\Repo\Entity\Data\Quote\Item as EPvQuoteItem;

class Item
{
    /** @var \Praxigento\Pv\Repo\Entity\Quote\Item */
    private $repoPvQuoteItem;
    /** @var \Praxigento\Pv\Api\Helper\GetPv */
    private $hlpGetPv;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Quote\Item $repoPvQuoteItem,
        \Praxigento\Pv\Api\Helper\GetPv $hlpGetPv
    ) {
        $this->repoPvQuoteItem = $repoPvQuoteItem;
        $this->hlpGetPv = $hlpGetPv;
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
        $isDeleted = $object->isDeleted();
        if (!$isDeleted) {
            $id = $object->getId();
            $qty = $object->getQty();
            $product = $object->getProduct();
            $productId = $product->getId();
            $pvWrhs = $this->hlpGetPv->product($productId);
            $subtotal = number_format($pvWrhs * $qty, 2);
            /* create/update PV values for quote item (if changed) */
            $pk = [EPvQuoteItem::ATTR_ITEM_REF => $id];
            $found = $this->repoPvQuoteItem->getById($pk);
            if ($found) {
                /* update PV data if subtotals are different */
                if ($found->getSubtotal() != $subtotal) {
                    /* we don't know discount for the item */
                    $found->setSubtotal($subtotal);
                    $found->setDiscount(0);
                    $found->setTotal($subtotal);
                    $this->repoPvQuoteItem->updateById($pk, $found);
                }
            } else {
                /* create new record */
                $entity = new EPvQuoteItem();
                $entity->setItemRef($id);
                /* we don't know discount for the item */
                $entity->setSubtotal($subtotal);
                $entity->setDiscount(0);
                $entity->setTotal($subtotal);
                $this->repoPvQuoteItem->create($entity);
            }
        }
        return $result;
    }
}