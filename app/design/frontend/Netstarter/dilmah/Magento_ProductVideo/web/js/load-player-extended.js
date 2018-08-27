/**
 * codepool - load-player.js
 * Created by Shabith on 5/3/2016.
 */

define([
    "jquery",
    "loadPlayer",
    "jquery/ui",
], function($){
    "use strict";

    $.widget('dilmah.videoVimeo', $.mage.videoVimeo, {

        _create: function () {
            var timestamp,
                additionalParams = '',
                src;

            this._initialize();
            timestamp = new Date().getTime();
            this._autoplay = true;

            if (this._autoplay) {
                additionalParams += '&autoplay=1';
            }

            if (this._loop) {
                additionalParams += '&loop=1';
            }
            //added https url
            src = 'https://player.vimeo.com/video/' +
                this._code + '?api=1&player_id=vimeo' +
                this._code +
                timestamp +
                additionalParams;
            this.element.append(
                $('<iframe/>')
                    .attr('frameborder', 0)
                    .attr('id', 'vimeo' + this._code + timestamp)
                    .attr('width', this._width)
                    .attr('height', this._height)
                    .attr('src', src)
                    .attr('webkitallowfullscreen', '')
                    .attr('mozallowfullscreen', '')
                    .attr('allowfullscreen', '')
            );
            this._player = window.$f(this.element.children(':first')[0]);

            // Froogaloop throws error without a registered ready event
            this._player.addEvent('ready', function () {
            });
        },


    });

    $.widget('dilmah.productVideoLoader', {

        /**
         * @private
         */
        _create: function () {
            switch (this.element.data('type')) {
                case 'youtube':
                    this.element.videoYoutube();
                    this._player = this.element.data('mageVideoYoutube');
                    break;

                case 'vimeo':
                    //bridge ui
                    $.widget.bridge('dilmah_videoVimeo', $.dilmah.videoVimeo);
                    this.element.dilmah_videoVimeo();
                    this._player = this.element.data('mageVideoVimeo');
                    break;
                default:
                    throw {
                        name: 'Video Error',
                        message: 'Unknown video type',

                        /**
                         * join name with message
                         */
                        toString: function () {
                            return this.name + ': ' + this.message;
                        }
                    };
            }
        }
    });
});