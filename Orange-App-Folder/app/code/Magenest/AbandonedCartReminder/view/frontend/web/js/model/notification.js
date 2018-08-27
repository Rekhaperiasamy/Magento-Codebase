/**
 * Created by Magenest on 10/06/2016.
 * Created by Magenest on 10/06/2016.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'ko',
        'Magenest_AbandonedCartReminder/js/action/refresh'
    ],
    function($, ko, refreshAction) {
        return function (notificationData) {
            return {
                subject: ko.observable(notificationData.subject),
                firstPart: ko.observable(notificationData.firstPart),
                secondPart: ko.observable(notificationData.secondPart),
                time:  ko.observable(notificationData.time),
                refreshUrl: captchaData.refreshUrl,
                isLoading: ko.observable(false),

                getSubject: function () {
                    return this.subject;
                },
                setSubject:function(subject) {
                    this.subject = subject
                },

                getRefreshUrl: function () {
                    return this.refreshUrl;
                },
                setRefreshUrl: function (url) {
                    this.refreshUrl = url;
                },

                refresh: function() {
                    var refresh,
                        self = this;
                    this.isLoading(true);

                    refresh = refreshAction(this.getRefreshUrl(), this.getFormId(), self);
                    $.when(refresh).done(function() {
                        self.isLoading(false);
                    });
                }
            };
        }
    }
);
