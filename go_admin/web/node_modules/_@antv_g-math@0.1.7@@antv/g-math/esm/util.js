function minNum(array) {
    return Math.min.apply(null, array);
}
function maxNum(array) {
    return Math.max.apply(null, array);
}
/**
 * 两点之间的距离
 * @param {number} x1 起始点 x
 * @param {number} y1 起始点 y
 * @param {number} x2 结束点 x
 * @param {number} y2 结束点 y
 * @return {number} 距离
 */
export function distance(x1, y1, x2, y2) {
    var dx = x1 - x2;
    var dy = y1 - y2;
    return Math.sqrt(dx * dx + dy * dy);
}
export function isNumberEqual(v1, v2) {
    return Math.abs(v1 - v2) < 0.001;
}
export function getBBoxByArray(xArr, yArr) {
    var minX = minNum(xArr);
    var minY = minNum(yArr);
    var maxX = maxNum(xArr);
    var maxY = maxNum(yArr);
    return {
        x: minX,
        y: minY,
        width: maxX - minX,
        height: maxY - minY,
    };
}
export function getBBoxRange(x1, y1, x2, y2) {
    return {
        minX: minNum([x1, x2]),
        maxX: maxNum([x1, x2]),
        minY: minNum([y1, y2]),
        maxY: maxNum([y1, y2]),
    };
}
export function piMod(angle) {
    return (angle + Math.PI * 2) % (Math.PI * 2);
}
//# sourceMappingURL=util.js.map