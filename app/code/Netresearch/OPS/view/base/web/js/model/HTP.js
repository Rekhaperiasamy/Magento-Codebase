/**
 * @package   OPS
 * @copyright 2017 Netresearch GmbH & Co. KG <http://www.netresearch.de>
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 * @license   OSL 3.0
 */
define(
    [
        'mage/translate',
        'ko',
        'jquery',
        'Netresearch_OPS/js/action/generate-htp-form',
        'Netresearch_OPS/js/action/sprintf'
    ],
    function (
        $t,
        ko,
        $,
        generateForm,
        sprintf
    ) {
        "use strict";
        return function (config) {
            var iframe = '#ops_iframe_' + config.code;
            var redirectNote = '#' + config.code + '_redirect_note';
            var loader = '#' + config.code + '_loader';

            $(iframe).on('load', function () {
                if ($(iframe).attr('src') != 'about:blank') {
                    $(loader).css('display', 'none');
                    $(iframe).css('display', 'block');
                }
            });

            return {
                config: config,

                iframe: iframe,
                loader: loader,
                redirect: redirectNote,
                src: null,

                aliasStr: {
                    success: function () {
                        return sprintf(
                            $t("Your payment data is ready to be processed by Ingenico ePayments. You can {0} reset it {1} or still select another payment method."),
                            '<a href="javascript:void(0)" id="' + config.code + '_reset">',
                            '</a>'
                        );
                    },
                    failure: function () {
                        return sprintf(
                            $t("Your payment data could not be saved by Ingenico ePayments. Please {0} retry {1} or select another payment method."),
                            '<a href="javascript:void(0)" id="' + config.code + '_retry">',
                            '</a>'
                        );
                    },
                },
                loadTokenStr: function () {
                    return $t('Please wait, while we load the Ingenico ePayments payment form.');
                },

                init: function () {
                    var self = this;
                    self.src = ko.observable('about:blank');
                    self.src.subscribe(this.onSrcChange.bind(this));
                    return self;
                },

                onSrcChange: function (value) {
                    $(this.iframe).attr('src', this.src());
                    if (this.src() != 'about:blank') {
                        $(this.loader).css('display', 'none');
                        $(this.iframe).css('display', 'block');
                    }
                },

                prepareForm: function (hash) {
                    return generateForm(this, hash);
                },

                getPaymentMethod: function () {
                    return this.config.paymentMethod;
                },

                getHashUrl: function () {
                    return window.checkoutConfig.payment.opsHTP.hashUrl;
                },

                getUrl: function () {
                    return window.checkoutConfig.payment.opsHTP.url;
                },

                getPspid: function () {
                    return window.checkoutConfig.payment.opsHTP.pspid;
                },

                getOrderId: function () {
                    return window.checkoutConfig.payment.opsHTP.orderId;
                },

                getAliasAcceptUrl: function () {
                    return this.config.aliasAcceptUrl;
                },

                getAliasExceptionUrl: function () {
                    return window.checkoutConfig.payment.opsHTP.aliasExceptionUrl;
                },

                getAliasManager: function () {
                    return this.config.aliasManager;
                },

                getLocale: function () {
                    return window.checkoutConfig.payment.opsHTP.locale;
                },

                getThreeDSecureEnabled: function () {
                    return this.config.isThreeDSecureEnabled;
                },

                getHtpTemplate: function () {
                    return this.config.htpTemplate;
                },

                getCode: function () {
                    return this.config.code;
                },

                generateIframeUrl: function (hash) {
                    var form = this.prepareForm(hash);
                    this.src(this.getUrl() + '?' + $(form).serialize());
                },

                generateHash: function () {
                    var self = this;
                    $.ajax({
                        url: self.getHashUrl() + '?' + $(self.prepareForm(false)).serialize(),
                        type: 'post',
                        dataType: 'json',
                        context: $('body'),
                        cache: true,
                        showLoader: true
                    }).done(function (data) {
                        self.generateIframeUrl(data.hash);
                    });
                },

                getBrand: function () {
                    return this.config.selector();
                },

                fillOpsLoader: function (token) {
                    var loaderElm = $(this.loader);
                    if (typeof token != 'undefined') {
                        loaderElm.html(token);
                        loaderElm.css('display', 'block');
                        $(this.iframe).css('display', 'none');
                    } else {
                        loaderElm.css('display', 'none');
                        $(this.iframe).css('display', 'none');
                    }
                }

            }.init();
        };
    }
);
