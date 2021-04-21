import { filter, isNil, isNumber } from '@antv/util';
import extended from '../util/extended';
/**
 * 计算分类 ticks
 * @param cfg 度量的配置项
 * @returns 计算后的 ticks
 */
export default function calculateCatTicks(cfg) {
    var values = cfg.values, tickInterval = cfg.tickInterval, tickCount = cfg.tickCount;
    var ticks = values;
    if (isNumber(tickInterval)) {
        return filter(ticks, function (__, i) { return i % tickInterval === 0; });
    }
    var min = cfg.min, max = cfg.max;
    if (isNil(min)) {
        min = 0;
    }
    if (isNil(max)) {
        max = values.length - 1;
    }
    if (isNumber(tickCount) && tickCount < max - min) {
        // 简单过滤，部分情况下小数的倍数也可以是整数
        // tslint:disable-next-line: no-shadowed-variable
        var ticks_1 = extended(min, max, tickCount, false, [1, 2, 5, 3, 4, 7, 6, 8, 9]).ticks;
        var valid = filter(ticks_1, function (tick) { return tick >= min && tick <= max; });
        return valid.map(function (index) { return values[index]; });
    }
    return values.slice(min, max + 1);
}
//# sourceMappingURL=cat.js.map