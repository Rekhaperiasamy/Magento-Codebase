define(
    [
       'jquery',
       'Magento_Checkout/js/view/summary/abstract-total',
       'Magento_Checkout/js/model/quote',
       'Magento_Checkout/js/model/totals',
       'Magento_Catalog/js/price-utils'
    ],
    function ($,Component,quote,totals,priceUtils) {
        "use strict";
		var quoteData = window.checkoutConfig.quoteData;
		var sohoDiscountAmount = window.sohoDiscountAmount;
        return Component.extend({
            defaults: {
                template: 'orange_checkout/checkout/summary/subsidy-discount'
            },
            totals: quote.getTotals(),
            isDisplayedSubsidydiscountTotal : function () {
                return true;
            },
            getSubsidydiscountTotal1 : function () {
				var fullPrice;
				var fullPriceRes;
                var price = quoteData.subsidy_discount;
				fullPrice = price.toLocaleString('en-US', {minimumFractionDigits: 2});
				fullPriceRes = fullPrice.split(".");
				return fullPriceRes[0];
                //var formatted = this.getFormattedPrice(price) + this.getPriceLabel();
                //return formatted;
            },
			getSubsidydiscountTotal2 : function () {
				var fullPrice;
				var fullPriceRes;
                var price = quoteData.subsidy_discount;
				//fullPrice = price.toLocaleString('en-US', {minimumFractionDigits: 2});
				fullPrice = Number(price).toFixed(2);
				fullPriceRes = fullPrice.split(".");
				return ","+fullPriceRes[1];
                //var formatted = this.getFormattedPrice(price) + this.getPriceLabel();
                //return formatted;
            },
			getCurrencySymbol: function() {
				var checkoutConfig = window.checkoutConfig;
				var priceData = checkoutConfig.priceFormat;
				var currency = priceData.pattern.replace("%s","");
				return currency;
			},
			isPrice: function () {
				//if(quoteData.subsidy_discount <= 0)
					//return false;
				//else
				return quoteData.subsidy_discount;
			},
			getSubsidyValue: function () {
				var customergroup = parseInt(quoteData.customer_group_id);
				var subsidy = '';
				if(customergroup === 4)
				{
					if(quoteData.subsidy_discount > 0) {
						var price = (quoteData.subsidy_discount / (1+(sohoDiscountAmount/100)));
						var formattedPrice = this.getFormattedPrice(price).replace(",00", "").replace(/\./g,"");
						subsidy = '('+formattedPrice + this.getPriceLabel()+')';
					}
				}
				return subsidy;
			},
			isDecimal: function () {
				var price = quoteData.subsidy_discount;
				return (price % 1 !== 0);
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
			}
         });
    }
);