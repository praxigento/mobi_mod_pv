/**
 *
 */
define([
    "Praxigento_Pv/js/view/checkout/cart/totals/base"
], function (Component) {
    "use strict";

    /* see \Praxigento\Pv\Plugin\Quote\Model\Cart\CartTotalRepository::SEGMENT_SUBTOTAL */
    const SEGMENT = 'prxgt_pv_subtotal';

    var result = Component.extend({
        defaults: {
            title: "PV Subtotal",
            segmentName: SEGMENT
        }
    });
    return result;
});