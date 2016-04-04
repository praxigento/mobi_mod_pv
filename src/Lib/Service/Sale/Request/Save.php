<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Pv\Lib\Service\Sale\Request;

use Praxigento\Pv\Data\Entity\Sale;

class Save extends \Praxigento\Core\Lib\Service\Base\Request {
    /* // @formatter:off
$data = [
    Sale::ATTR_SALE_ID  => 100,
    Sale::ATTR_SUBTOTAL => 500,
    Sale::ATTR_DISCOUNT => 50,
    Sale::ATTR_TOTAL    => 450,
    'items'             => [
        1024 => [
            SaleItem::ATTR_SALE_ITEM_ID => 1024,
            Sale::ATTR_SUBTOTAL         => 250,
            Sale::ATTR_DISCOUNT         => 50,
            Sale::ATTR_TOTAL            => 200
        ]
    ]
];
// @formatter:on
*/

    /* attribute to mark items data array in sale order data array */
    const DATA_ITEMS = 'item';

    /**
     * Get order only data related to PV processing.
     *
     * @return array
     */
    public function getOrderData() {
        $result = [
            Sale::ATTR_SALE_ID  => $this->getData(Sale::ATTR_SALE_ID),
            Sale::ATTR_SUBTOTAL => $this->getData(Sale::ATTR_SUBTOTAL),
            Sale::ATTR_DISCOUNT => $this->getData(Sale::ATTR_DISCOUNT),
            Sale::ATTR_TOTAL    => $this->getData(Sale::ATTR_TOTAL),
        ];
        return $result;
    }

    /**
     * Get array of the order items data related to PV processing.
     * @return array
     */
    public function getOrderItemsData() {
        $result = $this->getData(self::DATA_ITEMS);
        return $result;
    }
}