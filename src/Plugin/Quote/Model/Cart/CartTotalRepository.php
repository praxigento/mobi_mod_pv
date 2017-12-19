<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Quote\Model\Cart;


class CartTotalRepository
{
    const SEGMENT_DISCOUNT = 'prxgt_pv_discount';
    const SEGMENT_GRAND = 'prxgt_pv_grand';
    const SEGMENT_SUBTOTAL = 'prxgt_pv_subtotal';

    /** @var \Magento\Quote\Api\Data\TotalSegmentInterfaceFactory */
    private $factTotalSegment;

    public function __construct(
        \Magento\Quote\Api\Data\TotalSegmentInterfaceFactory $factTotalSegment
    ) {
        $this->factTotalSegment = $factTotalSegment;
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

        /** @var \Magento\Quote\Api\Data\TotalSegmentInterface $segSub */
        $segSub = $this->factTotalSegment->create();
        $segSub->setCode(self::SEGMENT_SUBTOTAL);
        $segSub->setValue(1024);

        /** @var \Magento\Quote\Api\Data\TotalSegmentInterface $segSub */
        $segDiscount = $this->factTotalSegment->create();
        $segDiscount->setCode(self::SEGMENT_DISCOUNT);
        $segDiscount->setValue(10);


        /** @var \Magento\Quote\Api\Data\TotalSegmentInterface $segSub */
        $segGrand = $this->factTotalSegment->create();
        $segGrand->setCode(self::SEGMENT_GRAND);
        $segGrand->setValue(1014);

        /* add segments to totals */
        $segments = $result->getTotalSegments();
        $segments[self::SEGMENT_SUBTOTAL] = $segSub;
        $segments[self::SEGMENT_DISCOUNT] = $segDiscount;
        $segments[self::SEGMENT_GRAND] = $segGrand;
        $result->setTotalSegments($segments);

        return $result;
    }

}