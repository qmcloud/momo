"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
function pretty(min, max, n) {
    if (n === void 0) { n = 5; }
    var res = {
        max: 0,
        min: 0,
        ticks: [],
    };
    if (min === max) {
        return {
            max: max,
            min: min,
            ticks: [min],
        };
    }
    /*
      R pretty:
      https://svn.r-project.org/R/trunk/src/appl/pretty.c
      https://www.rdocumentation.org/packages/base/versions/3.5.2/topics/pretty
      */
    var h = 1.5; // high.u.bias
    var h5 = 0.5 + 1.5 * h; // u5.bias
    // 反正我也不会调参，跳过所有判断步骤
    var d = max - min;
    var c = d / n;
    // 当d非常小的时候触发，但似乎没什么用
    // const min_n = Math.floor(n / 3);
    // const shrink_sml = Math.pow(2, 5);
    // if (Math.log10(d) < -2) {
    //   c = (_.max([ Math.abs(max), Math.abs(min) ]) * shrink_sml) / min_n;
    // }
    var base = Math.pow(10, Math.floor(Math.log10(c)));
    var toFixed = base < 1 ? Math.ceil(Math.abs(Math.log10(base))) : 0;
    var unit = base;
    if (2 * base - c < h * (c - unit)) {
        unit = 2 * base;
        if (5 * base - c < h5 * (c - unit)) {
            unit = 5 * base;
            if (10 * base - c < h * (c - unit)) {
                unit = 10 * base;
            }
        }
    }
    var nu = Math.ceil(max / unit);
    var ns = Math.floor(min / unit);
    res.max = Math.max(nu * unit, max);
    res.min = Math.min(ns * unit, min);
    var x = Number.parseFloat((ns * unit).toFixed(toFixed));
    while (x < max) {
        res.ticks.push(x);
        x += unit;
        if (toFixed) {
            x = Number.parseFloat(x.toFixed(toFixed));
        }
    }
    res.ticks.push(x);
    return res;
}
exports.default = pretty;
//# sourceMappingURL=pretty.js.map