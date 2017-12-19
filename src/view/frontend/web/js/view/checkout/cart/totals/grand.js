/**
 *
 */
define([
    "Praxigento_Pv/js/view/checkout/cart/totals/base"
], function (Component) {
    "use strict";

    /* see \Praxigento\Pv\Plugin\Quote\Model\Cart\CartTotalRepository::SEGMENT_GRAND */
    const SEGMENT = 'prxgt_pv_grand';

    var result = Component.extend({
        defaults: {
            title: "PV Total",
            segmentName: SEGMENT
        }
    });
    return result;
});