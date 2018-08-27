define([
    'Magento_Customer/js/customer-data',
    'underscore'
],function (customerData, _) {
    'use strict';

    function updateDataCampaign(viralloopsDataCampaign)
    {
        if (viralloopsDataCampaign.email) {
            campaign.identify({
                firstname: viralloopsDataCampaign.firstname,
                lastname: viralloopsDataCampaign.lastname,
                email: viralloopsDataCampaign.email,
                timestamp: viralloopsDataCampaign.timestamp,
                signature: viralloopsDataCampaign.signature,
            }, function () {
                //campaign.widgets.load();
            });
        } else {
            campaign.onBoot = function () {
                //campaign.widgets.load();
            };
        }
    }

    return function () {
        var viralloopsDataCampaign = customerData.get('viralloops_campaign');

        if (campaign !== undefined) {
            viralloopsDataCampaign.subscribe(function (viralloopsDataCampaign) {
                updateDataCampaign(viralloopsDataCampaign);
            }, this);
        }
        if (!_.contains(customerData.getExpiredKeys(), "viralloops_campaign")) {
            updateDataCampaign(viralloopsDataCampaign);
        }
    }
});
