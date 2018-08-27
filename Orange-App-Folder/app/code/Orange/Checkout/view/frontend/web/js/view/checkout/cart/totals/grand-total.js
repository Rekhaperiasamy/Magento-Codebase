/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magento_Checkout/js/view/summary/grand-total',
		'Magento_Checkout/js/model/quote'        
    ],
    function (Component, quote) {
        'use strict';
		var quoteData = window.checkoutConfig.quoteData;
		var sohoDiscountAmount = window.sohoDiscountAmount;
        return Component.extend({

            /**
             * @override
             */
            isDisplayed: function () {
                return true;
            },
            getValue: function() {
				var price = this.getPureValue();
                return this.getFormattedPrice(price);
            },
			getGrandValue1: function() {				
				var price = this.getPureValue();
				var fullPrice;
				var fullPriceRes;
				//fullPrice = price.toLocaleString('en-US', {minimumFractionDigits: 2});
				fullPrice = Number(price).toFixed(2);				
				fullPriceRes = fullPrice.split(".");				
				return fullPriceRes[0];
                //return this.getFormattedPrice(price);
            },
			getGrandValue2: function() {				
				var price = this.getPureValue();
				var fullPrice;
				var fullPriceRes;
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
				var price = this.getPureValue();
				var fullPrice = Number(price).toFixed(2);
				var fullPriceRes = fullPrice.split(".");
				if(fullPriceRes[1] > 0){
					return true;
				}
				return false;
				//return (price % 1 != 0);
			},
			getSubsidyValue: function () {
				var customergroup = parseInt(quoteData.customer_group_id);				
				var subsidy = '';
				if(customergroup === 4)
				{
					var price = (this.getPureValue() / (1+(sohoDiscountAmount/100)));
					if(price > 0)
					{
						var formattedPrice = this.getFormattedPrice(price).replace(",00", "").replace(/\./g,"");						
						subsidy = '('+formattedPrice + this.getPriceLabel()+')';
					}
				}
				return subsidy;
			},
			getPriceLabel: function() {
				var customergroup = parseInt(quoteData.customer_group_id);
				var store_id = parseInt(quoteData.store_id);
				var pricelabel = '';
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
			},
			isPayable: function() {				
				var price = this.getPureValue();
				var totals = quote.getTotals();		
				if (totals()) {
					var coupon = totals()['coupon_code'];
					//if(coupon || price >0){
						return true;
					//}
					//return false;
				}
				
			}
        });
    }
);