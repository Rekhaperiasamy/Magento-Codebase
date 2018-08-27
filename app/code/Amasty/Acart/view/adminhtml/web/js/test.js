define([
    'Magento_Ui/js/modal/alert',
    'prototype'
], function(alert) {
    var test = {
        url: null,
        ruleId: null,
        successMessage: '',
        init: function(url, ruleId, successMessage) {
            this.url = url;
            this.ruleId = ruleId;
            this.successMessage = successMessage;
        },
        send: function(id){
            new Ajax.Request(this.url, {
                method: 'get',
                parameters: {
                    quote_id: id,
                    rule_id: this.ruleId
                },
                onSuccess: function(response) {
                    var message = response.responseJSON.error ?
                        response.responseJSON.errorMsg :
                        this.successMessage;
                    alert({
                        content: message,
                        actions: {
                            confirm: function () {

                            }
                        }
                    })
                }.bind(this)
          });
        }
    }

    return test;
})