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
	"jquery"
], function($){
	"use strict";
	
	/**
	 * Form Class
	 * 
	 * @param string url
	 * @param object fields
	 */
	var Form = {}
	
	/**
	 * Form validation registry
	 */
	Form.Validation = new (function() {
		var validators = [];
		
		/**
		 * Register a js function postfix.
		 * 
		 * @param string group
		 * @param function js postfix
		 */
		this.register = function (group, postfix) {
			validators[group] = postfix;
		},
		
		
		/**
		 * Run the registered validators in the given group.
		 * 
		 * @param group
		 * @param function callback
		 */
		this.validate = function (group, successCallback, failureCallback) {
			
			var formId = $('form[name="'+group+'"]').attr('id');
			
			var postfix = validators[group]; 
			if(typeof postfix === 'undefined'){
				successCallback(new Array());
				return;
			}
							
			var validateFunctionName = 'cwValidateFields'+postfix;
			var validateFunction = window[validateFunctionName];
			
			if (typeof validateFunction != 'undefined') {
				validateFunction(successCallback, failureCallback);
				return;
			}
			successCallback(new Array());
		}
	})();
		
	/**
	 * Validate the form fields.
	 * 
	 * @return boolean
	 */
	Form.validate = function(name) {
		
		return Form.Validation.validate(name, 
			$.proxy(function(valid){
				for(var i = 0; i < valid.length; i++) {
					var elementId = valid[i];
					$('#' + elementId).removeClass('mage-error');
					$('#' + elementId + '-error').remove();
					
				}
				var formObject = $('form[name="'+name+'"]')[0];
				formObject.constructor.prototype.submit.call(formObject);	
			}, this),
			
			$.proxy(function(errors, valid){
				for(var i = 0; i < valid.length; i++) {
					var elementId = valid[i];
					$('#' + elementId).removeClass('mage-error');
					$('#' + elementId + '-error').remove();
					
				}
				$.each(errors, $.proxy(function(elementId, error){
					$('#' + elementId + '-error').remove();
					$('#' + elementId).parents('.field').last().append(this.fieldErrorTmpl({
	                    id: elementId,
	                    message: error
	                }));
				}, this));
			}, this));
		
	}
		
	return Form;
});