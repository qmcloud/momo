import { isAllowCapture } from '@antv/g-base/lib/util/util';
import { multiplyVec2, invert } from '@antv/g-base/lib/util/matrix';
function invertFromMatrix(v, matrix) {
    if (matrix) {
        var invertMatrix = invert(matrix);
        return multiplyVec2(invertMatrix, v);
    }
    return v;
}
function getRefXY(element, x, y) {
    // @ts-ignore
    var totalMatrix = element.getTotalMatrix();
    if (totalMatrix) {
        var _a = invertFromMatrix([x, y, 1], totalMatrix), refX = _a[0], refY = _a[1];
        return [refX, refY];
    }
    return [x, y];
}
// 拾取前的检测，只有通过检测才能继续拾取
function preTest(element, x, y) {
    // @ts-ignore
    if (element.isCanvas && element.isCanvas()) {
        return true;
    }
    // 不允许被拾取，则返回 null
    // @ts-ignore
    if (!isAllowCapture(element) && element.cfg.isInView === false) {
        return false;
    }
    if (element.cfg.clipShape) {
        // 如果存在 clip
        var _a = getRefXY(element, x, y), refX = _a[0], refY = _a[1];
        if (element.isClipped(refX, refY)) {
            return false;
        }
    }
    // @ts-ignore ，这个地方调用过于频繁
    var bbox = element.cfg.cacheCanvasBBox;
    if (!bbox) {
        bbox = element.getCanvasBBox();
    }
    if (!(x >= bbox.minX && x <= bbox.maxX && y >= bbox.minY && y <= bbox.maxY)) {
        return false;
    }
    return true;
}
export function getShape(container, x, y) {
    // 没有通过检测，则返回 null
    if (!preTest(container, x, y)) {
        return null;
    }
    var shape = null;
    var children = container.getChildren();
    var count = children.length;
    for (var i = count - 1; i >= 0; i--) {
        var child = children[i];
        if (child.isGroup()) {
            shape = getShape(child, x, y);
        }
        else if (preTest(child, x, y)) {
            var curShape = child;
            var _a = getRefXY(child, x, y), refX = _a[0], refY = _a[1];
            // @ts-ignore
            if (curShape.isInShape(refX, refY)) {
                shape = child;
            }
        }
        if (shape) {
            break;
        }
    }
    return shape;
}
//# sourceMappingURL=hit.js.map