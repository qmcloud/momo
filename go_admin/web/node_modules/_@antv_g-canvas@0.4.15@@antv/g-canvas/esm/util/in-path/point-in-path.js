import { getOffScreenContext } from '@antv/g-base/lib/util/offscreen';
export default function isPointInPath(shape, x, y) {
    var ctx = getOffScreenContext();
    shape.createPath(ctx);
    return ctx.isPointInPath(x, y);
}
//# sourceMappingURL=point-in-path.js.map