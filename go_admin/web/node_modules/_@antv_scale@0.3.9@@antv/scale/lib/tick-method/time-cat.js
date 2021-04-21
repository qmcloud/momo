"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var util_1 = require("@antv/util");
var cat_1 = require("./cat");
/**
 * 计算时间分类的 ticks, 保头，保尾
 * @param cfg 度量的配置项
 * @returns 计算后的 ticks
 */
function calculateTimeCatTicks(cfg) {
    var ticks = cat_1.default(cfg);
    var lastValue = util_1.last(cfg.values);
    if (lastValue !== util_1.last(ticks)) {
        ticks.push(lastValue);
    }
    return ticks;
}
exports.default = calculateTimeCatTicks;
//# sourceMappingURL=time-cat.js.map