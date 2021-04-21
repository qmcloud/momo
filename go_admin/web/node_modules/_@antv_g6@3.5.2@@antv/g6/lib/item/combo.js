"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _tslib = require("tslib");

var _node = _interopRequireDefault(require("./node"));

var _global = _interopRequireDefault(require("../global"));

var _graphic = require("../util/graphic");

var _isNumber = _interopRequireDefault(require("@antv/util/lib/is-number"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var CACHE_BBOX = 'bboxCache';
var CACHE_CANVAS_BBOX = 'bboxCanvasCache';
var CACHE_SIZE = 'sizeCache';
var CACHE_ANCHOR_POINTS = 'anchorPointsCache';

var Combo =
/** @class */
function (_super) {
  (0, _tslib.__extends)(Combo, _super);

  function Combo() {
    return _super !== null && _super.apply(this, arguments) || this;
  }

  Combo.prototype.getDefaultCfg = function () {
    return {
      type: 'combo',
      nodes: [],
      edges: [],
      combos: []
    };
  };

  Combo.prototype.getShapeCfg = function (model) {
    var styles = this.get('styles');
    var bbox = this.get('bbox');

    if (styles) {
      // merge graph的item样式与数据模型中的样式
      var newModel = model;
      var size = {
        r: Math.hypot(bbox.height, bbox.width) / 2 || _global.default.defaultCombo.size[0] / 2,
        width: bbox.width || _global.default.defaultCombo.size[0],
        height: bbox.height || _global.default.defaultCombo.size[1]
      };
      newModel.style = Object.assign({}, styles, model.style, size);
      var padding = model.padding || _global.default.defaultCombo.padding;

      if ((0, _isNumber.default)(padding)) {
        size.r += padding;
        size.width += padding * 2;
        size.height += padding * 2;
      } else {
        size.r += padding[0];
        size.width += padding[1] + padding[3] || padding[1] * 2;
        size.height += padding[0] + padding[2] || padding[0] * 2;
      }

      this.set(CACHE_SIZE, size);
      return newModel;
    }

    return model;
  };
  /**
   * 根据 keyshape 计算包围盒
   */


  Combo.prototype.calculateCanvasBBox = function () {
    var keyShape = this.get('keyShape');
    var group = this.get('group'); // 因为 group 可能会移动，所以必须通过父元素计算才能计算出正确的包围盒

    var bbox = (0, _graphic.getBBox)(keyShape, group);
    bbox.x = bbox.minX;
    bbox.y = bbox.minY;
    bbox.centerX = (bbox.minX + bbox.maxX) / 2;
    bbox.centerY = (bbox.minY + bbox.maxY) / 2;
    var cacheSize = this.get(CACHE_SIZE);

    if (cacheSize) {
      var keyShape_1 = this.get('keyShape');
      var type = keyShape_1.get('type');

      if (type === 'circle') {
        bbox.width = cacheSize.r * 2;
        bbox.height = cacheSize.r * 2;
      } else {
        bbox.width = cacheSize.width;
        bbox.height = cacheSize.height;
      }

      bbox.minX = bbox.centerX - bbox.width / 2;
      bbox.minY = bbox.centerY - bbox.height / 2;
      bbox.maxX = bbox.centerX + bbox.width / 2;
      bbox.maxY = bbox.centerY + bbox.height / 2;
    } else {
      bbox.width = bbox.maxX - bbox.minX;
      bbox.height = bbox.maxY - bbox.minY;
    }

    return bbox;
  };
  /**
   * 获取 Combo 中所有的子元素，包括 Combo、Node 及 Edge
   */


  Combo.prototype.getChildren = function () {
    var self = this;
    return {
      nodes: self.getNodes(),
      combos: self.getCombos()
    };
  };
  /**
   * 获取 Combo 中所有子节点
   */


  Combo.prototype.getNodes = function () {
    var self = this;
    return self.get('nodes');
  };
  /**
   * 获取 Combo 中所有子 combo
   */


  Combo.prototype.getCombos = function () {
    var self = this;
    return self.get('combos');
  };
  /**
   * 向 Combo 中增加子 combo 或 node
   * @param item Combo 或节点实例
   * @return boolean 添加成功返回 true，否则返回 false
   */


  Combo.prototype.addChild = function (item) {
    var self = this;
    var itemType = item.getType();

    switch (itemType) {
      case 'node':
        self.addNode(item);
        break;

      case 'combo':
        self.addCombo(item);
        break;

      default:
        console.warn('Only node or combo items are allowed to be added into a combo');
        return false;
    }

    return true;
  };
  /**
   * 向 Combo 中增加 combo
   * @param combo Combo 实例
   * @return boolean 添加成功返回 true，否则返回 false
   */


  Combo.prototype.addCombo = function (combo) {
    var self = this;
    self.get('combos').push(combo);
    return true;
  };
  /**
   * 向 Combo 中添加节点
   * @param node 节点实例
   * @return boolean 添加成功返回 true，否则返回 false
   */


  Combo.prototype.addNode = function (node) {
    var self = this;
    self.get('nodes').push(node);
    return true;
  };
  /**
   * 向 Combo 中增加子 combo 或 node
   * @param item Combo 或节点实例
   * @return boolean 添加成功返回 true，否则返回 false
   */


  Combo.prototype.removeChild = function (item) {
    var self = this;
    var itemType = item.getType();

    switch (itemType) {
      case 'node':
        self.removeNode(item);
        break;

      case 'combo':
        self.removeCombo(item);
        break;

      default:
        console.warn('Only node or combo items are allowed to be added into a combo');
        return false;
    }

    return true;
  };
  /**
   * 从 Combo 中移除指定的 combo
   * @param combo Combo 实例
   * @return boolean 移除成功返回 true，否则返回 false
   */


  Combo.prototype.removeCombo = function (combo) {
    var combos = this.getCombos();
    var index = combos.indexOf(combo);

    if (index > -1) {
      combos.splice(index, 1);
      return true;
    }

    return false;
  };
  /**
  * 向 Combo 中移除指定的节点
  * @param node 节点实例
  * @return boolean 移除成功返回 true，否则返回 false
  */


  Combo.prototype.removeNode = function (node) {
    var nodes = this.getNodes();
    var index = nodes.indexOf(node);

    if (index > -1) {
      nodes.splice(index, 1);
      return true;
    }

    return false;
  };

  Combo.prototype.isOnlyMove = function (cfg) {
    return false;
  };
  /**
   * 获取 item 的包围盒，这个包围盒是相对于 item 自己，不会将 matrix 计算在内
   * @return {Object} 包含 x,y,width,height, centerX, centerY
   */


  Combo.prototype.getBBox = function () {
    this.set(CACHE_CANVAS_BBOX, null);
    var bbox = this.calculateCanvasBBox(); // 计算 bbox 开销有些大，缓存
    // let bbox: IBBox = this.get(CACHE_BBOX);
    // if (!bbox) {
    //   bbox = this.getCanvasBBox();
    //   this.set(CACHE_BBOX, bbox);
    // }

    return bbox;
  };

  Combo.prototype.clearCache = function () {
    this.set(CACHE_BBOX, null); // 清理缓存的 bbox

    this.set(CACHE_CANVAS_BBOX, null);
    this.set(CACHE_ANCHOR_POINTS, null);
  };

  Combo.prototype.destroy = function () {
    if (!this.destroyed) {
      var animate = this.get('animate');
      var group = this.get('group');

      if (animate) {
        group.stopAnimate();
      }

      this.clearCache();
      this.set(CACHE_SIZE, null);
      this.set('bbox', null);
      group.remove();
      this._cfg = null;
      this.destroyed = true;
    }
  };

  return Combo;
}(_node.default);

var _default = Combo;
exports.default = _default;