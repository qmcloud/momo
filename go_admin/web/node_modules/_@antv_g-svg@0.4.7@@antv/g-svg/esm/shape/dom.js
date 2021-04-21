/**
 * @fileoverview dom
 * @author dengfuping_develop@163.com
 */
import { __extends } from "tslib";
import { each } from '@antv/util';
import { SVG_ATTR_MAP } from '../constant';
import ShapeBase from './base';
var Dom = /** @class */ (function (_super) {
    __extends(Dom, _super);
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
        each(targetAttrs || attrs, function (value, attr) {
            if (SVG_ATTR_MAP[attr]) {
                el.setAttribute(SVG_ATTR_MAP[attr], value);
            }
        });
        el.innerHTML = attrs['html']; // set innerHTML
    };
    return Dom;
}(ShapeBase));
export default Dom;
//# sourceMappingURL=dom.js.map