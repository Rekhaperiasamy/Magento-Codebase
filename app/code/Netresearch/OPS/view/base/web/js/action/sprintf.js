/**
 * @package   OPS
 * @copyright 2017 Netresearch GmbH & Co. KG <http://www.netresearch.de>
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 * @license   OSL 3.0
 */
define([], function () {
        return function (str, col) {
            col = typeof col === 'object' ? col : Array.prototype.slice.call(arguments, 1);

            return str.replace(/\{\{|\}\}|\{(\w+)\}/g, function (m, n) {
                if (m == "{{") {
                    return "{";
                }
                if (m == "}}") {
                    return "}";
                }
                return col[n];
            });
        }
    });