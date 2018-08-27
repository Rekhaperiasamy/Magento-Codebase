/*global define*/
define([
    'jquery',
    'Colissimo_Shipping/js/lib/popup',
    'Colissimo_Shipping/js/lib/maps',
    'Colissimo_Shipping/js/model/shipping/pickup',
    'Colissimo_Shipping/js/view/checkout/address',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/quote',
    'mage/translate'
], function($,
            popup,
            maps,
            pickupModel,
            pickupAddress,
            stepNavigator,
            setShippingInformationAction,
            quote
) {
    'use strict';

    return {
        actions:{
            'load':window.checkoutConfig.colissimoUrl + 'pickup/load'
        },
        pickupId:null,
        networkCode:null,

        /**
         * Run
         */
        run: function() {
            popup.open(920, 595);
            this.pickupAction();
        },

        /**
         * Load pop-up content with Ajax Request
         *
         * @param {string} action
         * @param {Object} data
         */
        loadContent: function(action, data) {
            popup.closeMessage();
            $.ajax({
                url: action,
                type: 'post',
                context: this,
                data: data,
                beforeSend: popup.loader($.mage.__('Loading...')),
                success: function (response) {
                    popup.update(response);
                }
            });
        },

        /**
         * Launch pickup action
         */
        pickupAction: function() {
            this.loadContent(this.actions.load, {});
        },

        /**
         * Init Pickup action
         *
         * @param {Object.<number, Object>} locations
         */
        pickupInit: function(locations) {
            var pickup = this;

            /* Form Pickup */
            $('#sc-pickup').submit(function(event) {
                var checked = $(this).find("input[name=pickup]:checked");

                if (checked.length) {
                    popup.loader($.mage.__('Loading...'));
                    var pickupData = checked.val().split('-');

                    pickup.pickupUpdateQuote(pickupData[0], pickupData[1]);
                } else {
                    popup.error($.mage.__('Please select pickup'));
                }
                event.preventDefault();
            });

            /* Form Address */
            $('#sc-address').submit(function(event) {
                pickup.loadContent(pickup.actions.load, $(this).serializeArray());
                event.preventDefault();
            });

            /* Back button */
            $('#sc-previous').click(function(event) {
                popup.close();
                event.preventDefault();
            });

            /* Select pickup */
            $('#sc-list').find('input').click(function() {
                $('#sc-list').find('li').removeClass('active');
                $(this).parent('li').addClass('active');
                maps.update($(this).attr('id'));
            });

            /* Show info */
            $('.colissimo-show-info').click(function(event) {
                popup.message($(this).parent('label').next('div').html(), false);
                $(popup.PopupMessage).find('button').click(function() {
                    popup.closeMessageWithEffect();
                });
                event.preventDefault();
            });

            /* Google Maps */
            maps.run('sc-map', 'sc-list');
            maps.locations(locations);
            var address = '';
            $('#sc-address').find('input').each(function() {
                address += $(this).val() + ' ';
            });
            if (address) {
                maps.address(address);
            }
        },

        pickupUpdateQuote: function(pickupId, networkCode) {
            var pickup = this;

            if (typeof pickupId == 'undefined') {
                pickupId = null;
            }

            if (typeof networkCode == 'undefined') {
                networkCode = null;
            }

            if (pickupId && networkCode) {
                var address = pickupModel.getPickup(pickupId, networkCode);
                address.done(
                    function (data) {
                        pickup.pickupUpdateAddress(
                            '<strong>' + data.nom + '</strong><br />' + data.adresse1 + '<br />' + data.code_postal + ' ' + data.localite
                        );

                        pickupModel.savePickup(quote.getQuoteId(), pickupId, networkCode);
                        pickupAddress.pickupAddress(data);
                        popup.close();
                        if (window.checkoutConfig.colissimoOpen === '0' && stepNavigator.getActiveItemIndex() === 0) {
                            stepNavigator.next();
                        }
                    }
                ).fail(
                    function () {
                        pickupAddress.pickupAddress('');
                        popup.error($.mage.__('Unable to load pickup, please select another shipping method'));
                    }
                );
            }
        },

        pickupUpdateAddress: function(content) {
            var label = $('#label_method_pickup_colissimo');

            if(label.length) {
                if (!$('#colissimo_pickup_address').length) {
                    label.parent('tr').after(
                        '<tr id="colissimo_pickup_address"><td id="colissimo_pickup_address_content" colspan="4"></td></tr>'
                    );
                }

                $('#colissimo_pickup_address_content').html(content);
            }
        },

        pickupRemoveAddress: function() {
            if ($('#colissimo_pickup_address').length) {
                $('#colissimo_pickup_address').remove();
            }
            pickupAddress.pickupAddress('');
            pickupModel.resetPickup(quote.getQuoteId());
        }
    }

});