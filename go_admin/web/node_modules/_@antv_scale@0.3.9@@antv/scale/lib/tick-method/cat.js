"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var util_1 = require("@antv/util");
var extended_1 = require("../util/extended");
/**
 * 计算分类 ticks
 * @param cfg 度量的配置项
 * @returns 计算后的 ticks
 */
function calculateCatTicks(cfg) {
    var values = cfg.values, tickInterval = cfg.tickInterval, tickCount = cfg.tickCount;
    var ticks = values;
    if (util_1.isNumber(tickInterval)) {
        return util_1.filter(ticks, function (__, i) { return i % tickInterval === 0; });
    }
    var min = cfg.min, max = cfg.max;
    if (util_1.isNil(min)) {
        min = 0;
    }
    if (util_1.isNil(max)) {
        max = values.length - 1;
    }
    if (util_1.isNumber(tickCount) && tickCount < max - min) {
        // 简单过滤，部分情况下小数的倍数也可以是整数
        // tslint:disable-next-line: no-shadowed-variable
        var ticks_1 = extended_1.default(min, max, tickCount, false, [1, 2, 5, 3, 4, 7, 6, 8, 9]).ticks;
        var valid = util_1.filter(ticks_1, function (tick) { return tick >= min && tick <= max; });
        return valid.map(function (index) { return values[index]; });
    }
    return values.slice(min, max + 1);
}
exports.default = calculateCatTicks;
//# sourceMappingURL=cat.js.map