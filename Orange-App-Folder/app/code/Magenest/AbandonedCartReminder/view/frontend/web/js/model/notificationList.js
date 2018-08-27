/**
 * Created by root on 10/06/2016.
 */
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
        'Magenest_AbandonedCartReminder/js/model/notification',
        'Magenest_AbandonedCartReminder/js/action/refresh'
    ],
    function($, ko,notification, refreshAction) {
        return function (configData) {
            return {
                config: configData,
                notis:ko.observableArray([{subject: "1"},{ subject : '2'}]),

                refreshUrl: configData.refreshUrl,
                isLoading: ko.observable(false),
                duong: ko.observable('Duong'),

                removeNoti: function (noti) {
                    var self = this;
                    self.notis.destroy(noti);
                },
                processNotiData:function(notiData) {
                    //mapping and put the notiData to the observable array
                    var self = this;
                    var notisRaw = ko.utils.parseJson(notiData);
                    var mappedItem = $.map(notisRaw, function(item) { return new notification(item) });

                    mappedItem.push({subject:'Till the End'});
                    mappedItem.push({subject:'Numb'});
                    self.notis(mappedItem);
                },


                refresh: function() {
                    alert('in the notification List Refresh') ;
                    var refresh,
                        self = this;
                    this.isLoading(true);

                    refresh = refreshAction(this.refreshUrl,  self.processNotiData);
                    $.when(refresh).done(function() {
                        self.isLoading(false);
                    });
                }
            };
        }
    }
);
