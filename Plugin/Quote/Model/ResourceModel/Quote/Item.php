<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Quote\Model\ResourceModel\Quote;

use Praxigento\Pv\Repo\Data\Quote\Item as EPvQuoteItem;

class Item
{
    /** @var \Praxigento\Pv\Repo\Dao\Quote\Item */
    private $daoPvQuoteItem;
    /** @var \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;
    /** @var \Praxigento\Pv\Api\Helper\GetPv */
    private $hlpGetPv;

    public function __construct(
        \Praxigento\Pv\Repo\Dao\Quote\Item $daoPvQuoteItem,
        \Praxigento\Pv\Api\Helper\GetPv $hlpGetPv,
        \Praxigento\Core\Api\Helper\Format $hlpFormat
    ) {
        $this->daoPvQuoteItem = $daoPvQuoteItem;
        $this->hlpGetPv = $hlpGetPv;
        $this->hlpFormat = $hlpFormat;
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
            $subtotal = $this->hlpFormat->toNumber($pvWrhs * $qty);
            /* create/update PV values for quote item (if changed) */
            $pk = [EPvQuoteItem::A_ITEM_REF => $id];
            $found = $this->daoPvQuoteItem->getById($pk);
            if ($found) {
                /* update PV data if subtotals are different */
                if ($found->getSubtotal() != $subtotal) {
                    /* we don't know discount for the item */
                    $found->setSubtotal($subtotal);
                    $found->setDiscount(0);
                    $found->setTotal($subtotal);
                    $this->daoPvQuoteItem->updateById($pk, $found);
                }
            } else {
                /* create new record */
                $entity = new EPvQuoteItem();
                $entity->setItemRef($id);
                /* we don't know discount for the item */
                $entity->setSubtotal($subtotal);
                $entity->setDiscount(0);
                $entity->setTotal($subtotal);
                $this->daoPvQuoteItem->create($entity);
            }
        }
        return $result;
    }
}