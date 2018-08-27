//nsd dropdown v 1.2.2
//Nallinda Bandara - netstarter
//jQuery plugin for custom styled selectbox
// Jan 30, 2014
//v 1.2 added links option
//v 1.2.1 added group options
//v 1.2.2 highlight default selected item


jQuery(document).ready(function () {
    var selectbox = jQuery('select');


    jQuery('.ns-selectbox').each(function (index, element) {
        jQuery(this).nsdropdown({});
    });


    // jQuery('select').eq(0).nsdropdown({});


});

(function ($, undefined) {
    var box;

    var methods = {
        init: function (options) {
            // Determine options
            var set = $.extend({
                opacity: 0.5,
                pagebottompadding: 5,
                speed: 300,
                cdiv: "",
                cul: "",
                cwrap: "",
                culdiv: "",
                disabled_class: 'disabled',
                links:false //add true to work as links, add option value as a link

            }, options);

            // Remember them
            this.data("nsdropdown", set);

//            return;

            // Initial stuff
            var cdiv = this;
            var cdivwidth = cdiv.width();
            var cdivheight = cdiv.height();
            var civmleft = cdiv
            if (!cdiv.length) return;

            //add foucs to hiden select box for tab events
            cdiv.focus(function(){

                if(ctit) ctit.trigger("click");

            })

            //check if plugin is already added update it and stop adding it again
            if (cdiv.hasClass('nsd-hide')) {
                cdiv.nsdropdown('update');
                return;
            }

            //add class to native selectbox check plugin is added and hide it
            cdiv.addClass("nsd-hide");


            //wrap it by div
            cdiv.wrap('<div class="nsd-dropdown"></div>');

            var cwrap = cdiv.parent();
            set.cwrap = cwrap;


            //add div for the title
            cwrap.append('<div class="nsd-drop">' + '<div class="dropttitle">' + ((cdiv.find('option:selected').attr("data-html")) ? cdiv.find('option:selected').attr("data-html") : cdiv.find('option:selected').text()) + '</div><div class="dropicon"></div></div></div>');

            var ctit = cwrap.find('.nsd-drop');

            //add div for the list drop down
            cwrap.append('<div class="nsd-ul"><ul></ul></div>');
            var culdiv = cwrap.find('.nsd-ul');
            var cul = culdiv.find('ul');

            //stop sbody scroll on this scroll
            //stop sbody scroll on this scroll
            culdiv.bind( 'mousewheel DOMMouseScroll', function ( e ) {
                var e0 = e.originalEvent,
                    delta = e0.wheelDelta || -e0.detail;

                // console.log(this.scrollTop)
                // console.log(cul.height()-cul.parent().height())


                if(  (this.scrollTop < 10) || (cul.height()-cul.parent().height()-10)<this.scrollTop )
                {

                    this.scrollTop += ( delta < 0 ? 1 : -1 ) *4;
                    e.preventDefault();
                }

            });


            //add the list


            cdiv.nsdropdown('update');


            //click event for the dropdown
            ctit.click(function (e) {
                e.stopPropagation();

                if (box != cdiv && box) box.nsdropdown('closebox');
                box = cdiv;

//                console.log(cwrap.hasClass("nsd-open") + " -" + cdiv.hasClass("novalue"))
                if (cwrap.hasClass("nsd-open") || cdiv.hasClass("novalue") || ctit.hasClass("disabled")) {
                    cdiv.nsdropdown('closebox');

                }
                else {
                    //check for uneven options


                    if (newset())   cdiv.nsdropdown('update');

                    // need to check page has enough space for default max-height and adjust
                    culdiv.css("max-height", "");
                    var defaultMaxHeight = parseInt(culdiv.css("max-height"));
                    var maxheight = defaultMaxHeight;
                    setTimeout(function () {
                        var heightLeft = jQuery(window).height() - (culdiv.offset().top - jQuery(window).scrollTop());
                        if (heightLeft < defaultMaxHeight)
                        {
                            maxheight = heightLeft - set.pagebottompadding;
                            culdiv.css("max-height",  maxheight+ "px");
                        }

                        if(maxheight < culdiv.find('ul').height()) culdiv.addClass("scrolled");
                        else culdiv.removeClass("scrolled");

                    }, 10);

                    culdiv.height('');
                    culdiv.stop().slideDown(set.speed, function () {
                        culdiv.css('overflow', '');
                    });

                    cwrap.addClass('nsd-open');
                    setTimeout(function(){cwrap.addClass('nsd-open-after');},set.speed+100);
                    setTimeout(function () {
                        cdiv.nsdropdown('addBodyClose');
                    }, 100);

                }
            });

            function newset() {
                var newt = false;
                if (cul.find('li').length != cdiv.find('option').length) newt = true;
                return newt;
            }

        },

        callThis: function (opts) {
            // Use 'opts' if relevant
            this.css("display", "none");
        },

        destroy: function (opts) {
            this.removeData("nsdropdown");
            // this.css("display", "").css("opacity", "");
        },


        updatelist: function (set) {


            var cdiv = this;
            var cwrap = cdiv.parent();
            var ctit = cwrap.find('.nsd-drop');
            var culdiv = cwrap.find('.nsd-ul');
            var cul = culdiv.find('ul');


            cwrap.find('.dropttitle').html((cdiv.find('option:selected').attr("data-html")) ? cdiv.find('option:selected').attr("data-html") : cdiv.find('option:selected').text());
            cdiv.change();
            cdiv.nsdropdown('closebox');

        },
        update: function (set) {


            var cdiv = this;
            var cwrap = cdiv.parent();
            var ctit = cwrap.find('.nsd-drop');
            var culdiv = cwrap.find('.nsd-ul');
            var cul = culdiv.find('ul');

            culdiv.height("");
            cul.html("");
            cwrap.find('.dropttitle').html((cdiv.find('option:selected').attr("data-html")) ? cdiv.find('option:selected').attr("data-html") : cdiv.find('option:selected').text());
            cdiv.find('option ,optgroup').each(function (index, element) {
                var disabled = (jQuery(this).is(':disabled')) ? set.disabled_class : '';
                var selected = (jQuery(this).is(':selected')) ? ' nsdroll' : '';
                if(jQuery(this).prop('tagName') ==  'OPTGROUP')
                {
                    cul.append('<h4>' + jQuery(this).attr('label') + '</h4>');
                }
                else if(jQuery(this).attr("data-html"))
                {
                    cul.append('<li class="'+ disabled + selected +'">' + jQuery(this).attr("data-html") + '</li>');
                }

                else if(set.links)   //add links for the select box (value taken as the URL)
                {
                    if(jQuery(this).attr("value"))
                    {
                        cul.append('<li class="'+ disabled + selected +'"><a href="' + jQuery(this).attr("value") + '">' + jQuery(this).text() + '</a></li>');
                    }
                    else
                    {
                        cul.append('<li class="'+ disabled + selected +'">'+jQuery(this).text()+'</li>');
                    }

                }
                else
                {
                    cul.append('<li class="' + disabled + selected +'">' + jQuery(this).text() + '</li>');
                }

            });

            cul.find('li').click(function () {
                if(jQuery(this).hasClass(set.disabled_class)){
                    return false;
                }
                //cdiv.find('option').eq(jQuery(this).index()).prop('selected', true);
                cdiv.find('option').eq(jQuery(this).parent('ul').find('>li').index(jQuery(this))).prop('selected', true);
                cdiv.nsdropdown('updatelist');
                //cdiv.find('option').eq(jQuery(this).index()).trigger('click');
                cdiv.find('option').eq(jQuery(this).parent('ul').find('>li').index(jQuery(this))).trigger('click');

                if (document.createEvent)
                {
                    var evt = document.createEvent('HTMLEvents');
                    evt.initEvent('change', true, true);

                    return cdiv[0].dispatchEvent(evt);
                }

                // Internet Explorer
                if (cdiv[0].fireEvent) {
                    return cdiv[0].fireEvent('onchange');
                }

                cul.find('li').removeClass('nsdroll');
                jQuery(this).addClass('nsdroll');
            });

            cul.find('li').hover(function () {

                // jQuery(this).addClass('nsdroll');

            }, function () {

                // jQuery(this).removeClass('nsdroll');
            });

            cdiv.attr("disabled") == "disabled"?ctit.addClass(set.disabled_class): ctit.removeClass(set.disabled_class);

        },

        closebox: function (set) {

            var cdiv = this;
            var box = this;
            var cwrap = cdiv.parent();
            var culdiv = cwrap.find('.nsd-ul');
            jQuery(document).unbind('click', documentclick);
            jQuery(document).unbind('keydown', nskeypress);
            jQuery(document).unbind('scroll', nsscroll);

            cwrap.removeClass('nsd-open-after');
            culdiv.stop().slideUp(200, function () {
                cwrap.removeClass('nsd-open');


            });
            // removeBodyClose();
        },

        addBodyClose: function (set) {

            var cdiv = this;
            jQuery(document).unbind('click', documentclick);
            jQuery(document).click(documentclick);
            jQuery(document).unbind('keydown', nskeypress);
            jQuery(document).keydown(nskeypress);
            jQuery(document).unbind('scroll', nsscroll);
            jQuery(document).scroll(nsscroll);

            //jQuery(document).click(function (e) {
//                e.stopPropagation();
            //   cdiv.nsdropdown('closebox');
            //  console.log('off')
            // });
        }
    };

    //document click to close menu
    function documentclick(event) {
        box.nsdropdown('closebox');
        box = undefined;
    }

    //document scroll to close menu
    function nsscroll(event) {
        box.nsdropdown('closebox');
        box = undefined;
    }

    function nskeypress(e) {
        var sb = box.parent();
        e.stopPropagation();
        e.preventDefault();
        var cselectedbox = sb.find('.nsdroll');
        //38 - up
        //13 - enter

        if (e.keyCode == 40) // click down arrow
        {
            if (!cselectedbox.length) {
                sb.find('.nsd-ul>ul>li').eq(0).addClass('nsdroll');
                cselectedbox = sb.find('.nsdroll');
            }

            if (cselectedbox.next().is("li")) {
                cselectedbox.removeClass('nsdroll');
                cselectedbox = cselectedbox.next().addClass('nsdroll');
            }
            var scrols = ((cselectedbox.index() + 2) * cselectedbox.outerHeight()) - sb.find('.nsd-ul').height();

            sb.find('.nsd-ul').stop().animate({scrollTop: scrols}, '500');

        }
        else if (e.keyCode == 38) //click up arrow
        {
            if (!cselectedbox.length) return;

            if (cselectedbox.prev().is("li")) {
                cselectedbox.removeClass('nsdroll');
                cselectedbox = cselectedbox.prev().addClass('nsdroll');
            }
            var scrols = ((cselectedbox.index() + 2) * cselectedbox.outerHeight()) - sb.find('.nsd-ul').height();
            sb.find('.nsd-ul').stop().animate({scrollTop: scrols}, '500');
        }
        else if (e.keyCode == 13) //click enter
        {
            if (cselectedbox.length) cselectedbox.trigger("click");
        }
        else if (e.keyCode == 27) //click esc
        {
            box.nsdropdown('closebox');
            box = undefined;
        }

        else if (e.keyCode == 9) //click tab
        {
//            jQuery('.tabItem').each(function(){
//
//                if( parseInt(jQuery(this).attr("tabIndex")) == 7)
//                {
//                    jQuery(this).parent().find(".nsd-drop").trigger("click")
//                }
//
//
//
//            })
        }

        else //selected by charactors
        {


            clearTimeout(timerletter)
            timerletter = setTimeout(function(){
                letterArray ="";
            },500);

            var code = e.charCode || e.keyCode;
            var c = String.fromCharCode(code);
            var car = c.match(/\w/);

            if(e.keyCode >=96 && e.keyCode <= 105 )
            {
                c = numpad[e.keyCode-96];
            }

            letterArray += c;

            if (car) {
                var cletterset = [];
                var cslidetoselect;

                sb.find('.nsd-ul>ul>li').each(function () {
                    //igonor first item and start searching
                    if( jQuery(this).index() != 0)
                    {
                        var arr= jQuery(this).text().toUpperCase().split(letterArray)
                        if (arr.length > 1) {
                            if(arr[0]=="")cletterset.push(jQuery(this));
                        }
                    }

                });

                if(!cletterset.length)
                {
                    if(lastletter == c) letterArray = "";
                    letterArray += c;
                    sb.find('.nsd-ul>ul>li').each(function () {
                        if( jQuery(this).index() != 0)
                        {
                            var arr= jQuery(this).text().toUpperCase().split(letterArray)
                            if (arr.length > 1) {
                                if(arr[0]=="")cletterset.push(jQuery(this));
                            }
                        }
                    });
                }

                if (cletterset.length) {
                    cslidetoselect = cletterset[0];
                    for (var s = 0; s < cletterset.length; s++) {
                        if ((s != cletterset.length - 1) && cletterset[s].hasClass('nsdroll')) {
                            cslidetoselect = cletterset[s].next()
                        }
                    }
                    cselectedbox.removeClass('nsdroll');
                    cselectedbox = cslidetoselect.addClass('nsdroll');
                    var scrols = ((cselectedbox.index() + 2) * cselectedbox.outerHeight()) - sb.find('.nsd-ul').height();
                    sb.find('.nsd-ul').stop().animate({scrollTop: scrols}, '500');
                }

            }
            lastletter = c;
        }

    }

    var timerletter;
    var letterArray = "";
    var lastletter="";
    var numpad = ["0","1","2","3","4","5","6","7","8","9"];


    jQuery.fn.nsdropdown = function (options) {
        var method, args;

        // Method?
        if (typeof options === "string") {
            // Yes, grab the name
            method = options;

            // And arguments (we copy the arguments, then
            // replace the first with our options)
            args = Array.prototype.slice.call(arguments, 0);

            // Get our options from setup call
            args[0] = this.data("nsdropdown");
            if (!args[0]) {
                // There was no setup call, do setup with defaults
                methods.init.call(this);
                args[0] = this.data("nsdropdown");
            }
        }
        else {
            // Not a method call, use init
            method = "init";
            args = [options];
        }

        // Do the call
        methods[method].apply(this, args);
    };
})(jQuery);


var te;