/**
 * Created by root on 11/06/2016.
 * katsu
 */
define(
    ['mage/storage'],
    function(storage) {
        "use strict";
        return function(refreshUrl, formId, notiProcess) {
            return storage.post(
                refreshUrl,
                JSON.stringify({'formId': formId}),
                false
            ).done(
                function (response) {
                    if (response) {
                      //  notiProcess(response);
                    }
                }
            );
        };
    }
);
