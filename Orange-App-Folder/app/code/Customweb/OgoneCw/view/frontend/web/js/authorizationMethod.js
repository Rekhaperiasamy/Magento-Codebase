/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_OgoneCw
 * 
 */
define([
	'jquery',
	'mage/url',
	'Customweb_OgoneCw/js/checkout',
	'Customweb_OgoneCw/js/storage'
], function($, urlBuilder, Form, Storage){
	"use strict";
	
	/**
	 * Abstract Authorization Method Class
	 * 
	 * @param string formElement
	 * @param string authorizationUrl
	 */
	var AuthorizationMethod = function(formElement, authorizationUrl) {
		
		/**
		 * @return void
		 */
		this.redirect = function() {
			Storage.post(
				authorizationUrl, this.getFormValues()
            ).done(
        		function (response) {
        			if ($.type(response) === 'object' && !$.isEmptyObject(response)) {
                		if (response.redirect) {
                			//window.location.replace(response.redirect);
							/* 	var payment_id=$('input.ogone_payment:checked').val();
								if (payment_id=='ogonecw_americanexpress' || payment_id=='ogonecw_visa' || payment_id=='ogonecw_mastercard' ){
								var iframe_id= payment_id+'_ifm';
								var ifrm = document.createElement("iframe");
								ifrm.setAttribute("src", response.redirect);
								ifrm.setAttribute('id','contentIframe');
								ifrm.style.width = "640px";
								ifrm.style.height = "480px";
								$('#'+iframe_id).html(ifrm);
								$(".loading-mask").hide();
								$('.b2c-global-header-bar-middle').css({"height":"0"});
								}else {
									window.location.replace(response.redirect);
									} */
									window.location.replace(response.redirect);
                		} else {
                			var form = new Form(response.formActionUrl, response.hiddenFormFields);
                			form.submit();
                		}
                	}
        		}
            );
		}
		
		/**
		 * @return boolean
		 */
		this.formDataProtected = function() {
			return false;
		}
		
		/**
		 * @return object
		 */
		this.getFormValues = function() {
			return Form.getValues($(formElement), this.formDataProtected());
		}
		
		/**
		 * @return void
		 */
		this.authorize = function() {
			throw 'Not implemented';
		}
	}
	
	
	
	
	/**
	 * Hidden Authorization Method Class
	 * 
	 * @param string formElement
	 * @param string authorizationUrl
	 */
	AuthorizationMethod.HiddenAuthorization = function(formElement, authorizationUrl) {
		AuthorizationMethod.call(this, formElement, authorizationUrl);
		
		/**
         * @override
         */
		this.formDataProtected = function() {
			return true;
		}
		
		/**
         * @override
         */
		this.authorize = function() {
			var self = this;
			
			Storage.get(
				authorizationUrl
            ).done(
        		function (response) {
        			if ($.type(response) === 'object' && !$.isEmptyObject(response)) {
                		var fields = $.extend({}, response.hiddenFormFields, self.getFormValues());
                		var form = new Form(response.formActionUrl, fields);
            			form.submit();
                	}
        		}
            );
		}
	}
	
	
	
	
	
	/**
	 * Payment Page Authorization Method Class
	 * 
	 * @param string formElement
	 * @param string authorizationUrl
	 */
	AuthorizationMethod.PaymentPage = function(formElement, authorizationUrl) {
		AuthorizationMethod.call(this, formElement, authorizationUrl);
		
		/**
         * @override
         */
		this.authorize = function() {
			this.redirect();
		}
	}
	
	
	
	/**
	 * Server Authorization Method Class
	 * 
	 * @param string formElement
	 * @param string authorizationUrl
	 */
	AuthorizationMethod.ServerAuthorization = function(formElement, authorizationUrl) {
		AuthorizationMethod.call(this, formElement, authorizationUrl);
		
		/**
         * @override
         */
		this.authorize = function() {
			var form = new Form(authorizationUrl, this.getFormValues());
			form.submit();
		}
	}
	
	
	
	
	/**
	 * Authorization Method Collection Function
	 * 
	 * @param string authorizationMethod
	 * @param string formElement
	 * @param string authorizationUrl
	 * @return AuthorizationMethod
	 */
	var Collection = function(authorizationMethod, formElement, authorizationUrl){
		if (!AuthorizationMethod[authorizationMethod]) {
			throw "No authorization method named '" + authorizationMethod + "' found.";
		}
		return new AuthorizationMethod[authorizationMethod](formElement, authorizationUrl);
	}
	
	return Collection;
});