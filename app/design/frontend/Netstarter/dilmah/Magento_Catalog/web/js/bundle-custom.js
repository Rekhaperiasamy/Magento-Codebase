/**
 * Copyright © 2015 Netstaretr. created by Nalinda
 * product listing page custom requiermant
 */
define([
    "jquery",
    "jquery/ui",
    "matchMedia"

], function ($) {
    "use strict";

    $.widget('mage.bundleCustom', {
        options: {},
        VAR: {
            device: null, current: null, mixloadcount: null, displaycount: 0, packsize: 1
        },


        _create: function () {

            var _self = this;
            this.VAR.current = $(this.element);

            //setup bundle radio buttons by check box
            this.VAR.current.on('click', '.checkbox-bundle', function (e) {

                e.preventDefault();
                if ($(this).hasClass('active')) {
                    $(this).parent().find('.field.choice').eq(0).find('input').trigger('click');
                    $(this).removeClass('active');
                }
                else {
                    $(this).parent().find('.field.choice').eq(1).find('input').trigger('click');
                    $(this).addClass('active');
                }
                _self.checkCalculation();

            });

            this.VAR.current.find('.checkbox-bundle').each(function () {
                if ($(this).parent().find('.field.choice').eq(1).find('input').is(":checked")) {
                    $(this).addClass('active');
                }
            });

            setTimeout(function () {
                if (jQuery('.product-mix-match').length) {
                    _self.checkCalculation();
                }
            }, 100);


            //text box cahnge
            _self.VAR.current.on('change paste keyup', '.qty-holder input[type="number"]', function (e) {
                _self.checkCalculation();
            })
            //text box cahnge by buttons
            _self.VAR.current.on('click', '.qty-holder .qty-btn', function (e) {
                setTimeout(function () {
                    _self.checkCalculation();
                }, 100);
            });

            jQuery('#product-addtocart-button.mix-match').addClass('need-to-add');

            //trigger submit for bundle products

            jQuery('#product-addtocart-button.trigger-cart, #product-updatecart-button').click(function () {
                if (!$(this).hasClass('need-to-add')) {
                    jQuery('#product_addtocart_form #bundle-qty').val(jQuery('.product-info-main #qty').val());
                    jQuery('#product_addtocart_form').submit();

                   // check radio buttons
                    setTimeout(function(){

                        var checkError = true;
                        if($('.mage-error').length)
                        {

                            $('.mage-error').each(function(){
                                var dis = $(this);
                               if(dis.css('display')=='block' && checkError)
                               {
                                   $('html,body').animate({
                                       scrollTop: dis.offset().top + (-200)
                                   }, 400)


                                   checkError = false;
                               }
                            });

                        }
                    },100);

                }
                else
                {
                    $('html,body').animate({
                        scrollTop: $('.mix-match-heading').offset().top
                    }, 400)
                }


            });

            var $pitems = jQuery('fieldset.fieldset-bundle-options>.field ');
            jQuery('.load-more-mix').click(function () {

                if ($pitems.length > _self.VAR.displaycount) {
                    _self.VAR.displaycount++;
                    $pitems.eq(_self.VAR.displaycount).css('display', 'block');

                    if ($pitems.length > _self.VAR.displaycount) {
                        _self.VAR.displaycount++;

                        $pitems.eq(_self.VAR.displaycount).css('display', 'block');
                        if ($pitems.length > _self.VAR.displaycount) {
                            _self.VAR.displaycount++;

                            $pitems.eq(_self.VAR.displaycount).css('display', 'block');
                        }
                    }
                }

                if ($pitems.length <= _self.VAR.displaycount) {
                    jQuery('.load-more-mix').hide();
                }

            });

            $('.qty-plus').on('plusClicked', function(e){
                if($(this).parent().find('.qty').val()==="0")
                {
                    $(this).parents('.options-list').find('.checkbox-bundle').trigger('click');
                    $(this).parent().find('.qty').val(1);
                }
            });
            $('.qty-minus').on('minusClickedBefore', function(e){
                if($(this).parent().find('.qty').val()==="1" &&  $(this).parents('.options-list').find('.checkbox-bundle').length)
                {
                    $(this).parents('.options-list').find('.checkbox-bundle').trigger('click');
                    $(this).parent().find('.qty').val(0);
                }
            });



            $(window).on('resize', function () {
                _self.resize(_self);
            });
            this.resize(_self);
        },

        checkCalculation: function () {

            var _self = this;

            var bundleSet = _self.VAR.current.find('.qty-holder input[type="number"]');
            var valueAll = 0;

            for (var i = 0; i < bundleSet.length; i++) {

                valueAll += parseInt(bundleSet.eq(i).val());

            }

            _self.VAR.packsize = jQuery('.pack-size .packs span').text();
            var addToLeft = valueAll % _self.VAR.packsize;

            $('.total-items>.count').text(valueAll);
            $('.total-items .add-more>.count').text(_self.VAR.packsize - addToLeft);
            $('.total-packs .count').text(Math.floor(valueAll / _self.VAR.packsize));

            var buttinitem = $('#product-addtocart-button');
            var updatecart = false;
            if ($('#product-updatecart-button').length)
            {
                buttinitem = $('#product-updatecart-button');
                updatecart = true;
            }

            if (addToLeft === 0 && valueAll !== 0) {
                buttinitem.html('Add to Cart');
                if(updatecart)
                {
                    buttinitem.html('Update Cart');
                }

                $('.total-items .add-more').addClass('disable');
                buttinitem.removeClass('need-to-add');

                $('#mobile-product-addtocart-button').removeClass('need-to-add')


            }
            else {
                buttinitem.html('<span>You need to add<span class="counter"> ' + (_self.VAR.packsize - addToLeft) + ' </span>more item</span>');
                buttinitem.addClass('need-to-add');

                $('.total-items .add-more').removeClass('disable');

                $('#mobile-product-addtocart-button').addClass('need-to-add')


            }


            $('#mobile-product-addtocart-button').html(buttinitem.html());




        },


        resize: function (_self) {

            var _self = this;
            //responsive
            mediaCheck({
                media: '(max-width: 767px)',
                //switch to mobile
                entry: function () {
                    if (_self.VAR.device !== "mob") {
                        _self.VAR.device = "mob";
                        _self.mobile();
                    }
                },
                //switch to tablet
                exit: function () {
                    if (_self.VAR.device !== "desk") {
                        _self.VAR.device = "desk";
                        _self.desktop();
                    }
                }
            });


        },

        desktop: function () {
            $('.load-more-mix').hide();
            if ($('.product-mix-match').length) {
                this.VAR.displaycount = 8;
                var $pitems = $('fieldset.fieldset-bundle-options>.field ');
                for (var i = 0; i < $pitems.length; i++) {
                    if (i > 8) {
                        $pitems.eq(i).css('display', 'none');
                    }
                }
                if ($pitems.length > 9) {
                    $('.load-more-mix').show();
                }
            }


        },
        mobile: function () {

            $('.load-more-mix').hide();
            if ($('.product-mix-match').length) {
                this.VAR.displaycount = 2;
                var $pitems = $('fieldset.fieldset-bundle-options>.field ');
                for (var i = 0; i < $pitems.length; i++) {
                    if (i > 2) {
                        $pitems.eq(i).css('display', 'none');
                    }
                }
                if ($pitems.length > 3) {
                    $('.load-more-mix').show();
                }

            }


        }


    });

    return $.mage.bundleCustom;
});