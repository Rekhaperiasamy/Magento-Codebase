/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Dilmah_Payments/js/action/set-payment-method',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function ($, Component, setPaymentMethodAction, additionalValidators) {
        'use strict';

        return Component.extend({
            /** Redirect to Global Payments */

            continueToGlobalPay: function () {
                //hide terms validation error
                document.getElementById('terms-validation-error').style.display = "none";

                if (additionalValidators.validate() && this.termsValidate()) {
                    //update payment method information if additional data was changed
                    this.selectPaymentMethod();
                    setPaymentMethodAction(this.messageContainer);
                    return false;
                }else if(!this.termsValidate()) {
                    document.getElementById('terms-validation-error').style.display = "block";
                    return false;
                }
            },

            termsOnClick: function() {
              if(this.termsValidate()) {
                  document.getElementById('terms-validation-error').style.display = "none";
              } else{
                  document.getElementById('terms-validation-error').style.display = "block";
              }

                return true;
            },

            termsValidate: function() {
                return document.getElementById('terms-and-conditions').checked
            }
        });
    }
);
