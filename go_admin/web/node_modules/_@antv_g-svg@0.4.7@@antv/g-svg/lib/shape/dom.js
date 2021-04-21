"use strict";
/**
 * @fileoverview dom
 * @author dengfuping_develop@163.com
 */
Object.defineProperty(exports, "__esModule", { value: true });
var tslib_1 = require("tslib");
var util_1 = require("@antv/util");
var constant_1 = require("../constant");
var base_1 = require("./base");
var Dom = /** @class */ (function (_super) {
    tslib_1.__extends(Dom, _super);
    function Dom() {
        var _this = _super !== null && _super.apply(this, arguments) || this;
        _this.type = 'dom';
        _this.canFill = false;
        _this.canStroke = false;
        return _this;
    }
    Dom.prototype.createPath = function (context, targetAttrs) {
        var attrs = this.attr();
        var el = this.get('el');
        util_1.each(targetAttrs || attrs, function (value, attr) {
            if (constant_1.SVG_ATTR_MAP[attr]) {
                el.setAttribute(constant_1.SVG_ATTR_MAP[attr], value);
            }
        });
        el.innerHTML = attrs['html']; // set innerHTML
    };
    return Dom;
}(base_1.default));
exports.default = Dom;
//# sourceMappingURL=dom.js.map