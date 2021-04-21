"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var offscreen_1 = require("@antv/g-base/lib/util/offscreen");
function isPointInPath(shape, x, y) {
    var ctx = offscreen_1.getOffScreenContext();
    shape.createPath(ctx);
    return ctx.isPointInPath(x, y);
}
exports.default = isPointInPath;
//# sourceMappingURL=point-in-path.js.map