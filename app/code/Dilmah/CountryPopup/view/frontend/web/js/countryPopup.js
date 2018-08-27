/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_CountryPopup
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2017 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
/*jshint browser:true jquery:true*/

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'jquery/ui'
], function($, modal) {
    'use strict';

    $.widget('dilmah.countrySelectPopup', {
        options: {
            modalId: 'countrySelectPopup'
        },

        _create: function () {
            var self = this;

            if (window.location.search === '?cskfi') {
                window.sessionStorage.setItem('selected-country', 'true');
            } else {
                $.get('/country/ajax/check', function (countryData) {
                    console.log(countryData);
                    if (countryData && countryData.hasOwnProperty('current') && countryData.hasOwnProperty('option')) {
                        var selectedCountry = window.sessionStorage.getItem('selected-country');

                        if (!selectedCountry) {
                            var modalButtons = [];
                            if (countryData.current){
                                modalButtons.push({
                                    text: countryData.current['country_name'],
                                    class: countryData.current['country_code'],
                                    click: function() {
                                        window.sessionStorage.setItem('selected-country', 'true');
                                        this.closeModal();
                                    }
                                });
                                $(self.element).find('.popup-current-store').each(function () {
                                    $(this).attr('id', countryData.current['country_code']);
                                    $(this).text(countryData.current['country_name']);
                                });
                            }
                            if (countryData.option){

                                modalButtons.push({
                                    text: countryData.option['country_name'],
                                    class: countryData.option['country_code'],
                                    click: function() {
                                        window.location.replace(countryData.option['url'] + '?cskfi');
                                    }
                                });

                                $(self.element).find('.popup-option-store').each(function () {
                                    $(this).attr('id', countryData.option['country_code']);
                                    $(this).text(countryData.option['country_name']);
                                });
                            }

                            $(self.element).modal({
                                title:'Welcome to Dilmah',
                                autoOpen: true,
                                buttons: modalButtons,
                                clickableOverlay:false,
                                modalClass: 'country-check-modal'
                            });
                        }
                    }
                });
            }
        }
    });

    return $.dilmah.countrySelectPopup;
});