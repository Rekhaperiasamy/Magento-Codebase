var BlackBox = {};
require(["jquery"], function ($) {

    BlackBox = {
        currentAjaxRequest: null,
        previewArea: null, // codemirror
        libraryLoaded: false,
        hideAllArea: function () {
            $('.blackbox .area').hide();
        },
        showLoader: function () {
            $('.blackbox .area.loader').show();
        },
        hideLoader: function () {
            $('.blackbox .area.loader').hide();
        },
        showPreviewXml: function () {
            $('.blackbox .area.preview.xml').show();
        },
        showError: function (msg) {
            $('.blackbox .area.error').show();
            $('.blackbox .error .msg').html(msg);
        },
        showLibrary: function (msg) {
            $('.blackbox .area.library').show();
            if (msg !== "") {
                $('.blackbox .area.library').html(msg);
            }
        },
        showReport: function (msg) {
            $('.blackbox .area.report').show();
            $('.blackbox .area.report').html(msg);
        },
        setActiveButton: function (btn) {
            $('.blackbox .button.active').removeClass('active');
            $('.blackbox .button.' + btn).addClass('active');
        },
        refreshPreview: function () {
            this.setActiveButton('preview');
            this.hideAllArea();
            this.showLoader();
            if (this.currentAjaxRequest !== null) {
                this.currentAjaxRequest.abort();
            }
            
            Filters.updateAdvancedFilters();
            $('#simplegoogleshopping_xmlitempattern').val(CodeMirrorProductPattern.getValue());
            
            this.currentAjaxRequest = $.ajax({
                url: Utils.sampleUrl,
                type: 'POST',
                showLoader: false,
                data: {
                    real_time_preview: true,
                    simplegoogleshopping_id: jQuery('#simplegoogleshopping_id').val(),
                    store_id: jQuery('#store_id').val(),
                    simplegoogleshopping_url: jQuery('#simplegoogleshopping_url').val(),
                    simplegoogleshopping_title: jQuery('#simplegoogleshopping_title').val(),
                    simplegoogleshopping_xmlitempattern: CodeMirrorProductPattern.getValue(),
                    simplegoogleshopping_description: jQuery('#simplegoogleshopping_description').val(),
                    simplegoogleshopping_categories: jQuery('#simplegoogleshopping_categories').val(),
                    simplegoogleshopping_category_filter: jQuery('#simplegoogleshopping_category_filter').val(),
                    simplegoogleshopping_category_type: jQuery('#simplegoogleshopping_category_type').val(),
                    simplegoogleshopping_type_ids: jQuery('#simplegoogleshopping_type_ids').val(),
                    simplegoogleshopping_visibility: jQuery('#simplegoogleshopping_visibility').val(),
                    simplegoogleshopping_attributes: jQuery('#simplegoogleshopping_attributes').val(),
                    simplegoogleshopping_attribute_sets: jQuery('#simplegoogleshopping_attribute_sets').val()
                },
                success: function (data) {
                    if (typeof data.data !== "undefined") {
                        this.hideLoader();
                        this.showPreviewXml();
                        BlackBox.previewArea.setValue(data.data);
                        BlackBox.previewArea.refresh();
                    } else if (typeof data.error !== "undefined") {
                        this.hideLoader();
                        this.showError(data.error);
                    } else {
                        this.hideLoader();
                        this.showError(data);
                    }
                }.bind(this)
            });
        },
        refreshReport: function () {
            this.setActiveButton('report');
            this.hideAllArea();
            this.showLoader();
            if (this.currentAjaxRequest !== null) {
                this.currentAjaxRequest.abort();
            }
            this.currentAjaxRequest = $.ajax({
                url: Utils.sampleReportUrl,
                type: 'POST',
                showLoader: false,
                data: {
                    real_time_preview: true,
                    simplegoogleshopping_id: jQuery('#simplegoogleshopping_id').val(),
                    store_id: jQuery('#store_id').val(),
                    simplegoogleshopping_url: jQuery('#simplegoogleshopping_url').val(),
                    simplegoogleshopping_title: jQuery('#simplegoogleshopping_title').val(),
                    simplegoogleshopping_xmlitempattern: CodeMirrorProductPattern.getValue(),
                    simplegoogleshopping_description: jQuery('#simplegoogleshopping_description').val(),
                    simplegoogleshopping_categories: jQuery('#simplegoogleshopping_categories').val(),
                    category_filter: jQuery('#simplegoogleshopping_category_filter').val(),
                    simplegoogleshopping_type_ids: jQuery('#simplegoogleshopping_type_ids').val(),
                    simplegoogleshopping_visibility: jQuery('#simplegoogleshopping_visibility').val(),
                    simplegoogleshopping_attributes: jQuery('#simplegoogleshopping_attributes').val(),
                    simplegoogleshopping_attribute_sets: jQuery('#simplegoogleshopping_attribute_sets').val()
                },
                success: function (data) {
                    if (typeof data.data !== "undefined") {
                        this.hideLoader();
                        this.showReport(data.data);
                    } else if (typeof data.error !== "undefined") {
                        this.hideLoader();
                        this.showError(data.error);
                    } else {
                        this.hideLoader();
                        this.showError(data);
                    }
                }.bind(this)
            });
        },
        refreshLibrary: function () {
            this.setActiveButton('library');
            this.hideAllArea();
            this.showLoader();
            if (this.currentAjaxRequest !== null) {
                this.currentAjaxRequest.abort();
            }

            if (this.libraryLoaded) {
                this.hideLoader();
                this.showLibrary("");
            }

            this.currentAjaxRequest = $.ajax({
                url: Utils.libraryUrl,
                type: 'GET',
                showLoader: false,
                data: {},
                success: function (data) {
                    if (typeof data.data !== "undefined") {
                        this.hideLoader();
                        this.showLibrary(data.data);
                        this.libraryLoaded = true;
                    }
                }.bind(this)
            });
        },
        loadLibrarySamples: function (elt) {
            var code = elt.attr('att_code');
            var store_id = $('#store_id').val();
            if (elt.find('span').hasClass('opened')) {
                elt.find('span').addClass('closed').removeClass('opened');
                elt.parent().next().find('td').html("");
                elt.parent().next().removeClass('visible');
                return;
            }
            if (this.currentAjaxRequest !== null) {
                this.currentAjaxRequest.abort();
            }

            this.showLoader();
            this.currentAjaxRequest = $.ajax({
                url: Utils.librarySampleUrl,
                data: {
                    code: code,
                    store_id: store_id
                },
                type: 'GET',
                showLoader: false,
                success: function (data) {
                    elt.parent().next().addClass('visible');
                    var html = "<table class='inner-attribute'>";
                    if (data.length > 0) {
                        data.each(function (elt) {
                            html += "<tr><td class='name'><b>" + elt.name + "</b><br/>" + elt.sku + "</td><td class='values'>" + elt.attribute + "<td></tr>";
                        });
                        html += "</table>";
                    } else {
                        html = $.mage.__("No product found.");
                    }
                    elt.find('span').addClass('opened').removeClass('closed');
                    elt.parent().next().find('td').html(html);
                    this.hideLoader();
                }.bind(this)
            });
        },
        setCookie: function (c_name, value, exdays) {
            var exdate = new Date();
            exdate.setDate(exdate.getDate() + exdays);
            var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
            document.cookie = c_name + "=" + c_value + "; path=/;";
        },
        getCookie: function (c_name) {
            var c_value = document.cookie;
            var c_start = c_value.indexOf(" " + c_name + "=");
            if (c_start === -1) {
                c_start = c_value.indexOf(c_name + "=");
            }
            if (c_start === -1) {
                c_value = null;
            } else {
                c_start = c_value.indexOf("=", c_start) + 1;
                var c_end = c_value.indexOf(";", c_start);
                if (c_end === -1) {
                    c_end = c_value.length;
                }
                c_value = unescape(c_value.substring(c_start, c_end));
            }
            return c_value;
        },
        savePosition: function (position) {
            var top = position.top;
            var left = position.left;
            if (top < 0)
                top = 0;
            if (left < 0)
                left = 0;
            if (top > $(window).height() - 20)
                top = $(window).height() - 20;
            if (left > $(window).width() - 20)
                left = $(window).width() - 20;
            this.setCookie("blackbox.top", top);
            this.setCookie("blackbox.left", left);
            this.setPositionAndSize();
        },
        saveSize: function (size) {
            this.setCookie("blackbox.width", size.width);
            this.setCookie("blackbox.height", size.height);
        },
        setPositionAndSize: function () {
            var top = this.getCookie("blackbox.top");
            var left = this.getCookie("blackbox.left");
            var width = this.getCookie("blackbox.width");
            var height = this.getCookie("blackbox.height");
            if (top === null) {
                top = 380;
            }
            if (left === null) {
                left = 1300;
            }
            if (width === null) {
                width = 490;
            }
            if (height === null) {
                height = 380;
            }
            $('.blackbox .resizable').css({
                'width': width + 'px',
                'height': height + 'px'
            });
            $(".blackbox.draggable").css({
                'top': top + 'px',
                'left': left + 'px',
                'display': 'block'});
        }
    };
});
