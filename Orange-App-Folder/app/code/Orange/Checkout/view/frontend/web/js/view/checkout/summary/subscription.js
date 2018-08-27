/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
        [
            'Magento_Checkout/js/view/summary/abstract-total',
            'Magento_Checkout/js/model/quote',
            'Magento_Catalog/js/price-utils',
            'Magento_Checkout/js/model/totals'
        ],
        function (Component, quote, priceUtils, totals) {
            "use strict";
			var quoteData = window.checkoutConfig.quoteData;
			var sohoDiscountAmount = window.sohoDiscountAmount;
            return Component.extend({
                defaults: {
                    isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                    template: 'Orange_Checkout/checkout/summary/subscription'
                },
                totals: quote.getTotals(),
                isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,
                isDisplayed: function () {
                    return this.isFullMode();
                },
                getValue: function () {
                    var price = 0;
					if (this.totals()) {
						price = quoteData.subscription_total;
					}
                    return this.getFormattedPrice(price);
                },
				getSubscriptionValue1: function () {
                    var price = 0;
					var fullPrice;
					var fullPriceRes;
					if (this.totals()) {
						price = quoteData.subscription_total;
					}
					//fullPrice = price.toLocaleString('en-US', {minimumFractionDigits: 2});
					fullPrice = Number(price).toFixed(2);
					fullPriceRes = fullPrice.split(".");
					return fullPriceRes[0];
                   // return this.getFormattedPrice(price);
                },
				getSubscriptionValue2: function () {
                    var price = 0;
					var fullPrice;
					var fullPriceRes;
					if (this.totals()) {
						price = quoteData.subscription_total;
					}
					//fullPrice = price.toLocaleString('en-US', {minimumFractionDigits: 2});
					fullPrice = Number(price).toFixed(2);
					fullPriceRes = fullPrice.split(".");
					return ","+fullPriceRes[1];
                    //return this.getFormattedPrice(price);
                },
				getCurrencySymbol: function() {
					var checkoutConfig = window.checkoutConfig;
					var priceData = checkoutConfig.priceFormat;
					var currency = priceData.pattern.replace("%s","");
					return currency;
				},
				isDecimal: function () {
					var price = 0;
					if (this.totals()) {
						price = quoteData.subscription_total;
					}
					var fullPrice = Number(price).toFixed(2);
					var fullPriceRes = fullPrice.split(".");
					if(fullPriceRes[1] > 0){
						return true;
					}
					return false;
					//return (price % 1 != 0);
					
				},
                getBaseValue: function () {
                    var price = 0;
                    if (this.totals()) {
                        price = quoteData.subscription_total;
                    }
                    return priceUtils.formatPrice(price, quote.getBasePriceFormat());
                },
				isPrice: function () {
					if(quoteData.subscription_total <= 0){
						return true;
					}
					else{
					return quoteData.subscription_total;
					}
				},
				getSubsidyValue: function () {
					var customergroup = parseInt(quoteData.customer_group_id);
					var subsidy = '';
					if(customergroup === 4)
					{
						var price = 0;
						if (this.totals()) {
							price = (quoteData.subscription_total / (1+(sohoDiscountAmount/100)));
						}
						var formattedPrice = this.getFormattedPrice(price).replace(",00", "").replace(/\./g,"");
						subsidy = '('+formattedPrice + this.getSuffix() + this.getPriceLabel()+')';
					}
					return subsidy;
				},
				getSuffix: function() {
					var store_id = quoteData.store_id;
					var suffix;
					if(store_id === 1)
					{
						suffix = '/maand';
					}
					else
					{
						suffix = '/mois';
					}					
                    return suffix;
                },
                getPriceLabel: function() {
					var customergroup = parseInt(quoteData.customer_group_id);
					var store_id = parseInt(quoteData.store_id);
					var pricelabel;
					if(customergroup === 4)
					{
						if(store_id === 1)
						{
							pricelabel = ' excl. BTW';
						}
						else
						{
							pricelabel = ' HTVA';
						}
					}
                    return pricelabel;
                }
            });
        }
);