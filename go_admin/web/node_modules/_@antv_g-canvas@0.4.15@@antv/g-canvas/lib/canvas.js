"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var tslib_1 = require("tslib");
var g_base_1 = require("@antv/g-base");
var hit_1 = require("./util/hit");
var Shape = require("./shape");
var group_1 = require("./group");
var draw_1 = require("./util/draw");
var util_1 = require("./util/util");
var Canvas = /** @class */ (function (_super) {
    tslib_1.__extends(Canvas, _super);
    function Canvas() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    Canvas.prototype.getDefaultCfg = function () {
        var cfg = _super.prototype.getDefaultCfg.call(this);
        // 设置渲染引擎为 canvas，只读属性
        cfg['renderer'] = 'canvas';
        // 是否自动绘制，不需要用户调用 draw 方法
        cfg['autoDraw'] = true;
        // 是否允许局部刷新图表
        cfg['localRefresh'] = true;
        cfg['refreshElements'] = [];
        // 是否在视图内自动裁剪
        cfg['clipView'] = true;
        cfg['quickHit'] = false;
        return cfg;
    };
    /**
     * 一些方法调用会引起画布变化
     * @param {ChangeType} changeType 改变的类型
     */
    Canvas.prototype.onCanvasChange = function (changeType) {
        /**
         * 触发画布更新的三种 changeType
         * 1. attr: 修改画布的绘图属性
         * 2. sort: 画布排序，图形的层次会发生变化
         * 3. changeSize: 改变画布大小
         */
        if (changeType === 'attr' || changeType === 'sort' || changeType === 'changeSize') {
            this.set('refreshElements', [this]);
            this.draw();
        }
    };
    Canvas.prototype.getShapeBase = function () {
        return Shape;
    };
    Canvas.prototype.getGroupBase = function () {
        return group_1.default;
    };
    /**
     * 获取屏幕像素比
     */
    Canvas.prototype.getPixelRatio = function () {
        var pixelRatio = this.get('pixelRatio') || util_1.getPixelRatio();
        // 不足 1 的取 1，超出 1 的取整
        return pixelRatio >= 1 ? Math.ceil(pixelRatio) : 1;
    };
    Canvas.prototype.getViewRange = function () {
        return {
            minX: 0,
            minY: 0,
            maxX: this.get('width'),
            maxY: this.get('height'),
        };
    };
    // 复写基类的方法生成标签
    Canvas.prototype.createDom = function () {
        var element = document.createElement('canvas');
        var context = element.getContext('2d');
        // 缓存 context 对象
        this.set('context', context);
        return element;
    };
    Canvas.prototype.setDOMSize = function (width, height) {
        _super.prototype.setDOMSize.call(this, width, height);
        var context = this.get('context');
        var el = this.get('el');
        var pixelRatio = this.getPixelRatio();
        el.width = pixelRatio * width;
        el.height = pixelRatio * height;
        // 设置 canvas 元素的宽度和高度，会重置缩放，因此 context.scale 需要在每次设置宽、高后调用
        if (pixelRatio > 1) {
            context.scale(pixelRatio, pixelRatio);
        }
    };
    // 复写基类方法
    Canvas.prototype.clear = function () {
        _super.prototype.clear.call(this);
        this._clearFrame(); // 需要清理掉延迟绘制的帧
        var context = this.get('context');
        var element = this.get('el');
        context.clearRect(0, 0, element.width, element.height);
    };
    Canvas.prototype.getShape = function (x, y) {
        if (this.get('quickHit')) {
            return hit_1.getShape(this, x, y);
        }
        return _super.prototype.getShape.call(this, x, y, null);
    };
    // 对绘制区域边缘取整，避免浮点数问题
    Canvas.prototype._getRefreshRegion = function () {
        var elements = this.get('refreshElements');
        var viewRegion = this.getViewRange();
        var region;
        // 如果是当前画布整体发生了变化，则直接重绘整个画布
        if (elements.length && elements[0] === this) {
            region = viewRegion;
        }
        else {
            region = draw_1.getMergedRegion(elements);
            if (region) {
                region.minX = Math.floor(region.minX);
                region.minY = Math.floor(region.minY);
                region.maxX = Math.ceil(region.maxX);
                region.maxY = Math.ceil(region.maxY);
                var clipView = this.get('clipView');
                // 自动裁剪不在 view 内的区域
                if (clipView) {
                    region = draw_1.mergeView(region, viewRegion);
                }
            }
        }
        return region;
    };
    /**
     * 刷新图形元素，这里仅仅是放入队列，下次绘制时进行绘制
     * @param {IElement} element 图形元素
     */
    Canvas.prototype.refreshElement = function (element) {
        var refreshElements = this.get('refreshElements');
        refreshElements.push(element);
        // if (this.get('autoDraw')) {
        //   this._startDraw();
        // }
    };
    // 清理还在进行的绘制
    Canvas.prototype._clearFrame = function () {
        var drawFrame = this.get('drawFrame');
        if (drawFrame) {
            // 如果全部渲染时，存在局部渲染，则抛弃掉局部渲染
            util_1.clearAnimationFrame(drawFrame);
            this.set('drawFrame', null);
            this.set('refreshElements', []);
        }
    };
    // 手工调用绘制接口
    Canvas.prototype.draw = function () {
        var drawFrame = this.get('drawFrame');
        if (this.get('autoDraw') && drawFrame) {
            return;
        }
        this._startDraw();
    };
    // 绘制所有图形
    Canvas.prototype._drawAll = function () {
        var context = this.get('context');
        var element = this.get('el');
        var children = this.getChildren();
        context.clearRect(0, 0, element.width, element.height);
        draw_1.applyAttrsToContext(context, this);
        draw_1.drawChildren(context, children);
        // 对于 https://github.com/antvis/g/issues/422 的场景，全局渲染的模式下也会记录更新的元素队列，因此全局渲染完后也需要置空
        this.set('refreshElements', []);
    };
    // 绘制局部
    Canvas.prototype._drawRegion = function () {
        var context = this.get('context');
        var refreshElements = this.get('refreshElements');
        var children = this.getChildren();
        var region = this._getRefreshRegion();
        // 需要注意可能没有 region 的场景
        // 一般发生在设置了 localRefresh ,在没有图形发生变化的情况下，用户调用了 draw
        if (region) {
            // 清理指定区域
            context.clearRect(region.minX, region.minY, region.maxX - region.minX, region.maxY - region.minY);
            // 保存上下文，设置 clip
            context.save();
            context.beginPath();
            context.rect(region.minX, region.minY, region.maxX - region.minX, region.maxY - region.minY);
            context.clip();
            draw_1.applyAttrsToContext(context, this);
            // 绘制子元素
            draw_1.drawChildren(context, children, region);
            context.restore();
        }
        util_1.each(refreshElements, function (element) {
            if (element.get('hasChanged')) {
                // 在视窗外的 Group 元素会加入到更新队列里，但实际却没有执行 draw() 逻辑，也就没有清除 hasChanged 标记
                // 即已经重绘完、但 hasChanged 标记没有清除的元素，需要统一清除掉。主要是 Group 存在问题，具体原因待排查
                element.set('hasChanged', false);
            }
        });
        this.set('refreshElements', []);
    };
    // 触发绘制
    Canvas.prototype._startDraw = function () {
        var _this = this;
        var drawFrame = this.get('drawFrame');
        if (!drawFrame) {
            drawFrame = util_1.requestAnimationFrame(function () {
                if (_this.get('localRefresh')) {
                    _this._drawRegion();
                }
                else {
                    _this._drawAll();
                }
                _this.set('drawFrame', null);
            });
            this.set('drawFrame', drawFrame);
        }
    };
    Canvas.prototype.skipDraw = function () { };
    return Canvas;
}(g_base_1.AbstractCanvas));
exports.default = Canvas;
//# sourceMappingURL=canvas.js.map