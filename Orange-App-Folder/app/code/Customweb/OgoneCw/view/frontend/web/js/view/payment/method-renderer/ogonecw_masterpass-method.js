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
define(
    [
     	'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/template',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Customweb_OgoneCw/js/checkout',
        'Customweb_OgoneCw/js/authorizationMethod',
        'Customweb_OgoneCw/js/storage',
        'Customweb_OgoneCw/js/alias',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/payment/additional-validators',
    ],
    function ($, Component, mageTemplate, url, errorProcessor, Form, AuthorizationMethod, Storage, Alias, placeOrderAction, additionalValidators) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Customweb_OgoneCw/payment/ogonecw_masterpass',
                fieldErrorTemplate: '<div for="<%- id %>" generated="true" class="mage-error" id="<%- id %>-error"><%- message %></div>',
                authorizationUrl: url.build('ogonecw/checkout/authorize/'),
                updateAliasUrl: url.build('ogonecw/checkout/updateAlias/'),
                updateVisibleFieldsUrl: url.build('ogonecw/checkout/updateFields/')
            },
            
            /**
             * @override
             */
            initialize: function () {
            	this._super();
            	
            	Form.fieldErrorTmpl = mageTemplate(this.fieldErrorTemplate);
            	
            	this.authorizationMethod = AuthorizationMethod(this.getAuthorizationMethod(), this.getFormElementSelector(), this.authorizationUrl);
            	
            	this.alias = new Alias(this.getFormElementSelector(), {
            		paymentMethod: this.item.method
            	}, $.proxy(this.onAliasUpdate, this), this.updateAliasUrl);
            	this.alias.attachListeners();
            	
            	this.preload();
            	
            	return this;
            },
            
            /**
             * @override
             */
            validate: function () {
            	this.isPlaceOrderActionAllowed(false);
            	return Form.validate(this.item.method, $.proxy(this.validateSuccess, this), $.proxy(this.validateFailure, this));
            },
            
            
            validateSuccess: function(){
            	var self = this;
            	var placeOrder;
            	if(additionalValidators.validate()) {
                    
                    placeOrder = placeOrderAction(this.getData(), this.redirectAfterPlaceOrder, this.messageContainer);

                    $.when(placeOrder).fail(function () {
                    	self.isPlaceOrderActionAllowed(true);
                    }).done(this.afterPlaceOrder.bind(this));
            	}
            	else{
            		this.isPlaceOrderActionAllowed(true);
            	}
            },
            
            validateFailure: function(){
            	this.isPlaceOrderActionAllowed(true);
            },
            
            /**
             * @override
             */
            redirectAfterPlaceOrder: false,
            
            /**
             * @override
             */
            afterPlaceOrder: function () {
            	this.authorizationMethod.authorize();
            },
            
            /**
             * @override
             */
            getData: function() {
            	var parent = this._super(),
	                additionalData = {};
            	$.each(Form.getValues($(this.getFormElementSelector()), false), function(key, value){
            		additionalData['form[' + key + ']'] = value;
            	});
	            if (this.alias.getValue()) {
	                additionalData['alias'] = this.alias.getValue();
	            }
	            return $.extend(true, parent, {'additional_data': additionalData});
            },
            
            /**
             * @param object response
             * @return void
             */
            onAliasUpdate: function(response) {
            	$(this.getFormElementSelector()).html(this.updateForm(response.html));
            },
    		
    		/**
    		 * @param string formContent
    		 * @return string
    		 */
    		updateForm: function(formContent) {
    			var $form = $('<div>').append(formContent);
    			
            	this.alias.updateForm($form);
    			
    			if (this.authorizationMethod.formDataProtected()) {
    				Form.removeFieldNames($form);
    			}
    			
    			return $form.html();
    		},
            
            /**
             * Preload the payment method after a failure and show the error message.
             * 
             * @return void
             */
            preload: function () {
            	if (this.getFailureMessage()) {
	            	this.selectPaymentMethod();
	            	errorProcessor.process({
	            		status: 500,
	            		responseText: JSON.stringify({
	            			message: this.getFailureMessage()
	            		})
	            	}, this.messageContainer);
            	}
            },
            
	        /**
	         * Retrieve true if the method image should be displayed.
	         * 
	         * @return boolean
	         */
	        isShowImage: function () {
	            return window.checkoutConfig.payment.show_image[this.item.method];
	        },
	        
	        /**
	         * Retrieve the image file url.
	         * 
	         * @return string
	         */
	        getImageUrl: function () {
	            return window.checkoutConfig.payment.image_url[this.item.method];
	        },
	        
	        /**
	         * Retrieve the description text.
	         * 
	         * @return string
	         */
	        getDescription: function () {
	            return window.checkoutConfig.payment.description[this.item.method];
	        },
	        
	        /**
	         * Retrieve the payment form.
	         * 
	         * @return string
	         */
	        getForm: function () {
	        	
	        	Storage.post(
	        			this.updateVisibleFieldsUrl, {
	                		paymentMethod: this.item.method
	                	}, false
					).done($.proxy(this.onFieldUpdate, this));
	        	
	        },
	        
	        /**
             * @param object response
             * @return void
             */
            onFieldUpdate: function(response) {
            	$(this.getFormElementSelector()).html(this.updateForm(response.html));
            },
	        
	        /**
	         * Retrieve the failure message.
	         * 
	         * @return string
	         */
	        getFailureMessage: function () {
	        	return window.checkoutConfig.payment.failureMessage[this.item.method];
	        },
	        
	        /**
	         * Retrieve the authorization method.
	         * 
	         * @return string
	         */
	        getAuthorizationMethod: function () {
	            return window.checkoutConfig.payment.authorizationMethod[this.item.method];
	        },
	        
	        /**
	         * Retrieve the form element's selector.
	         * 
	         * @return string
	         */
	        getFormElementSelector: function () {
	        	return '#payment_form_' + this.item.method;
	        }
        });
    }
);
