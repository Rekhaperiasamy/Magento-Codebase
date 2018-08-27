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
	"jquery",
	'Customweb_OgoneCw/js/storage'
], function($, Storage){
	"use strict";
	
	/**
	 * Alias Class
	 * 
	 * @param string formElement
	 * @param object updateData
	 * @param function updateCallback
	 * @param string updateUrl
	 */
	var Alias = function(formElement, updateData, updateCallback, updateUrl) {
		
		/**
		 * @return string
		 */
		this.getValue = function() {
			var aliasElement = $(formElement).find('[data-field-alias="select"]'),
				aliasCreateElement = $(formElement).find('[data-field-alias="create"]'),
				alias = aliasElement.size() ? aliasElement.val() : null,
				aliasCreate = aliasCreateElement.size() ? aliasCreateElement.prop('checked') : false;
			if (alias != null && alias != '') {
				return alias;
			} else if (aliasCreate) {
				return 'new';
			} else {
				return null;
			}
		}
		
		/**
		 * @param object $form
		 * @return void
		 */
		this.updateForm = function($form) {
			$form.find('*[name="alias[create]"]').attr('name', '').attr('data-field-alias', 'create');
			$form.find('*[name="alias[select]"]').attr('name', '').attr('data-field-alias', 'select');
		}
		
		/**
		 * @return void
		 */
		this.attachListeners = function() {
			var self = this;
			
			$(document).on('change', formElement + ' [data-field-alias="select"]', function(){
				updateData['alias'] = self.getValue();
				Storage.post(
					updateUrl, updateData
				).done(
					function(response){
						if ($.type(response) === 'object' && !$.isEmptyObject(response)) {
							if (typeof updateCallback == 'function') {
								updateCallback(response);
							}
						}
					}
				);
			});
		}
	}
	
	return Alias;
});