"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var util_1 = require("@antv/util");
var mat3_1 = require("./mat3");
exports.default = (function (m, ts) {
    // 上层使用时会传入为 null 的 matrix，此时按照单位矩阵处理
    var matrix = m ? util_1.clone(m) : [1, 0, 0, 0, 1, 0, 0, 0, 1];
    util_1.each(ts, function (t) {
        switch (t[0]) {
            case 't':
                mat3_1.default.translate(matrix, matrix, [t[1], t[2]]);
                break;
            case 's':
                mat3_1.default.scale(matrix, matrix, [t[1], t[2]]);
                break;
            case 'r':
                mat3_1.default.rotate(matrix, matrix, t[1]);
                break;
            case 'm':
                mat3_1.default.multiply(matrix, matrix, t[1]);
                break;
            default:
                return false;
        }
    });
    return matrix;
});
//# sourceMappingURL=transform.js.map