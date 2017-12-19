<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Quote\Model\Cart;

use Praxigento\Pv\Repo\Entity\Data\Quote as EPvQuote;

class CartTotalRepository
{
    const SEGMENT_DISCOUNT = 'prxgt_pv_discount';
    const SEGMENT_GRAND = 'prxgt_pv_grand';
    const SEGMENT_SUBTOTAL = 'prxgt_pv_subtotal';

    /** @var \Magento\Quote\Api\Data\TotalSegmentInterfaceFactory */
    private $factTotalSegment;
    /** @var \Praxigento\Pv\Repo\Entity\Quote */
    private $repoPvQuote;

    public function __construct(
        \Magento\Quote\Api\Data\TotalSegmentInterfaceFactory $factTotalSegment,
        \Praxigento\Pv\Repo\Entity\Quote $repoPvQuote
    ) {
        $this->factTotalSegment = $factTotalSegment;
        $this->repoPvQuote = $repoPvQuote;
    }

    /**
     * Add PV totals to fronted data container
     *
     * @param \Magento\Quote\Model\Cart\CartTotalRepository $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @return \Magento\Quote\Model\Cart\Totals
     */
    public function aroundGet(
        \Magento\Quote\Model\Cart\CartTotalRepository $subject,
        \Closure $proceed,
        $cartId
    ) {
        /** @var \Magento\Quote\Model\Cart\Totals $result */
        $result = $proceed($cartId);
        /* set init values for totals */
        $subtotal = $discount = $grand = 0;
        /* get quote totals by ID */
        $pk = [EPvQuote::ATTR_QUOTE_REF => $cartId];
        $found = $this->repoPvQuote->getById($pk);
        if ($found) {
            $subtotal = $found->getSubtotal();
            $discount = $found->getDiscount();
            $grand = $found->getTotal();
        }

        /**
         * Init segments.
         */

        /** @var \Magento\Quote\Api\Data\TotalSegmentInterface $segSub */
        $segSub = $this->factTotalSegment->create();
        $segSub->setCode(self::SEGMENT_SUBTOTAL);
        $segSub->setValue($subtotal);

        /** @var \Magento\Quote\Api\Data\TotalSegmentInterface $segSub */
        $segDiscount = $this->factTotalSegment->create();
        $segDiscount->setCode(self::SEGMENT_DISCOUNT);
        $segDiscount->setValue($discount);

        /** @var \Magento\Quote\Api\Data\TotalSegmentInterface $segSub */
        $segGrand = $this->factTotalSegment->create();
        $segGrand->setCode(self::SEGMENT_GRAND);
        $segGrand->setValue($grand);

        /* add segments to totals */
        $segments = $result->getTotalSegments();
        $segments[self::SEGMENT_SUBTOTAL] = $segSub;
        $segments[self::SEGMENT_DISCOUNT] = $segDiscount;
        $segments[self::SEGMENT_GRAND] = $segGrand;
        $result->setTotalSegments($segments);

        return $result;
    }

}