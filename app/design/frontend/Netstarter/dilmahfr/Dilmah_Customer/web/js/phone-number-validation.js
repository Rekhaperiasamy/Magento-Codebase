/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Customer
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

    require([
        'jquery', // jquery Library
        'jquery/ui', // Jquery UI Library
        'jquery/validate', // Jquery Validation Library
        'mage/translate' // Magento text translate (Validation message translte as per language)
    ], function($){
        $.validator.addMethod(
            'validate-phone-number', function (value) {
                if(value.match(/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im)) {
                    return true;
                }else {
                    return false;
                }
            }, $.mage.__('Saisissez un numéro de téléphone valide'));

    });