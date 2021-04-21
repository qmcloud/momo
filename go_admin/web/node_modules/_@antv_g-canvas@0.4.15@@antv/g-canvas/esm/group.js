import { __extends } from "tslib";
import { AbstractGroup } from '@antv/g-base';
import * as Shape from './shape';
import { applyAttrsToContext, drawChildren, refreshElement } from './util/draw';
var Group = /** @class */ (function (_super) {
    __extends(Group, _super);
    function Group() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    /**
     * 一些方法调用会引起画布变化
     * @param {ChangeType} changeType 改变的类型
     */
    Group.prototype.onCanvasChange = function (changeType) {
        refreshElement(this, changeType);
    };
    Group.prototype.getShapeBase = function () {
        return Shape;
    };
    Group.prototype.getGroupBase = function () {
        return Group;
    };
    // 同 shape 中的方法重复了
    Group.prototype._applyClip = function (context, clip) {
        if (clip) {
            context.save();
            // 将 clip 的属性挂载到 context 上
            applyAttrsToContext(context, clip);
            // 绘制 clip 路径
            clip.createPath(context);
            context.restore();
            // 裁剪
            context.clip();
            clip._afterDraw();
        }
    };
    Group.prototype.draw = function (context, region) {
        var children = this.getChildren();
        if (children.length) {
            context.save();
            // group 上的矩阵和属性也会应用到上下文上
            // 先将 attrs 应用到上下文中，再设置 clip。因为 clip 应该被当前元素的 matrix 所影响
            applyAttrsToContext(context, this);
            this._applyClip(context, this.getClip());
            drawChildren(context, children, region);
            context.restore();
        }
        // 这里的成本比较大
        this.set('cacheCanvasBBox', this.getCanvasBBox());
        // 绘制后，消除更新标记
        this.set('hasChanged', false);
    };
    // 绘制时被跳过，一般发生在分组隐藏时
    Group.prototype.skipDraw = function () {
        this.set('cacheCanvasBBox', null);
        this.set('hasChanged', false);
    };
    return Group;
}(AbstractGroup));
export default Group;
//# sourceMappingURL=group.js.map