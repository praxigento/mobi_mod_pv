<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Plugin\Quote\Model\Cart;

/**
 * @deprecated see \Praxigento\Pv\Helper\PvProvider
 */
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
        return $result;
    }

}