/**
 * Created by shabith on 8/24/16.
 */

define([
    "jquery",
    "matchMedia",
    "jquery/ui",
    "stickupNav"
], function ($, mediaCheck) {
    "use strict";

    $.widget('dilmah.cms', {
        options: {
            collapsible: {

            }
        },

        VAR: {
            $teaClubVids: undefined,
            $stickyWrapper: undefined,
            $stickyLink: undefined,
        },

        _create: function () {
            var _self = this;
            this._initialization(_self);
            this._bindEvents(_self);

            //tea club videos
            //<iframe width="854" height="480" src="https://www.youtube.com/embed/brd0LKrTkwI?list=PLAF9F2CCF8CC2C67F" frameborder="0" allowfullscreen></iframe>

            this.VAR.$teaClubVids.on('click', function (e) {
                e.preventDefault();
                var vid_id = _self.getYoutubeId(jQuery(this).attr('href'));
                var vid_list = _self.getYoutubePlayListId(jQuery(this).attr('href'));
                if(vid_list) {
                    jQuery(this).after('<iframe src="https://www.youtube.com/embed/videoseries?list=' + vid_list + '&amp;autoplay=1" frameborder="0" allowfullscreen></iframe>');
                }else {
                    jQuery(this).after('<iframe src="https://www.youtube.com/embed/' + vid_id + '?autoplay=1" frameborder="0" allowfullscreen></iframe>');
                }


            });

            //sticky nav
            this.VAR.$stickyWrapper.stickUp({
                parts: {
                    0: 'sticky-blog',
                    1:'sticky-reward-points',
                    2: 'sticky-rules-of-tea',
                    3: 'sticky-school-of-tea',
                    4: 'sticky-school-of-tea',
                    5: 'sticky-school-of-tea',
                    6: 'sticky-tea-gastronomy',
                    7: 'sticky-mjf-videos'
                },
                itemClass: 'menuItem',
                itemHover: 'active',
                footer: 'footer.page-footer'
            });
        },

        getYoutubePlayListId: function (url) {
            var regExp = /(?:(?:\?|&)list=)((?!videoseries)[a-zA-Z0-9_]*)/g;
            var match = url.match(regExp);

            if (match && match[0].split('list=')[1].length > 11) {
                return match[0].split('list=')[1];
            } else {
                return '';
            }
        },

        getYoutubeId: function (url) {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
            var match = url.match(regExp);

            if (match && match[2].length == 11) {
                return match[2];
            } else {
                return 'error';
            }
        },

        _initialization: function (_self) {
            this.VAR.$teaClubVids = $('.teaclub-video-popup');
            this.VAR.$stickyWrapper = $('.sticky-nav-wrapper');
            this.VAR.$stickyLink = $('.sticky-nav-wrapper .menuItem a');
        },

        _bindEvents: function (_self) {
            this.VAR.$stickyLink.on('click', function (e) {
                e.preventDefault();
                jQuery('html,body').animate({
                    scrollTop: jQuery(jQuery(this).attr('href')).offset().top - 40
                });
            });
        }
    });

    return $.dilmah.cartSummary;
});