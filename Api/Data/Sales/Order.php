<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Pv\Api\Data\Sales;

/**
 * Additional attributes for Magento's "Sales Order" model.
 */
interface Order
{
    /**
     * Additional Sales Order attributes. They are used to get data from DB and put in
     * Sales Order model in query builders and to get data from Sales Order model in other classes
     * (plugins, observers, services, ...)
     */
    const A_PV_DISCOUNT = 'prxgt_pv_discount';
    const A_PV_GRAND = 'prxgt_pv_grand';
    const A_PV_SUBTOTAL = 'prxgt_pv_subtotal';
}