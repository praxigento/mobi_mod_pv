/**
 *
 */
define([
    "Praxigento_Pv/js/view/checkout/cart/totals/base"
], function (Component) {
    "use strict";

    /* see \Praxigento\Pv\Helper\ConfigProvider::JSON_TOTAL_SEG_DISCOUNT */
    const SEGMENT = 'prxgt_pv_cart_discount';

    var result = Component.extend({
        defaults: {
            title: "PV Discount",
            segmentName: SEGMENT
        }
    });
    return result;
});