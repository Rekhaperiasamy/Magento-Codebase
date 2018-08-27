/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Customer
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2017 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

require([
    'jquery', // jquery Library
    'jquery/ui', // Jquery UI Library
    'jquery/validate', // Jquery Validation Library
    'mage/translate' // Magento text translate (Validation message translte as per language)
], function($){
    $.validator.addMethod(
        'validate-english-language', function (value) {
            var english = /^[\[\]\nA-Za-z0-9-$&+,:;=!~?@#|_'<>"`+-/.^*()%!' '{}]*$/;
            return english.test(value);

        }, $.mage.__('Please enter in English Only'));

});