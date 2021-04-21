"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var get_style_1 = require("./get-style");
function getHeight(el, defaultValue) {
    var height = get_style_1.default(el, 'height', defaultValue);
    if (height === 'auto') {
        height = el.offsetHeight;
    }
    return parseFloat(height);
}
exports.default = getHeight;
//# sourceMappingURL=get-height.js.map