<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Block\Sales\Order;

use Praxigento\Pv\Repo\Data\Sale as EPvSale;

/**
 * Block to display sale order totals.
 *
 * see:
 *  ./view/adminhtml/layout/sales_order_view.xml
 *  ./view/frontend/layout/sales_order_view.xml
 */
class Totals
    extends \Magento\Framework\View\Element\Template
{
    const PV_DISCOUNT = 'prxgt_pv_discount_sale';
    const PV_GRAND = 'prxgt_pv_grand_sale';
    const PV_SUBTOTAL = 'prxgt_pv_subtotal_sale';

    /** @var \Praxigento\Pv\Repo\Dao\Sale */
    private $daoPvSale;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Praxigento\Pv\Repo\Dao\Sale $daoPvSale,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->daoPvSale = $daoPvSale;
    }

    public function initTotals()
    {
        /** @var \Magento\Sales\Block\Adminhtml\Order\Totals $parent */
        $parent = $this->getParentBlock();
        /** @var \Magento\Sales\Model\Order $sale */
        $sale = $parent->getOrder();
        $saleId = $sale->getId();
        /** @var EPvSale $found */
        $found = $this->daoPvSale->getById($saleId);
        if ($found) {
            $subtotal = $found->getSubtotal();
            $discount = $found->getDiscount();
            $grand = $found->getTotal();
            $subtotal = number_format($subtotal, 2, '.', '');
            $discount = number_format($discount, 2, '.', '');
            $grand = number_format($grand, 2, '.', '');
            $subtotal = new \Magento\Framework\DataObject(
                [
                    'code' => self::PV_SUBTOTAL,
                    'strong' => true,
                    'base_value' => $subtotal,
                    'value' => $subtotal,
                    'label' => __('PV Subtotal'),
                    'is_formated' => true
                ]
            );
            $discount = new \Magento\Framework\DataObject(
                [
                    'code' => self::PV_DISCOUNT,
                    'strong' => true,
                    'base_value' => $discount,
                    'value' => $discount,
                    'label' => __('PV Discount'),
                    'is_formated' => true
                ]
            );
            $grand = new \Magento\Framework\DataObject(
                [
                    'code' => self::PV_GRAND,
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