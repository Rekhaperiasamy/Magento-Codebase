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
], function($, urlBuilder){
	"use strict";
	
	var Storage = {
			
		/**
         * Perform asynchronous GET request to server.
         * 
         * @param string url
         * @return object
         */
        get: function (url, isGlobal) {
            return $.ajax({
                url: urlBuilder.build(url),
                type: 'GET',
                global: isGlobal == undefined ? true : isGlobal
            });
        },
        
        /**
         * Perform asynchronous POST request to server.
         * 
         * @param string url
         * @param object data
         * @return object
         */
        post: function (url, data, isGlobal) {
            return $.ajax({
                url: urlBuilder.build(url),
                type: 'POST',
                data: data,
                global: isGlobal == undefined ? true : isGlobal
            });
        }
        
	};
	
	return Storage;
});