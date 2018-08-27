define([
    'prototype'
], function() {
    var grabemail = {
        timers: null,
        url: null,
        isNeedLogEmail: false,
        run: function(url)
        {
            this.url = url;
            $(document).on('keyup', '[id="customer-email"]', this.sendEmail.bind(this));
        },
        validateEmail: function(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        ajaxCall: function (value){
            new Ajax.Request(this.url, {
                method: 'post',
                loaderArea: false,
                parameters: {
                    form_key: window.checkoutConfig ? window.checkoutConfig.formKey : '',
                    email: value
                },
                onSuccess: function(response) {

                }
          });
        },
        sendEmail: function(e, input){
            var value = $(input).value;
            if (this.validateEmail(value) && this.isNeedLogEmail == true){

                if (this.timers != null){
                    clearTimeout(this.timers);
                }

                this.timers = setTimeout(function(){
                    this.ajaxCall(value)
                }.bind(this), 500);
            }
        }
    };
    return grabemail;
})