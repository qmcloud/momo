import { each, clone } from '@antv/util';
import mat3 from './mat3';
export default (function (m, ts) {
    // 上层使用时会传入为 null 的 matrix，此时按照单位矩阵处理
    var matrix = m ? clone(m) : [1, 0, 0, 0, 1, 0, 0, 0, 1];
    each(ts, function (t) {
        switch (t[0]) {
            case 't':
                mat3.translate(matrix, matrix, [t[1], t[2]]);
                break;
            case 's':
                mat3.scale(matrix, matrix, [t[1], t[2]]);
                break;
            case 'r':
                mat3.rotate(matrix, matrix, t[1]);
                break;
            case 'm':
                mat3.multiply(matrix, matrix, t[1]);
                break;
            default:
                return false;
        }
    });
    return matrix;
});
//# sourceMappingURL=transform.js.map