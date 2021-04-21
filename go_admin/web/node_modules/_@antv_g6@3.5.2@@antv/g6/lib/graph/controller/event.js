"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _addEventListener = _interopRequireDefault(require("@antv/dom-util/lib/add-event-listener"));

var _each = _interopRequireDefault(require("@antv/util/lib/each"));

var _isNil = _interopRequireDefault(require("@antv/util/lib/is-nil"));

var _wrapBehavior = _interopRequireDefault(require("@antv/util/lib/wrap-behavior"));

var _base = require("../../util/base");

var _matrixUtil = require("@antv/matrix-util");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var EVENTS = ['click', 'mousedown', 'mouseup', 'dblclick', 'contextmenu', 'mouseenter', 'mouseout', 'mouseover', 'mousemove', 'mouseleave', 'dragstart', 'dragend', 'drag', 'dragenter', 'dragleave', 'dragover', 'dragout', 'drop', 'touchstart', 'touchmove', 'touchend'];

var EventController =
/** @class */
function () {
  function EventController(graph) {
    this.preItem = null;
    this.graph = graph;
    this.extendEvents = [];
    this.dragging = false;
    this.destroyed = false;
    this.initEvents();
  } // 初始化 G6 中的事件


  EventController.prototype.initEvents = function () {
    var _a = this,
        graph = _a.graph,
        extendEvents = _a.extendEvents;

    var canvas = graph.get('canvas'); // canvas.set('draggable', true);

    var el = canvas.get('el');
    var canvasHandler = (0, _wrapBehavior.default)(this, 'onCanvasEvents');
    var originHandler = (0, _wrapBehavior.default)(this, 'onExtendEvents');
    var wheelHandler = (0, _wrapBehavior.default)(this, 'onWheelEvent');
    (0, _each.default)(EVENTS, function (event) {
      canvas.on(event, canvasHandler);
    });
    this.canvasHandler = canvasHandler;
    extendEvents.push((0, _addEventListener.default)(el, 'DOMMouseScroll', wheelHandler));
    extendEvents.push((0, _addEventListener.default)(el, 'mousewheel', wheelHandler));

    if (typeof window !== 'undefined') {
      extendEvents.push((0, _addEventListener.default)(window, 'keydown', originHandler));
      extendEvents.push((0, _addEventListener.default)(window, 'keyup', originHandler));
      extendEvents.push((0, _addEventListener.default)(window, 'focus', originHandler));
    }
  }; // 获取 shape 的 item 对象


  EventController.getItemRoot = function (shape) {
    while (shape && !shape.get('item')) {
      shape = shape.get('parent');
    }

    return shape;
  };
  /**
   * 处理 canvas 事件
   * @param evt 事件句柄
   */


  EventController.prototype.onCanvasEvents = function (evt) {
    var graph = this.graph;
    var canvas = graph.get('canvas');
    var target = evt.target;
    var eventType = evt.type;
    /**
     * (clientX, clientY): 相对于页面的坐标；
     * (canvasX, canvasY): 相对于 <canvas> 左上角的坐标；
     * (x, y): 相对于整个画布的坐标, 与 model 的 x, y 是同一维度的。
     */

    evt.canvasX = evt.x;
    evt.canvasY = evt.y;
    var point = {
      x: evt.canvasX,
      y: evt.canvasY
    };
    var group = graph.get('group');
    var matrix = group.getMatrix();

    if (!matrix) {
      matrix = _matrixUtil.mat3.create();
    }

    if ((0, _base.isViewportChanged)(matrix)) {
      point = graph.getPointByCanvas(evt.canvasX, evt.canvasY);
    }

    evt.x = point.x;
    evt.y = point.y;
    evt.currentTarget = graph;

    if (target === canvas) {
      if (eventType === 'mousemove') {
        this.handleMouseMove(evt, 'canvas');
      }

      evt.target = canvas;
      evt.item = null;
      graph.emit(eventType, evt);
      graph.emit("canvas:" + eventType, evt);
      return;
    }

    var itemShape = EventController.getItemRoot(target);

    if (!itemShape) {
      graph.emit(eventType, evt);
      return;
    }

    var item = itemShape.get('item');

    if (item.destroyed) {
      return;
    }

    var type = item.getType(); // 事件target是触发事件的Shape实例，, item是触发事件的item实例

    evt.target = target;
    evt.item = item;
    graph.emit(eventType, evt);
    graph.emit(type + ":" + eventType, evt);

    if (eventType === 'dragstart') {
      this.dragging = true;
    }

    if (eventType === 'dragend') {
      this.dragging = false;
    }

    if (eventType === 'mousemove') {
      this.handleMouseMove(evt, type);
    }
  };
  /**
   * 处理扩展事件
   * @param evt 事件句柄
   */


  EventController.prototype.onExtendEvents = function (evt) {
    this.graph.emit(evt.type, evt);
  };
  /**
   * 处理滚轮事件
   * @param evt 事件句柄
   */


  EventController.prototype.onWheelEvent = function (evt) {
    if ((0, _isNil.default)(evt.wheelDelta)) {
      evt.wheelDelta = -evt.detail;
    }

    this.graph.emit('wheel', evt);
  };
  /**
   * 处理鼠标移动的事件
   * @param evt 事件句柄
   * @param type item 类型
   */


  EventController.prototype.handleMouseMove = function (evt, type) {
    var _a = this,
        graph = _a.graph,
        preItem = _a.preItem;

    var canvas = graph.get('canvas');
    var item = evt.target === canvas ? null : evt.item;
    evt = (0, _base.cloneEvent)(evt); // 从前一个item直接移动到当前item，触发前一个item的leave事件

    if (preItem && preItem !== item && !preItem.destroyed) {
      evt.item = preItem;
      this.emitCustomEvent(preItem.getType(), 'mouseleave', evt);

      if (this.dragging) {
        this.emitCustomEvent(preItem.getType(), 'dragleave', evt);
      }
    } // 从一个item或canvas移动到当前item，触发当前item的enter事件


    if (item && preItem !== item) {
      evt.item = item;
      this.emitCustomEvent(type, 'mouseenter', evt);

      if (this.dragging) {
        this.emitCustomEvent(type, 'dragenter', evt);
      }
    }

    this.preItem = item;
  };
  /**
   * 在 graph 上面 emit 事件
   * @param itemType item 类型
   * @param eventType 事件类型
   * @param evt 事件句柄
   */


  EventController.prototype.emitCustomEvent = function (itemType, eventType, evt) {
    evt.type = eventType;
    this.graph.emit(itemType + ":" + eventType, evt);
  };

  EventController.prototype.destroy = function () {
    var _a = this,
        graph = _a.graph,
        canvasHandler = _a.canvasHandler,
        extendEvents = _a.extendEvents;

    var canvas = graph.get('canvas');
    (0, _each.default)(EVENTS, function (event) {
      canvas.off(event, canvasHandler);
    });
    (0, _each.default)(extendEvents, function (event) {
      event.remove();
    });
    this.dragging = false;
    this.preItem = null;
    this.extendEvents.length = 0;
    this.canvasHandler = null;
    this.destroyed = true;
  };

  return EventController;
}();

var _default = EventController;
exports.default = _default;