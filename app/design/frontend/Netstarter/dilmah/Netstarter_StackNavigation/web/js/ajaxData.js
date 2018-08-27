/**
 * Copyright © 2015 Netstarter. All rights reserved.
 *
 * PHP version 5
 *
 * @category  JavaScript
 * @author    Netstarter M2 Stack Team <contact@netstarter.com>
 * @copyright 2007-2015 NETSTARTER PTY LTD (ABN 28 110 067 96)
 * @license   licence.txt ©
 * @link      http://netstarter.com.au
 */
/*jshint strict:true, noempty:true, noarg:true, eqeqeq:true, devel:true, jquery:true, browser:true, bitwise:true, curly:true, undef:true, nonew:true, forin:true */
/*global define */

define([
  "jquery",
  "underscore",
  "Netstarter_StackNavigation/js/prpare",
  "ko"
], function ($) {
  "use strict";

  var popped = ('state' in window.history && window.history.state !== null), initialURL = location.href;

  $.widget('netstarter.ajaxData', {
    $this : null,
    options: {
      link_to_send_ajax           : '.filter-options .items .item a, ' +
                                    '.pages-items .item a, ' +
                                    '#narrow-by-list .swatch-attribute-options a, ' +
                                    'a.filter-clear',                                   // ajax send normal links

      pagination_link_to_send_ajax: '.pages-items .item a',                             // ajax send pagination links
      ajax_load_content           : '#maincontent .columns',                            // ajax loading content
      ajax_loader_area            : 'body',                                             // ajax loader applying element
      multi_select                : true,                                               // Enabling multi select
      multi_select_delimiter      : ', ',                                               // multi select delimiter
      multi_select_sections       : '',                                                 // multi select applying sections
      customEventsToTrigger       : 're-init-priceSlider, filter_options_show_reinit',                                                 // Custom event to trigger after ajax load EG :- re-init-priceSlider
      ajaxSent                    : false,                                              // to stop two ajax requests.
      body                        : '',                                                 // to stop selecting same 'body' element.
      foo                         : ''                                                  // foo
    },
    _create: function () {

      //$this.options.multi_select_sections.split(', ');

      var $this = this; // current widget scope

      $this.options.body = $('body'); // body selector

      $this._prepareDOM();
      $this._prepareMultiSelect();


      // init all related events
      this._events();

    },
    _prepareMultiSelect: function () {

      if(this.options.multi_select){
        var $this     = this, // current widget scope
            multiSec  = $this.options.multi_select_sections.split(', ');

        // add check box
        multiSec.map(function(section) {
          var curentSection = $('[data-section="'+section+'"]');
          curentSection.find('.item a').each(function(){
            $(this).append('<input type="checkbox"/><span></span>').wrapInner('<label>');
          });

        });

        // check filter aplyed selecters
        $('.filter-current .items .item .filter-value').each(function(){
          var text = jQuery(this).text().split($this.options.multi_select_delimiter);
          if (text.length > 0 && text[0] !== ""){
            $.each(text,function(k, v){

              var selectText = v;

              //$( '.filter-options-content li a:contains('+v+') label').map(function(k,v){
              $( '.filter-options-content li a label').map(function(k,v){

                var matchText = $(this).clone().children().remove().end().text().trim();

                if(matchText === selectText){
                  $( this).parent().find( 'input[type=checkbox]' ).prop('checked', true).parent().addClass('selected');
                }

              });

              $('.filter-options-content [option-label="'+v+'"]').addClass('selected');
            });

          }
        });

      }



    },
    _prepareDOM: function () {
      // preparing the DOM for Layerd navigation
      $('.filter-options-item').each(function () {
        //var toTitle = $(this).find('.filter-options-title').text();
        var toTitle = $(this).find('.filter-options-title').attr("data-name");
        $(this).attr('data-section', toTitle);
      });
    },
    _events: function () {
      var $this = this; // current widget scope

      // listen to a tags to send ajax call
      $this.options.body.on('click', this.options.link_to_send_ajax, function (event) {
        event.preventDefault();

        $this.callAjax(this.href);

      });

      $this.options.body.on('click', this.options.pagination_link_to_send_ajax, function (event) {
        event.preventDefault();
        $this.callAjax(this.href);
      });


      // custom event lister to init the Ajax Call
      $this.options.body.on('init-callAjax', function (event, href) {
        $this.callAjax(href);
      });


      $(window).on("navigate", function (event, data) {
        var direction = data.state.direction;
        if ( !! direction) {
          console.log()
          alert(direction);
        }
      });


    },
    callAjax: function (href) {
      var $this       = this; // current widget scope

      //console.log(href);
      if(!$this.options.ajaxSent) {

        // init the loader
        $(this.options.ajax_loader_area).loader();

        // start ajax loader
        $(this.options.ajax_loader_area).trigger('processStart');

        // ajax Sent
        $this.options.ajaxSent = true;


        $.ajax({
          cache: false,
          type: 'POST',
          dataType: "html",
          url: href
        }).done(function( responseText ) {

          //var content = $($.parseHTML(responseText, document, true)).find($this.options.ajax_load_content);
          var content = $(responseText).find($this.options.ajax_load_content);

          /*start DT-1577*/
            var prevRel = $(responseText).filter("[rel='prev']");
            var nextRel = $(responseText).filter("[rel='next']");
            if(prevRel.length){
                if($('[rel="prev"]').length){
                    $('[rel="prev"]').attr('href', prevRel.attr('href'));
                } else {
                    $('head').append('<link rel="prev" href="'+prevRel.attr('href')+'" />');
                }

            } else {
                if($('[rel="prev"]').length){
                    $('[rel="prev"]').remove();
                }
            }
            if(!$('[rel="prev"]').length && prevRel.attr('href')){
              console.log('xoxo')
            }
            if(nextRel.length){
                if($('[rel="next"]').length){
                    $('[rel="next"]').attr('href', nextRel.attr('href'));
                } else {
                    $('head').append('<link rel="next" href="'+nextRel.attr('href')+'" />');
                }
            } else {
                if($('[rel="next"]').length){
                    $('[rel="next"]').remove();
                }
            }
            /*end DT-1577*/

          $( $this.options.ajax_load_content ).html(content.children());


        }).always(function( responseText ) {

          if(responseText.status === "500"){
            console.log('500 - something went wrong we are looking in to that');
          }

          $this._AjaxCallCompleat(href);
          $this._manageBrowserState(href);
          $this.options.ajaxSent = false;

        });

      }

    },
    _AjaxCallCompleat: function (href) {
      var $this = this; // current widget scope


      // custom data-ajax-options
      $('[data-ajax-options]').each(function(){

        /*
         *   find if this has init the plugin
         *   TODO: need to find good approach
         */
        if($(this).find('.swatch-option.text').length > 0){ return; }

        var jsonString = JSON.parse(jQuery(this).data('ajax-options'));
        var widgetObject = JSON.parse(jsonString);

        $(widgetObject.container)[widgetObject.widget](widgetObject.options);

      });

      // re init data-mage-init in side the newly loaded scope
      $('#maincontent [data-mage-init]').each(function(){
        var $_this = jQuery(this),
            widgetObject = jQuery(this).data('mage-init');

        try {

          // magento jQuery widget insistence reInit
          $.each(widgetObject, function(k, v) {
            // set selector
            // set plugin
            // set Options
            $($_this)[k](v);
          });

        } catch(e){
          console.log(e);
        }

      });




      // re prepare new content for layerd navigation
      $this._prepareDOM();

      // re init Multi Select
      $this._prepareMultiSelect();

      // re init Custom Events
      $this._triggerCustomEvents();

      // go to top
      $this._goToTop();


      // end ajax loader
      $(this.options.ajax_loader_area).trigger('processStop');
      $(window).trigger('nsLayeredNavAjaxComplete');

    },
    _goToTop: function(){
      jQuery('body').scrollTop(jQuery('.breadcrumbs').offset()['top']);
    },

    _manageBrowserState: function (href) {
      var $this = this; // current widget scope

      // to manage Browser state
      window.history.pushState(href, href, href);


    },
    _triggerCustomEvents: function () {
      var $this = this, // current widget scope
          cusEvents  = $this.options.customEventsToTrigger.split(', ');

      // triggering custom events
      try {

        cusEvents.map(function(k) {
          jQuery('body').trigger(k);
        });

      } catch (e){ console.log(e); }

    }

  });

  return $.netstarter.ajaxData;
});






