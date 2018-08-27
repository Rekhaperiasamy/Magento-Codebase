/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_RegionShipping
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
/*global define*/
define(
    [],
    function () {
        "use strict";
        return {
            getRules: function() {
                return {
                    'country_id': {
                        'required': true
                    }
                };
            }
        };
    }
);
