/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'IWD_Opc/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function ($, Component, quote, riceUtils, totals) {
        "use strict";
		var quoteData = window.checkoutConfig.quoteData;
		var sohoDiscountAmount = window.sohoDiscountAmount;
        return Component.extend({
            defaults: {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'IWD_Opc/grand-total-item'
            },
            totals: quote.getTotals(),
            //isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,
            isDisplayed: function() {
                return this.isFullMode();
            },
            getValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = totals.getSegment('grand_total').value;
                }
                return this.getFormattedPrice(price);
            },
			getGrandValue1: function() {
				var customergroup = quoteData.customer_group_id;
                var price = 0;
				var fullPrice = 0;
				var fullPriceRes = '';
                if (this.totals()) {
					price = totals.getSegment('grand_total').value;
                }
				fullPrice = price.toLocaleString('en-US', {minimumFractionDigits: 2});
				fullPriceRes = fullPrice.split(".");
				return fullPriceRes[0].replace(/,/g,"");
                //return this.getFormattedPrice(price);
            },
			getCurrencySymbol: function() {
                var checkoutConfig = window.checkoutConfig;
				var priceData = checkoutConfig.priceFormat;
				var currency = priceData.pattern.replace("%s","");
				return currency;
            },
			getGrandValue2: function() {
				var customergroup = quoteData.customer_group_id;
                var price = 0;
				var fullPrice = 0;
				var fullPriceRes = '';
				var fullNewpriceRes = '';
                if (this.totals()) {
					price = totals.getSegment('grand_total').value;
                }
				fullPrice = price.toLocaleString('en-US', {minimumFractionDigits: 2});
				fullPriceRes = fullPrice.split(".");
				if(fullPriceRes[1]){
				   fullNewpriceRes = fullPriceRes[1].substring(0, 2);
				}
				else{
				fullNewpriceRes = fullPriceRes[1]
				}
				return ","+fullNewpriceRes;
                //return this.getFormattedPrice(price);
            },
			isDecimal: function () {
				var customergroup = quoteData.customer_group_id; 
				var price = 0;
                if (this.totals()) {
					price = totals.getSegment('grand_total').value;
                }
				return (price % 1 != 0);
			},
            getBaseValue: function() {
                var price = 0;
                if (this.totals()) {
                    price = this.totals().base_grand_total;
                }
                return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            },
            getGrandTotalExclTax: function() {
                var totals = this.totals();
                if (!totals) {
                    return 0;
                }
                return this.getFormattedPrice(totals.grand_total);
            },
			getSubsidyValue: function () {
				var customergroup = quoteData.customer_group_id;				
				var store_id = quoteData.store_id;
				var subsidy = '';
				if(customergroup == 4)
				{
					var price = (totals.getSegment('grand_total').value / (1+(sohoDiscountAmount/100)));
					if(price > 0)
					{
						var formattedPrice = this.getFormattedPrice(price).replace(",00", "").replace(/\./g,"");	
						subsidy = '('+formattedPrice + this.getPriceLabel()+')';
					}
				}
				return subsidy;
			},
			getPriceLabel: function() {
				var customergroup = quoteData.customer_group_id;
				var store_id = quoteData.store_id;
				var pricelabel = '';
				if(customergroup == 4)
				{
					if(store_id == 1)
					{
						var pricelabel = ' excl. BTW';
					}
					else
					{
						var pricelabel = ' HTVA';
					}
				}
				return pricelabel;
			},
            isBaseGrandTotalDisplayNeeded: function() {
                var totals = this.totals();
                if (!totals) {
                    return false;
                }
                return totals.base_currency_code != totals.quote_currency_code;
            },
			isPayable: function() {				
				var price = totals.getSegment('grand_total').value;
				var g_total = this.totals();
				if (g_total) {
					var coupon = g_total['coupon_code'];
					//if(coupon || price >0){
						return true;
					//}
					//return false;
				}
				
			}
        });
    }
);