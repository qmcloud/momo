"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var each_1 = require("./each");
var is_array_1 = require("./is-array");
var is_function_1 = require("./is-function");
/**
 * @param {Array} arr The array to iterate over.
 * @param {Function} [fn] The iteratee invoked per element.
 * @return {*} Returns the maximum value.
 * @example
 *
 * var objects = [{ 'n': 1 }, { 'n': 2 }];
 *
 * maxBy(objects, function(o) { return o.n; });
 * // => { 'n': 2 }
 *
 * maxBy(objects, 'n');
 * // => { 'n': 2 }
 */
exports.default = (function (arr, fn) {
    if (!is_array_1.default(arr)) {
        return undefined;
    }
    var max = arr[0];
    var maxData;
    if (is_function_1.default(fn)) {
        maxData = fn(arr[0]);
    }
    else {
        maxData = arr[0][fn];
    }
    var data;
    each_1.default(arr, function (val) {
        if (is_function_1.default(fn)) {
            data = fn(val);
        }
        else {
            data = val[fn];
        }
        if (data > maxData) {
            max = val;
            maxData = data;
        }
    });
    return max;
});
//# sourceMappingURL=max-by.js.map