/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'jquery/ui',
    'Magento_Ui/js/modal/modal'
], function ($, $t) {
    "use strict";

    $.widget('mage.catalogAddToCart', {

        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: $t('Adding...'),
            addToCartButtonTextAdded: $t('Added'),
            addToCartButtonTextDefault: $t('Add to Cart')

        },

        VAR:
        {
            popups:null,
            addToCartMessageType:null,
            addToCartMessage:null
        }  ,

        _create: function () {
            var self = this;
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }

            //add popup if it is available
            var options = {
                'type': 'popup',
                'responsive': false,
                'innerScroll': false,
                'buttons': []
            };

            this.VAR.popups =   $('<div class="popupdailog"></div>').append( $('#checkout-popup'));
            this.VAR.popups.modal(options);

            jQuery('#checkout-popup .close').click(function(e){
                e.preventDefault()
                self.VAR.popups.modal('closeModal');

            });






            //if ($('#checkout-popup').length)
            //{
            //    $('#checkout-popup').modal(options);
            //}

        },

        _bindSubmit: function () {
            var self = this;
            this.element.on('submit', function (e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        isLoaderEnabled: function () {
            return this.options.processStart && this.options.processStop;
        },

        submitForm: function (form) {
            var self = this;
            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        ajaxSubmit: function (form) {
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function (res) {
                    var dataSeo =[];
                    if (res.status !== undefined) {
                        self.VAR.addToCartMessageType = res.status;
                    }
                    if (res.msg) {
                        self.VAR.addToCartMessage = res.msg;
                    }


                    if($(form).data('seo'))
                    {
                        dataSeo = $(form).data('seo');
                        var productid;

                        if(dataSeo.id)
                        {
                            productid = dataSeo.id;
                        }
                        else if (typeof $(form).find('input[name=product]') !== "undefined")
                        {
                            productid = $(form).find('input[name=product]').val();
                        }
                        dataSeo['price'] = $('#product-price-'+productid + ' span').html();
                    }
                    
                    if(typeof dataLayer !== 'undefined') {
                        dataLayer.push({
                            'event': 'addToCart',
                            'ecommerce': {
                                'currencyCode': dataSeo['currency'],
                                'add': {
                                    'products': dataSeo
                                }
                            }
                        });
                    }
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    self.enableAddToCartButton(form);
                }
            });
        },

        disableAddToCartButton: function (form) {
            var addToCartButton = $(form).find(this.options.addToCartButtonSelector);
            if (addToCartButton.length) {
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                addToCartButton.attr('title', this.options.addToCartButtonTextWhileAdding);
                addToCartButton.find('span').text(this.options.addToCartButtonTextWhileAdding);
            }
            else {
                addToCartButton = jQuery('#product-addtocart-button');
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                addToCartButton.text(this.options.addToCartButtonTextWhileAdding);
            }
            if ($('#mobile-product-addtocart-button').length) {
                $('#mobile-product-addtocart-button').addClass(this.options.addToCartButtonDisabledClass);
                $('#mobile-product-addtocart-button').text(this.options.addToCartButtonTextWhileAdding);
            }

            //custiom fix - loader bug


            setTimeout(function () {
                if ($('.loading-minicart-top').length) {
                    $('.loading-minicart-top').addClass('loading-mask');
                }
                $('.minicart-wrapper .counter.qty>.loading-mask').eq(0).addClass('loading-minicart-top');

            }, 100);


        },

        enableAddToCartButton: function (form) {

            var self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            if (addToCartButton.length) {
                addToCartButton.find('span').text(this.options.addToCartButtonTextAdded);
                addToCartButton.attr('title', this.options.addToCartButtonTextAdded);

                setTimeout(function () {
                    addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                    addToCartButton.find('span').text(self.options.addToCartButtonTextDefault);
                    addToCartButton.attr('title', self.options.addToCartButtonTextDefault);
                }, 1000);
            }
            else {
                addToCartButton = jQuery('#product-addtocart-button');

                addToCartButton.text(this.options.addToCartButtonTextAdded);

                setTimeout(function () {
                    addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                    addToCartButton.text(self.options.addToCartButtonTextDefault);
                    addToCartButton.attr('title', self.options.addToCartButtonTextDefault);
                }, 1000);

            }
            if ($('#mobile-product-addtocart-button').length) {
                $('#mobile-product-addtocart-button').text(this.options.addToCartButtonTextAdded);
                setTimeout(function () {
                    $('#mobile-product-addtocart-button').removeClass(self.options.addToCartButtonDisabledClass);
                    $('#mobile-product-addtocart-button').text(self.options.addToCartButtonTextDefault);
                    $('#mobile-product-addtocart-button').attr('title', self.options.addToCartButtonTextDefault);

                }, 1000);
            }
            //custiom fix - loader bug
            setTimeout(function () {
                if ($('.loading-minicart-top').length) {
                    $('.loading-minicart-top').removeClass('loading-mask');
                }

            }, 3000);


            //reset mix and match
            if ($('.checkbox-bundle').length) {
                $('.checkbox-bundle').each(function () {
                    if ($(this).parent().find('.field.choice').eq(1).find('input').is(":checked")) {
                        $(this).trigger('click');
                    }
                });
                setTimeout(function () {
                    self.resetProduct();

                }, 1050);
            }
            setTimeout(function () {
                $('html,body').animate({
                    scrollTop: 0
                }, 400,function(){
                    if (!self.VAR.addToCartMessageType && self.VAR.addToCartMessageType != 0) {
                        if ($('#checkout-popup').length)
                        {
                            self.VAR.popups.modal('openModal');
                            if ($('.engrave-text').length > 0 && $('.engrave-radio').length > 0) {
                                self.resetEngrave();
                            }
                        }
                    }
                });

            }, 1050);


        },

        resetEngrave: function () {
            $('.product-custom-option').attr('data-text','');
            $('.engrave-text').val('');
            $('.engrave-radio').removeAttr('checked')
            //adding back the qty value before selecting engraving message
            $('#qty').val('1');
            jQuery('#qty').attr('data-text', '1');
            $('#qty').attr('disabled', false);
            $('.qty-main-btn').attr('style', 'display:table-cell');
        },

        resetProduct: function () {
            var bundleSet = jQuery('.qty-holder input[type="number"]');
            var valueAll = 0;

            for (var i = 0; i < bundleSet.length; i++) {

                valueAll += parseInt(bundleSet.eq(i).val());

            }

            var packsize = jQuery('.pack-size .packs span').text();
            var addToLeft = valueAll % packsize;

            jQuery('.total-items>.count').text(valueAll);
            jQuery('.total-items .add-more>.count').text(packsize - addToLeft);
            jQuery('.total-packs .count').text(Math.floor(valueAll / packsize));


            if (addToLeft === 0 && valueAll !== 0) {
                jQuery('#product-addtocart-button').html('Add to Cart');
                jQuery('.total-items .add-more').addClass('disable');
                jQuery('#product-addtocart-button').removeClass('need-to-add');

                jQuery('#mobile-product-addtocart-button').removeClass('need-to-add')


            }
            else {
                jQuery('#product-addtocart-button').html('<span>You need to add<span class="counter"> ' + (packsize - addToLeft) + ' </span>more item</span>');
                jQuery('#product-addtocart-button').addClass('need-to-add');

                jQuery('.total-items .add-more').removeClass('disable');

                jQuery('#mobile-product-addtocart-button').addClass('need-to-add');


            }


            jQuery('#mobile-product-addtocart-button').html(jQuery('#product-addtocart-button').html());
        }

    });

    return $.mage.catalogAddToCart;
});

