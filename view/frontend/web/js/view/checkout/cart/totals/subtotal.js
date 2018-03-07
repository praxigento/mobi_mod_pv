/**
 *
 */
define([
    "Praxigento_Pv/js/view/checkout/cart/totals/base",
    "Magento_Checkout/js/model/totals"
], function (Component, uiTotals) {
    "use strict";

    /* save totals uiComponent to local context */
    const totals = uiTotals;

    /* see \Praxigento\Pv\Helper\PvProvider::JSON_TOTAL_SEG_SUBTOTAL */
    const SEGMENT = 'prxgt_pv_cart_subtotal';
    /* see \Praxigento\Pv\Helper\PvProvider::JSON_TOTAL_SEG_GRAND*/
    const SEGMENT_GRAND = 'prxgt_pv_cart_grand';

    /* pin prototype function to use in overridden 'isVisible' */
    const fnIsVisible = Component.prototype.isVisible;

    function getAmountGrand() {
        var result = 0;
        if (totals && totals.getSegment(SEGMENT_GRAND)) {
            result = totals.getSegment(SEGMENT_GRAND).value;
        }
        return Number(result);
    }

    var result = Component.extend({
        defaults: {
            title: "PV Subtotal",
            segmentName: SEGMENT
        },

        /**
         * Extend visibility for summary node - hide if subtotal equals to grand.
         *
         * @returns {boolean}
         */
        isVisible: function () {
            const parentResult = fnIsVisible.call(this);
            const valueSub = this.getAmount();
            const valueGrand = getAmountGrand();
            const result = parentResult && (valueSub != valueGrand);
            return result;
        }
    });

    return result;
});