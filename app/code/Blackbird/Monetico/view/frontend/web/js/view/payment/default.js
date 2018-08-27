/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Blackbird_Monetico/js/action/process-sealed-form',
		'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function (
        $,
        Component,
		setPaymentMethodAction, 
		additionalValidators,
        processSealedForm
    ) {
        'use strict';

        return Component.extend({

            /**
             * Initialize view.
             *
             * @override
             * @return {exports}
             */
            initialize: function () {
                this._super();
                this.redirectAfterPlaceOrder = false;
                return this;
            },

            /**
             * Call the form with ajax
             *
             * @override
             */
            placeOrder: function () {
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
            },
			
            afterPlaceOrder: function () {
                $.when(processSealedForm(this.getCode(), '#checkout-step-payment'));
            }
			
			
        });
    }
);
