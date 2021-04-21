"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var get_style_1 = require("./get-style");
var get_height_1 = require("./get-height");
function getOuterHeight(el, defaultValue) {
    var height = get_height_1.default(el, defaultValue);
    var bTop = parseFloat(get_style_1.default(el, 'borderTopWidth')) || 0;
    var pTop = parseFloat(get_style_1.default(el, 'paddingTop')) || 0;
    var pBottom = parseFloat(get_style_1.default(el, 'paddingBottom')) || 0;
    var bBottom = parseFloat(get_style_1.default(el, 'borderBottomWidth')) || 0;
    var mTop = parseFloat(get_style_1.default(el, 'marginTop')) || 0;
    var mBottom = parseFloat(get_style_1.default(el, 'marginBottom')) || 0;
    return height + bTop + bBottom + pTop + pBottom + mTop + mBottom;
}
exports.default = getOuterHeight;
//# sourceMappingURL=get-outer-height.js.map