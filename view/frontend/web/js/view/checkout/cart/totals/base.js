/**
 * Base component for PV totals.
 */
define([
    "Magento_Checkout/js/view/summary/abstract-total",
    "Magento_Checkout/js/model/totals",
    "Magento_Catalog/js/price-utils"
], function (Component, uiTotals, uiPriceUtils) {
    "use strict";

    /* save totals uiComponent to local context */
    const totals = uiTotals;
    /* shortcuts to global objects */
    const basePriceFormat = window.checkoutConfig.basePriceFormat;
    /* \Praxigento\Pv\Plugin\Framework\Webapi\ServiceOutputProcessor::JSON_CAN_SEE_PV */
    const canSeePv = window.checkoutConfig.praxigentoCustomerCanSeePv;
    /* clone base format and modify it */
    var pvFormat = JSON.parse(JSON.stringify(basePriceFormat));
    pvFormat.pattern = "%s";


    var result = Component.extend({
        defaults: {
            segmentName: '',
            template: "Praxigento_Pv/checkout/total/entry"
        },
        /**
         * Extract appropriate PV amount from totals segment.
         * @returns {number}
         */
        getAmount: function () {
            var result = 0;
            if (totals && totals.getSegment(this.segmentName)) {
                result = totals.getSegment(this.segmentName).value;
            }
            return Number(result);
        },
        /**
         * Switch visibility for summary node.
         *
         * @returns {boolean}
         */
        isVisible: function () {
            var value = this.getAmount();
            var result = (value > 0) && canSeePv;
            return result;
        },

        /**
         * Return formatted amount for appropriate PV total value (subtotal, discount, grand).
         *
         * @returns {String|*}
         */
        getBaseValue: function () {
            var price = this.getAmount();
            var result = uiPriceUtils.formatPrice(price, pvFormat);
            return result;
        },

    });
    return result;
});