<?php
/**
 * Authors: Alex Gusev <flancer64@gmail.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Plugin\Sales\Block\Adminhtml\Order\View\Items\Renderer;

/**
 * Add PV data to columns HTML in order.
 */
class DefaultRenderer
{
    /** @var array \Praxigento\Pv\Repo\Entity\Data\Quote\Item[] */
    private $cacheQuoteItems = [];

    /** @var \Praxigento\Pv\Repo\Entity\Quote\Item */
    private $repoPvQuoteItem;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\Quote\Item $repoPvQuoteItem
    ) {
        $this->repoPvQuoteItem = $repoPvQuoteItem;
    }

    public function aroundGetColumnHtml(
        \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $item,
        $column,
        $field = null
    ) {
        $result = $proceed($item, $column, $field);
        $itemId = $item->getId();
        $qty = $item->getQtyOrdered();
        $pvItem = $this->getPvQuoteItem($itemId);
        switch ($column) {
            case 'price':
                $result .= $this->htmlForColumnPrice($pvItem, $qty);
                break;
            case 'subtotal':
                $result .= $this->htmlForColumnSubtotal($pvItem);
                break;
            case 'discont':
                $result .= $this->htmlForColumnDiscount($pvItem);
                break;
            case 'total':
                $result .= $this->htmlForColumnTotal($pvItem);
                break;
        }
        return $result;
    }

    /**
     * @param int $itemId
     * @return \Praxigento\Pv\Repo\Entity\Data\Quote\Item
     */
    private function getPvQuoteItem($itemId)
    {
        if (!isset($this->cacheQuoteItems[$itemId])) {
            $item = $this->repoPvQuoteItem->getById($itemId);
            $this->cacheQuoteItems[$itemId] = $item;
        }
        return $this->cacheQuoteItems[$itemId];
    }

    /**
     * @param \Praxigento\Pv\Repo\Entity\Data\Quote\Item $pvItem
     * @return string
     */
    private function htmlForColumnDiscount($pvItem)
    {
        $val = $pvItem->getDiscount();
        $val = number_format($val, 2, '.', '');
        $result = "<div>$val PV</div>";
        return $result;
    }

    /**
     * @param \Praxigento\Pv\Repo\Entity\Data\Quote\Item $pvItem
     * @param int $qty
     * @return string
     */
    private function htmlForColumnPrice($pvItem, $qty)
    {
        $subtotal = $pvItem->getSubtotal();
        $val = ($qty > 0) ? $subtotal / $qty : $subtotal;
        $val = number_format($val, 2, '.', '');
        $result = "<div>$val PV</div>";
        return $result;
    }

    /**
     * @param \Praxigento\Pv\Repo\Entity\Data\Quote\Item $pvItem
     * @return string
     */
    private function htmlForColumnSubtotal($pvItem)
    {
        $val = $pvItem->getSubtotal();
        $val = number_format($val, 2, '.', '');
        $result = "<div>$val PV</div>";
        return $result;
    }

    /**
     * @param \Praxigento\Pv\Repo\Entity\Data\Quote\Item $pvItem
     * @return string
     */
    private function htmlForColumnTotal($pvItem)
    {
        $val = $pvItem->getTotal();
        $val = number_format($val, 2, '.', '');
        $result = "<div>$val PV</div>";
        return $result;
    }
}