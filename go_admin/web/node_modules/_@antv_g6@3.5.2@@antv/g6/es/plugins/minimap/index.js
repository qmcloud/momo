import { __assign, __extends } from "tslib";
import GCanvas from '@antv/g-canvas/lib/canvas';
import GSVGCanvas from '@antv/g-svg/lib/canvas';
import Base from '../base';
import isString from '@antv/util/lib/is-string';
import createDOM from '@antv/dom-util/lib/create-dom';
import modifyCSS from '@antv/dom-util/lib/modify-css';
import isNil from '@antv/util/lib/is-nil';
import each from '@antv/util/lib/each';
import { transform, mat3 } from '@antv/matrix-util';
import { debounce } from '@antv/util';
var max = Math.max;
var DEFAULT_MODE = 'default';
var KEYSHAPE_MODE = 'keyShape';
var DELEGATE_MODE = 'delegate';
var SVG = 'svg';

var MiniMap =
/** @class */
function (_super) {
  __extends(MiniMap, _super);

  function MiniMap(cfg) {
    var _this = _super.call(this, cfg) || this;
    /**
     * 主图更新的监听函数，使用 debounce 减少渲染频率
     * e.g. 拖拽节点只会在松手后的 100ms 后执行 updateCanvas
     * e.g. render 时大量 addItem 也只会执行一次 updateCanvas
     */


    _this.handleUpdateCanvas = debounce(function (event) {
      var self = _this;
      if (self.destroyed) return;
      self.updateCanvas();
    }, 100, false);
    return _this;
  }

  MiniMap.prototype.getDefaultCfgs = function () {
    return {
      container: null,
      className: 'g6-minimap',
      viewportClassName: 'g6-minimap-viewport',
      // Minimap 中默认展示和主图一样的内容，KeyShape 只展示节点和边的 key shape 部分，delegate表示展示自定义的rect，用户可自定义样式
      type: 'default',
      padding: 50,
      size: [200, 120],
      delegateStyle: {
        fill: '#40a9ff',
        stroke: '#096dd9'
      },
      refresh: true
    };
  };

  MiniMap.prototype.getEvents = function () {
    return {
      beforepaint: 'updateViewport',
      beforeanimate: 'disableRefresh',
      afteranimate: 'enableRefresh',
      viewportchange: 'disableOneRefresh'
    };
  }; // 若是正在进行动画，不刷新缩略图


  MiniMap.prototype.disableRefresh = function () {
    this.set('refresh', false);
  };

  MiniMap.prototype.enableRefresh = function () {
    this.set('refresh', true);
    this.updateCanvas();
  };

  MiniMap.prototype.disableOneRefresh = function () {
    this.set('viewportChange', true);
  };

  MiniMap.prototype.initViewport = function () {
    var _this = this;

    var cfgs = this._cfgs;
    var size = cfgs.size,
        graph = cfgs.graph;
    if (this.destroyed) return;
    var canvas = this.get('canvas');
    var containerDOM = canvas.get('container');
    var viewport = createDOM("<div class=" + cfgs.viewportClassName + " \n      style='position:absolute;\n        left:0;\n        top:0;\n        box-sizing:border-box;\n        border: 2px solid #1980ff'></div>"); // 计算拖拽水平方向距离

    var x = 0; // 计算拖拽垂直方向距离

    var y = 0; // 是否在拖拽minimap的视口

    var dragging = false; // 缓存viewport当前对于画布的x

    var left = 0; // 缓存viewport当前对于画布的y

    var top = 0; // 缓存viewport当前宽度

    var width = 0; // 缓存viewport当前高度

    var height = 0;
    var ratio = 0;
    var zoom = 0;
    containerDOM.addEventListener('mousedown', function (e) {
      cfgs.refresh = false;

      if (e.target !== viewport) {
        return;
      } // 如果视口已经最大了，不需要拖拽


      var style = viewport.style;
      left = parseInt(style.left, 10);
      top = parseInt(style.top, 10);
      width = parseInt(style.width, 10);
      height = parseInt(style.height, 10);

      if (width > size[0] || height > size[1]) {
        return;
      }

      zoom = graph.getZoom();
      ratio = _this.get('ratio');
      dragging = true;
      x = e.clientX;
      y = e.clientY;
    }, false);
    containerDOM.addEventListener('mousemove', function (e) {
      if (!dragging || isNil(e.clientX) || isNil(e.clientY)) {
        return;
      }

      var dx = x - e.clientX;
      var dy = y - e.clientY; // 若视口移动到最左边或最右边了,仅移动到边界

      if (left - dx < 0) {
        dx = left;
      } else if (left - dx + width > size[0]) {
        dx = left + width - size[0];
      } // 若视口移动到最上或最下边了，仅移动到边界


      if (top - dy < 0) {
        dy = top;
      } else if (top - dy + height > size[1]) {
        dy = top + height - size[1];
      }

      left -= dx;
      top -= dy; // 先移动视口，避免移动到边上以后出现视口闪烁

      modifyCSS(viewport, {
        left: left + "px",
        top: top + "px"
      }); // graph 移动需要偏移量 dx/dy * 缩放比例才会得到正确的移动距离

      graph.translate(dx * zoom / ratio, dy * zoom / ratio);
      x = e.clientX;
      y = e.clientY;
    }, false);
    containerDOM.addEventListener('mouseleave', function () {
      dragging = false;
      cfgs.refresh = true;
    }, false);
    containerDOM.addEventListener('mouseup', function () {
      dragging = false;
      cfgs.refresh = true;
    }, false);
    this.set('viewport', viewport);
    containerDOM.appendChild(viewport);
  };
  /**
   * 更新 viewport 视图
   */


  MiniMap.prototype.updateViewport = function () {
    if (this.destroyed) return;
    var ratio = this.get('ratio');
    var dx = this.get('dx');
    var dy = this.get('dy');
    var totaldx = this.get('totaldx');
    var totaldy = this.get('totaldy');
    var graph = this.get('graph');
    var size = this.get('size');
    var graphWidth = graph.get('width');
    var graphHeight = graph.get('height');
    var topLeft = graph.getPointByCanvas(0, 0);
    var bottomRight = graph.getPointByCanvas(graphWidth, graphHeight);
    var graphBBox = graph.get('canvas').getCanvasBBox();
    var viewport = this.get('viewport');

    if (!viewport) {
      this.initViewport();
    }

    var zoom = graph.getZoom(); // viewport宽高,左上角点的计算

    var width = (bottomRight.x - topLeft.x) * ratio;
    var height = (bottomRight.y - topLeft.y) * ratio;
    var left = topLeft.x * ratio + totaldx;
    var top = topLeft.y * ratio + totaldy;

    if (width > size[0]) {
      width = size[0];

      if (graphBBox.maxX > graphWidth) {
        left = -dx - (graphBBox.maxX - graphWidth) / zoom * ratio;
      } else {
        left = dx - graphBBox.minX / zoom * ratio;
      }
    }

    if (height > size[1]) {
      height = size[1];

      if (graphBBox.maxY > graphHeight) {
        top = -dy - (graphBBox.maxY - graphHeight) / zoom * ratio;
      } else {
        top = dy - graphBBox.minY / zoom * ratio;
      }
    } // 缓存目前缩放比，在移动 minimap 视窗时就不用再计算大图的移动量


    this.set('ratio', ratio);
    var correctLeft = left + "px";
    var correctTop = top + "px";
    var graphCanvasBBox = graph.get('canvas').getCanvasBBox();

    if (width >= size[0]) {
      if (graphCanvasBBox.minX > 0 && graphCanvasBBox.maxX < graphWidth) {
        if (left >= 0 && left + width <= size[0]) {
          correctLeft = left + "px";
        } else if (left < 0) {
          correctLeft = 0;
        } else if (left + width > size[0]) {
          correctLeft = size[0] - width + "px";
        }
      }
    }

    if (height >= size[1]) {
      if (graphCanvasBBox.minY > 0 && graphCanvasBBox.maxY < graphHeight) {
        if (top >= 0 && top + height <= size[1]) {
          correctTop = top + "px";
        } else if (top < 0) {
          correctTop = 0;
        } else if (top + height > size[1]) {
          correctTop = size[1] - height + "px";
        }
      }
    }

    modifyCSS(viewport, {
      left: correctLeft,
      top: correctTop,
      width: width + "px",
      height: height + "px"
    });
  };
  /**
   * 将主图上的图形完全复制到小图
   */


  MiniMap.prototype.updateGraphShapes = function () {
    var graph = this._cfgs.graph;
    var canvas = this.get('canvas');
    var graphGroup = graph.get('group');
    if (graphGroup.destroyed) return;
    var clonedGroup = graphGroup.clone();
    clonedGroup.resetMatrix();
    canvas.clear();
    canvas.add(clonedGroup);
  }; // 仅在 minimap 上绘制 keyShape
  // FIXME 如果用户自定义绘制了其他内容，minimap上就无法画出


  MiniMap.prototype.updateKeyShapes = function () {
    var _this = this;

    var graph = this._cfgs.graph;
    each(graph.getEdges(), function (edge) {
      _this.updateOneEdgeKeyShape(edge);
    });
    each(graph.getNodes(), function (node) {
      _this.updateOneNodeKeyShape(node);
    });
    this.clearDestroyedShapes();
  };
  /**
   * 增加/更新单个元素的 keyShape
   * @param item INode 实例
   */


  MiniMap.prototype.updateOneNodeKeyShape = function (item) {
    var canvas = this.get('canvas');
    var group = canvas.get('children')[0] || canvas.addGroup();
    var itemMap = this.get('itemMap') || {}; // 差量更新 minimap 上的一个节点，对应主图的 item

    var mappedItem = itemMap[item.get('id')];
    var bbox = item.getBBox(); // 计算了节点父组矩阵的 bbox

    var cKeyShape = item.get('keyShape').clone();
    var keyShapeStyle = cKeyShape.attr();
    var attrs = {
      x: bbox.centerX,
      y: bbox.centerY
    };

    if (!mappedItem) {
      mappedItem = cKeyShape;
      group.add(mappedItem);
    } else {
      attrs = Object.assign(keyShapeStyle, attrs);
    }

    var shapeType = mappedItem.get('type');

    if (shapeType === 'rect' || shapeType === 'image') {
      attrs.x = bbox.minX;
      attrs.y = bbox.minY;
    }

    mappedItem.attr(attrs);

    if (!item.isVisible()) {
      mappedItem.hide();
    }

    mappedItem.exist = true;
    itemMap[item.get('id')] = mappedItem;
    this.set('itemMap', itemMap);
  };
  /**
   * Minimap 中展示自定义的rect，支持用户自定义样式和节点大小
   */


  MiniMap.prototype.updateDelegateShapes = function () {
    var _this = this;

    var graph = this._cfgs.graph; // 差量更新 minimap 上的节点和边

    each(graph.getEdges(), function (edge) {
      _this.updateOneEdgeKeyShape(edge);
    });
    each(graph.getNodes(), function (node) {
      _this.updateOneNodeDelegateShape(node);
    });
    this.clearDestroyedShapes();
  };

  MiniMap.prototype.clearDestroyedShapes = function () {
    var itemMap = this.get('itemMap') || {};
    var keys = Object.keys(itemMap);
    if (!keys || keys.length === 0) return;

    for (var i = keys.length - 1; i >= 0; i--) {
      var shape = itemMap[keys[i]];
      var exist = shape.exist;
      shape.exist = false;

      if (!exist) {
        shape.remove();
        delete itemMap[keys[i]];
      }
    }
  };
  /**
   * 设置只显示 edge 的 keyShape
   * @param item IEdge 实例
   */


  MiniMap.prototype.updateOneEdgeKeyShape = function (item) {
    var canvas = this.get('canvas');
    var group = canvas.get('children')[0] || canvas.addGroup();
    var itemMap = this.get('itemMap') || {}; // 差量更新 minimap 上的一个节点，对应主图的 item

    var mappedItem = itemMap[item.get('id')];

    if (mappedItem) {
      var path = item.get('keyShape').attr('path');
      mappedItem.attr('path', path);
    } else {
      mappedItem = item.get('keyShape').clone();
      group.add(mappedItem);
      mappedItem.toBack();
    }

    if (!item.isVisible()) {
      mappedItem.hide();
    }

    mappedItem.exist = true;
    itemMap[item.get('id')] = mappedItem;
    this.set('itemMap', itemMap);
  };
  /**
   * Minimap 中展示自定义的 rect，支持用户自定义样式和节点大小
   * 增加/更新单个元素
   * @param item INode 实例
   */


  MiniMap.prototype.updateOneNodeDelegateShape = function (item) {
    var canvas = this.get('canvas');
    var group = canvas.get('children')[0] || canvas.addGroup();
    var delegateStyle = this.get('delegateStyle');
    var itemMap = this.get('itemMap') || {}; // 差量更新 minimap 上的一个节点，对应主图的 item

    var mappedItem = itemMap[item.get('id')];
    var bbox = item.getBBox(); // 计算了节点父组矩阵的 bbox

    if (mappedItem) {
      var attrs = {
        x: bbox.minX,
        y: bbox.minY,
        width: bbox.width,
        height: bbox.height
      };
      mappedItem.attr(attrs);
    } else {
      mappedItem = group.addShape('rect', {
        attrs: __assign({
          x: bbox.minX,
          y: bbox.minY,
          width: bbox.width,
          height: bbox.height
        }, delegateStyle),
        name: 'minimap-node-shape'
      });
    }

    if (!item.isVisible()) {
      mappedItem.hide();
    }

    mappedItem.exist = true;
    itemMap[item.get('id')] = mappedItem;
    this.set('itemMap', itemMap);
  };

  MiniMap.prototype.init = function () {
    this.initContainer();
    this.get('graph').on('afterupdateitem', this.handleUpdateCanvas);
    this.get('graph').on('afteritemstatechange', this.handleUpdateCanvas);
    this.get('graph').on('afteradditem', this.handleUpdateCanvas);
    this.get('graph').on('afterremoveitem', this.handleUpdateCanvas);
    this.get('graph').on('afterrender', this.handleUpdateCanvas);
    this.get('graph').on('afterlayout', this.handleUpdateCanvas);
  };
  /**
   * 初始化 Minimap 的容器
   */


  MiniMap.prototype.initContainer = function () {
    var self = this;
    var graph = self.get('graph');
    var size = self.get('size');
    var className = self.get('className');
    var parentNode = self.get('container');
    var container = createDOM("<div class='" + className + "' style='width: " + size[0] + "px; height: " + size[1] + "px; overflow: hidden'></div>");

    if (isString(parentNode)) {
      parentNode = document.getElementById(parentNode);
    }

    if (parentNode) {
      parentNode.appendChild(container);
    } else {
      graph.get('container').appendChild(container);
    }

    self.set('container', container);
    var containerDOM = createDOM('<div class="g6-minimap-container" style="position: relative;"></div>');
    container.appendChild(containerDOM);
    var canvas;
    var renderer = graph.get('renderer');

    if (renderer === SVG) {
      canvas = new GSVGCanvas({
        container: containerDOM,
        width: size[0],
        height: size[1]
      });
    } else {
      canvas = new GCanvas({
        container: containerDOM,
        width: size[0],
        height: size[1]
      });
    }

    self.set('canvas', canvas);
    self.updateCanvas();
  };

  MiniMap.prototype.updateCanvas = function () {
    // 如果是在动画，则不刷新视图
    var isRefresh = this.get('refresh');

    if (!isRefresh) {
      return;
    }

    var graph = this.get('graph');

    if (graph.get('destroyed')) {
      return;
    } // 如果是视口变换，也不刷新视图，但是需要重置视口大小和位置


    if (this.get('viewportChange')) {
      this.set('viewportChange', false);
      this.updateViewport();
    }

    var size = this.get('size');
    var canvas = this.get('canvas');
    var type = this.get('type');
    var padding = this.get('padding');

    if (canvas.destroyed) {
      return;
    }

    switch (type) {
      case DEFAULT_MODE:
        this.updateGraphShapes();
        break;

      case KEYSHAPE_MODE:
        this.updateKeyShapes();
        break;

      case DELEGATE_MODE:
        // 得到的节点直接带有 x 和 y，每个节点不存在父 group，即每个节点位置不由父 group 控制
        this.updateDelegateShapes();
        break;
    }

    var group = canvas.get('children')[0];
    if (!group) return;
    group.resetMatrix(); // 该 bbox 是准确的，不计算 matrix 的包围盒

    var bbox = group.getCanvasBBox();
    var graphBBox = graph.get('canvas').getBBox();
    var width = graphBBox.width;
    var height = graphBBox.height;

    if (Number.isFinite(bbox.width)) {
      // 刷新后bbox可能会变，需要重置画布矩阵以缩放到合适的大小
      width = max(bbox.width, width);
      height = max(bbox.height, height);
    }

    width += 2 * padding;
    height += 2 * padding;
    var ratio = Math.min(size[0] / width, size[1] / height);
    var matrix = mat3.create();
    var minX = 0;
    var minY = 0; // 平移到左上角

    if (Number.isFinite(bbox.minX)) {
      minX = -bbox.minX;
    }

    if (Number.isFinite(bbox.minY)) {
      minY = -bbox.minY;
    } // 缩放到适合视口后, 平移到画布中心


    var dx = (size[0] - (width - 2 * padding) * ratio) / 2;
    var dy = (size[1] - (height - 2 * padding) * ratio) / 2;
    matrix = transform(matrix, [['t', minX, minY], ['s', ratio, ratio], ['t', dx, dy]]);
    group.setMatrix(matrix); // 更新minimap视口

    this.set('ratio', ratio);
    this.set('totaldx', dx + minX * ratio);
    this.set('totaldy', dy + minY * ratio);
    this.set('dx', dx);
    this.set('dy', dy);
    this.updateViewport();
  };
  /**
   * 获取minimap的画布
   * @return {GCanvas} G的canvas实例
   */


  MiniMap.prototype.getCanvas = function () {
    return this.get('canvas');
  };
  /**
   * 获取minimap的窗口
   * @return {HTMLElement} 窗口的dom实例
   */


  MiniMap.prototype.getViewport = function () {
    return this.get('viewport');
  };
  /**
   * 获取minimap的容器dom
   * @return {HTMLElement} dom
   */


  MiniMap.prototype.getContainer = function () {
    return this.get('container');
  };

  MiniMap.prototype.destroy = function () {
    this.get('canvas').destroy();
    var container = this.get('container');
    container.parentNode.removeChild(container);
  };

  return MiniMap;
}(Base);

export default MiniMap;