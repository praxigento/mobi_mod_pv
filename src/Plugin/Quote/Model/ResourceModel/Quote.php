<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Quote\Model\ResourceModel;

use Praxigento\Pv\Config as Cfg;
use Praxigento\Pv\Repo\Entity\Data\Quote as EPvQuote;

class Quote
{
    /** @var \Praxigento\Pv\Repo\Entity\Quote */
    private $repoPvQuote;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Quote $repoPvQuote
    ) {
        $this->repoPvQuote = $repoPvQuote;
    }

    /**
     * Create/update PV for quote on save.
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote $object
     * @return mixed
     */
    public function aroundSave(
        \Magento\Quote\Model\ResourceModel\Quote $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote $object
    ) {
        $result = $proceed($object);
        $isDeleted = $object->isDeleted();
        $isPreventSaving = $object->isPreventSaving();
        if (!$isDeleted && !$isPreventSaving) {
            $id = $object->getId();
            $addr = $object->getShippingAddress();
            $subtotal = $addr->getData(Cfg::CODE_TOTAL_SUBTOTAL . '_amount');
            $discount = $addr->getData(Cfg::CODE_TOTAL_DISCOUNT . '_amount');
            $grand = $addr->getData(Cfg::CODE_TOTAL_GRAND . '_amount');
            /* create/update PV values for quote (if changed) */
            $pk = [EPvQuote::ATTR_QUOTE_REF => $id];
            $found = $this->repoPvQuote->getById($pk);
            if ($found) {
                /* update PV data if subtotals are different */
                if (
                    ($found->getSubtotal() != $subtotal) ||
                    ($found->getDiscount() != $discount) ||
                    ($found->getTotal() != $grand)
                ) {
                    $found->setSubtotal($subtotal);
                    $found->setDiscount($discount);
                    $found->setTotal($grand);
                    $this->repoPvQuote->updateById($pk, $found);
                }
            } else {
                /* create new record */
                $entity = new EPvQuote();
                $entity->setQuoteRef($id);
                $entity->setSubtotal($subtotal);
                $entity->setDiscount($discount);
                $entity->setTotal($grand);
                $this->repoPvQuote->create($entity);
            }
        }
        return $result;
    }
}