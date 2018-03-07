<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Block\Sales\Order;

use Praxigento\Pv\Repo\Entity\Data\Quote as EPvQuote;

class Pv
    extends \Magento\Framework\View\Element\Template
{
    /** @var \Praxigento\Pv\Repo\Entity\Quote */
    private $repoPvQuote;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Praxigento\Pv\Repo\Entity\Quote $repoPvQuote
    ) {
        parent::__construct($context, $data);
        $this->repoPvQuote = $repoPvQuote;
    }

    public function initTotals() {
        /* get order data */
        /** @var \Magento\Sales\Block\Order\Totals $parent */
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        $quoteId = $order->getQuoteId();
        /* get PV data */
        $pk = [EPvQuote::ATTR_QUOTE_REF => $quoteId];
        $entity = $this->repoPvQuote->getById($pk);

        if ($entity) {
            $value = $entity->getTotal();
            $value = number_format($value, 2);
            /* compose total data for 'module-sales/view/frontend/templates/order/totals.phtml' */
            $pvSubtotal = new \Magento\Framework\DataObject();
            $pvSubtotal->setCode('prxgt_pv_total');
            $pvSubtotal->setValue($value);
            $pvSubtotal->setLabel('PV Total');
            $pvSubtotal->setStrong(true);
            $pvSubtotal->setIsFormated(true);
            $parent->addTotal($pvSubtotal, 'last');
        }
    }
}