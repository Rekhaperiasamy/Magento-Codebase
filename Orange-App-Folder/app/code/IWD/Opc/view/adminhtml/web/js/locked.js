require([
    "jquery"
], function($){
    "use strict";
    $(document).ready(function(){
        if ($('#config-edit-form .section-config #iwd_opc_storecredit').length) {
            $('#config-edit-form .section-config #iwd_opc_storecredit').each(function () {
                $(this).append('<span class="iwd_free_opc_locked_button"><i class="fa fa-lock" aria-hidden="true"></i>Unlock Pro</span>');
            });
        }
        if($('#config-edit-form .section-config #carriers_iwdstorepickup')){
            $('#config-edit-form .section-config #carriers_iwdstorepickup')
                .append('<span class="iwd_free_opc_locked_button"><i class="fa fa-lock" aria-hidden="true"></i>Unlock Pro</span>');
        }
        $('body').append(
            '<div id="iwd_free_opc_locked_popup_container">' +
            '<div class="iwd_free_opc_locked_mask"></div>' +
            '<div class="iwd_free_opc_locked_pop_up">' +
            '<div class="iwd_free_opc_locked_pop_up_close"><i class="fa fa-times" aria-hidden="true"></i></div>' +
            '<div class="iwd_free_opc_locked_pop_up_title_img"></div>' +
            '<div class="iwd_free_opc_locked_pop_up_content">' +
            'IWD’s Checkout Suite enhances your checkout experience to make the process quicker, while still offering a suite of robust features to beef up your orders. Upgrade to Pro today for these great features:' +
            '</div>' +
            '<div class="iwd_free_opc_locked_pop_up_features">' +
            '<div class="iwd_free_opc_locked_pop_up_one_feature">In-Store Pickup</div>' +
            '<div class="iwd_free_opc_locked_pop_up_one_feature">Store Credits</div>' +
            '<div class="iwd_free_opc_locked_pop_up_one_feature">Customer Support</div>' +
            '<div class="iwd_free_opc_locked_pop_up_one_feature">Installation & Upgrades</div>' +
            '</div>' +
            '<div class="clear"></div>' +
            '<a title="Unlock Pro" href="https://www.iwdagency.com/extensions/one-step-page-checkout.html?add" target="_blank" class="iwd_free_opc_locked_button_small"><i class="fa fa-lock" aria-hidden="true"></i>Unlock Pro</a>' +
            '</div>' +
            '</div>'
        );
        $(document).on('click', '.iwd_free_opc_locked_button', function () {
            $('#iwd_free_opc_locked_popup_container').css('height', $('html').height()+'px');
            $('#iwd_free_opc_locked_popup_container').show();
            $('html, body').animate({
                scrollTop: $(".iwd_free_opc_locked_pop_up").offset().top -
                ($('.page-actions._fixed').length?($('.page-actions._fixed').height()+5):85)
            }, 500);
        });
        $(document).on('click', '.iwd_free_opc_locked_pop_up_close', function () {
            $('#iwd_free_opc_locked_popup_container').hide();
        });
        $(window).on('keydown', function(e){
            if(e.keyCode == 27){
                $('#iwd_free_opc_locked_popup_container').hide();
            }
        });
    });
});