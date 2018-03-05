/**
 *
 */
define([
    "Praxigento_Pv/js/view/checkout/cart/totals/base"
], function (Component) {
    "use strict";

    /* see \Praxigento\Pv\Plugin\Checkout\Model\CompositeConfigProvider::JSON_TOTAL_SEG_GRAND */
    const SEGMENT = 'prxgt_pv_cart_grand';

    var result = Component.extend({
        defaults: {
            title: "PV Total",
            segmentName: SEGMENT
        }
    });
    return result;
});