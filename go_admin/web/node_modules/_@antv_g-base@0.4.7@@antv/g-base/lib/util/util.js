"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
function removeFromArray(arr, obj) {
    var index = arr.indexOf(obj);
    if (index !== -1) {
        arr.splice(index, 1);
    }
}
exports.removeFromArray = removeFromArray;
exports.isBrowser = typeof window !== 'undefined' && typeof window.document !== 'undefined';
var is_nil_1 = require("@antv/util/lib/is-nil");
exports.isNil = is_nil_1.default;
var is_function_1 = require("@antv/util/lib/is-function");
exports.isFunction = is_function_1.default;
var is_string_1 = require("@antv/util/lib/is-string");
exports.isString = is_string_1.default;
var is_object_1 = require("@antv/util/lib/is-object");
exports.isObject = is_object_1.default;
var is_array_1 = require("@antv/util/lib/is-array");
exports.isArray = is_array_1.default;
var mix_1 = require("@antv/util/lib/mix");
exports.mix = mix_1.default;
var each_1 = require("@antv/util/lib/each");
exports.each = each_1.default;
var upper_first_1 = require("@antv/util/lib/upper-first");
exports.upperFirst = upper_first_1.default;
// 是否元素的父容器
function isParent(container, shape) {
    // 所有 shape 都是 canvas 的子元素
    if (container.isCanvas()) {
        return true;
    }
    var parent = shape.getParent();
    var isParent = false;
    while (parent) {
        if (parent === container) {
            isParent = true;
            break;
        }
        parent = parent.getParent();
    }
    return isParent;
}
exports.isParent = isParent;
function isAllowCapture(element) {
    // @ts-ignore
    return element.cfg.visible && element.cfg.capture;
}
exports.isAllowCapture = isAllowCapture;
//# sourceMappingURL=util.js.map