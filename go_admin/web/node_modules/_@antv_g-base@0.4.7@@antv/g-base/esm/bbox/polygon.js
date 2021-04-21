import { getBBoxByArray } from '@antv/g-math/lib/util';
export default function (shape) {
    var attrs = shape.attr();
    var points = attrs.points;
    var xArr = [];
    var yArr = [];
    for (var i = 0; i < points.length; i++) {
        var point = points[i];
        xArr.push(point[0]);
        yArr.push(point[1]);
    }
    return getBBoxByArray(xArr, yArr);
}
//# sourceMappingURL=polygon.js.map