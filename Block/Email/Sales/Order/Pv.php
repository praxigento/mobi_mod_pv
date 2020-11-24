<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Pv\Block\Email\Sales\Order;

use Praxigento\Pv\Repo\Data\Quote as EPvQuote;

/**
 * Add PV to sale order email.
 *
 * see ./view/frontend/layout/sales_email_order_items.xml
 */
class Pv
    extends \Magento\Framework\View\Element\Template
{
    const PV_TOTAL = 'prxgt_pv_total_email';

    /** @var \Praxigento\Pv\Repo\Dao\Quote */
    private $daoPvQuote;
    /** @var \Praxigento\Pv\Helper\Customer */
    private $hlpCust;
    /** @var \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Praxigento\Pv\Repo\Dao\Quote $daoPvQuote,
        \Praxigento\Core\Api\Helper\Format $hlpFormat,
        \Praxigento\Pv\Helper\Customer $hlpCust
    ) {
        parent::__construct($context, $data);
        $this->daoPvQuote = $daoPvQuote;
        $this->hlpFormat = $hlpFormat;
        $this->hlpCust = $hlpCust;
    }

    public function initTotals()
    {
        /* get order data */
        /** @var \Magento\Sales\Block\Order\Totals $parent */
        $parent = $this->getParentBlock();
        $order = $parent->getOrder();
        $quoteId = $order->getQuoteId();
        $groupId = $order->getCustomerGroupId();
        $canSeePv = $this->hlpCust->canSeePv($groupId);
        if ($canSeePv) {
            /* get PV data */
            $pk = [EPvQuote::A_QUOTE_REF => $quoteId];
            $entity = $this->daoPvQuote->getById($pk);

            if ($entity) {
                $value = $entity->getTotal();
                $value = $this->hlpFormat->toNumber($value);
                /* compose total data for 'module-sales/view/frontend/templates/order/totals.phtml' */
                $grand = new \Magento\Framework\DataObject();
                $grand->setCode(self::PV_TOTAL);
                $grand->setValue($value);
                $grand->setLabel('PV Total');
                $grand->setStrong(true);
                $grand->setIsFormated(true);
                $parent->addTotal($grand);
            }
        }
    }
}
