<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Block\Adminhtml\Order;

use Praxigento\Pv\Repo\Entity\Data\Sale as EPvSale;

class Totals
    extends \Magento\Framework\View\Element\Template
{
    /** @var \Praxigento\Pv\Repo\Entity\Sale */
    private $repoPvSale;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Praxigento\Pv\Repo\Entity\Sale $repoPvSale,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->repoPvSale = $repoPvSale;
    }

    public function initTotals()
    {
        /** @var \Magento\Sales\Block\Adminhtml\Order\Totals $parent */
        $parent = $this->getParentBlock();
        /** @var \Magento\Sales\Model\Order $sale */
        $sale = $parent->getOrder();
        $saleId = $sale->getId();
        /** @var EPvSale $found */
        $found = $this->repoPvSale->getById($saleId);
        if ($found) {
            $subtotal = $found->getSubtotal();
            $discount = $found->getDiscount();
            $grand = $found->getTotal();
            $subtotal = number_format($subtotal, 2, '.', '');
            $discount = number_format($discount, 2, '.', '');
            $grand = number_format($grand, 2, '.', '');
            $subtotal = new \Magento\Framework\DataObject(
                [
                    'code' => 'prxgt_pv_subtotal',
                    'strong' => true,
                    'base_value' => $subtotal,
                    'value' => $subtotal,
                    'label' => __('PV Subtotal'),
                    'is_formated' => true
                ]
            );
            $discount = new \Magento\Framework\DataObject(
                [
                    'code' => 'prxgt_pv_discount',
                    'strong' => true,
                    'base_value' => $discount,
                    'value' => $discount,
                    'label' => __('PV Discount'),
                    'is_formated' => true
                ]
            );
            $grand = new \Magento\Framework\DataObject(
                [
                    'code' => 'prxgt_pv_grand',
                    'strong' => true,
                    'base_value' => $grand,
                    'value' => $grand,
                    'label' => __('PV Total'),
                    'is_formated' => true
                ]
            );
            /** add totals to the first  position in back order */
            $parent->addTotal($grand, 'first');
            $parent->addTotal($discount, 'first');
            $parent->addTotal($subtotal, 'first');
        }
        return $this;
    }
}