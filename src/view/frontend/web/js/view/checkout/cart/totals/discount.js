/**
 *
 */
define([
    "Praxigento_Pv/js/view/checkout/cart/totals/base"
], function (Component) {
    "use strict";

    /* see \Praxigento\Pv\Plugin\Quote\Model\Cart\CartTotalRepository::SEGMENT_DISCOUNT */
    const SEGMENT = 'prxgt_pv_discount';

    var result = Component.extend({
        defaults: {
            title: "PV Discount",
            segmentName: SEGMENT
        }
    });
    return result;
});