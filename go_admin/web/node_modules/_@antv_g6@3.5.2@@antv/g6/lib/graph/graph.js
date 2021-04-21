"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _tslib = require("tslib");

var _eventEmitter = _interopRequireDefault(require("@antv/event-emitter"));

var _canvas = _interopRequireDefault(require("@antv/g-canvas/lib/canvas"));

var _canvas2 = _interopRequireDefault(require("@antv/g-svg/lib/canvas"));

var _lib = require("@antv/matrix-util/lib");

var _clone = _interopRequireDefault(require("@antv/util/lib/clone"));

var _deepMix = _interopRequireDefault(require("@antv/util/lib/deep-mix"));

var _each = _interopRequireDefault(require("@antv/util/lib/each"));

var _isPlainObject = _interopRequireDefault(require("@antv/util/lib/is-plain-object"));

var _isString = _interopRequireDefault(require("@antv/util/lib/is-string"));

var _isNumber = _interopRequireDefault(require("@antv/util/lib/is-number"));

var _group = require("../util/group");

var _math = require("../util/math");

var _lodash = require("lodash");

var _global = _interopRequireDefault(require("../global"));

var _controller = require("./controller");

var _createDom = _interopRequireDefault(require("@antv/dom-util/lib/create-dom"));

var _graphic = require("../util/graphic");

var _degree = _interopRequireDefault(require("../algorithm/degree"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var NODE = 'node';
var SVG = 'svg';
var CANVAS = 'canvas';

var Graph =
/** @class */
function (_super) {
  (0, _tslib.__extends)(Graph, _super);

  function Graph(cfg) {
    var _this = _super.call(this) || this;

    _this.cfg = (0, _deepMix.default)(_this.getDefaultCfg(), cfg);

    _this.init();

    _this.animating = false;
    _this.destroyed = false;
    return _this;
  }

  Graph.prototype.init = function () {
    this.initCanvas(); // instance controller

    var eventController = new _controller.EventController(this);
    var viewController = new _controller.ViewController(this);
    var modeController = new _controller.ModeController(this);
    var itemController = new _controller.ItemController(this);
    var layoutController = new _controller.LayoutController(this);
    var stateController = new _controller.StateController(this);
    var customGroupControll = new _controller.CustomGroup(this);
    this.set({
      eventController: eventController,
      viewController: viewController,
      modeController: modeController,
      itemController: itemController,
      layoutController: layoutController,
      stateController: stateController,
      customGroupControll: customGroupControll
    });
    this.initPlugin();
  };

  Graph.prototype.initCanvas = function () {
    var container = this.get('container');

    if ((0, _isString.default)(container)) {
      container = document.getElementById(container);
      this.set('container', container);
    }

    if (!container) {
      throw new Error('invalid container');
    }

    var width = this.get('width');
    var height = this.get('height');
    var renderer = this.get('renderer');
    var canvas;

    if (renderer === SVG) {
      canvas = new _canvas2.default({
        container: container,
        width: width,
        height: height
      });
    } else {
      canvas = new _canvas.default({
        container: container,
        width: width,
        height: height
      });
    }

    this.set('canvas', canvas);
    this.initGroups();
  };

  Graph.prototype.initPlugin = function () {
    var self = this;
    (0, _each.default)(self.get('plugins'), function (plugin) {
      if (!plugin.destroyed && plugin.initPlugin) {
        plugin.initPlugin(self);
      }
    });
  }; // 初始化所有 Group


  Graph.prototype.initGroups = function () {
    var canvas = this.get('canvas');
    var el = this.get('canvas').get('el');
    var id = el.id;
    var group = canvas.addGroup({
      id: id + "-root",
      className: _global.default.rootContainerClassName
    });

    if (this.get('groupByTypes')) {
      var edgeGroup = group.addGroup({
        id: id + "-edge",
        className: _global.default.edgeContainerClassName
      });
      var nodeGroup = group.addGroup({
        id: id + "-node",
        className: _global.default.nodeContainerClassName
      });
      var comboGroup = group.addGroup({
        id: id + "-combo",
        className: _global.default.comboContainerClassName
      }); // 用于存储自定义的群组

      var customGroup = group.addGroup({
        id: id + "-group",
        className: _global.default.customGroupContainerClassName
      });
      customGroup.toBack();
      comboGroup.toBack();
      this.set({
        nodeGroup: nodeGroup,
        edgeGroup: edgeGroup,
        customGroup: customGroup,
        comboGroup: comboGroup
      });
    }

    var delegateGroup = group.addGroup({
      id: id + "-delegate",
      className: _global.default.delegateContainerClassName
    });
    this.set({
      delegateGroup: delegateGroup
    });
    this.set('group', group);
  }; // eslint-disable-next-line class-methods-use-this


  Graph.prototype.getDefaultCfg = function () {
    return {
      /**
       * Container could be dom object or dom id
       */
      container: undefined,

      /**
       * Canvas width
       * unit pixel if undefined force fit width
       */
      width: undefined,

      /**
       * Canvas height
       * unit pixel if undefined force fit height
       */
      height: undefined,

      /**
       * renderer canvas or svg
       * @type {string}
       */
      renderer: 'canvas',

      /**
       * control graph behaviors
       */
      modes: {},

      /**
       * 注册插件
       */
      plugins: [],

      /**
       * source data
       */
      data: {},

      /**
       * Fit view padding (client scale)
       */
      fitViewPadding: 10,

      /**
       * Minimum scale size
       */
      minZoom: 0.2,

      /**
       * Maxmum scale size
       */
      maxZoom: 10,

      /**
       *  capture events
       */
      event: true,

      /**
       * group node & edges into different graphic groups
       */
      groupByTypes: true,

      /**
       * determine if it's a directed graph
       */
      directed: false,

      /**
       * when data or shape changed, should canvas draw automatically
       */
      autoPaint: true,

      /**
       * store all the node instances
       */
      nodes: [],

      /**
       * store all the edge instances
       */
      edges: [],

      /**
       * store all the combo instances
       */
      combos: [],

      /**
       * store all the edge instances which are virtual edges related to collapsed combo
       */
      vedges: [],

      /**
       * all the instances indexed by id
       */
      itemMap: {},

      /**
       * 边直接连接到节点的中心，不再考虑锚点
       */
      linkCenter: false,

      /**
       * 默认的节点配置，data 上定义的配置会覆盖这些配置。例如：
       * defaultNode: {
       *  type: 'rect',
       *  size: [60, 40],
       *  style: {
       *    //... 样式配置项
       *  }
       * }
       * 若数据项为 { id: 'node', x: 100, y: 100 }
       * 实际创建的节点模型是 { id: 'node', x: 100, y: 100， type: 'rect', size: [60, 40] }
       * 若数据项为 { id: 'node', x: 100, y: 100, type: 'circle' }
       * 实际创建的节点模型是 { id: 'node', x: 100, y: 100， type: 'circle', size: [60, 40] }
       */
      defaultNode: {},

      /**
       * 默认边配置，data 上定义的配置会覆盖这些配置。用法同 defaultNode
       */
      defaultEdge: {},

      /**
       * 节点默认样式，也可以添加状态样式
       * 例如：
       * const graph = new G6.Graph({
       *  nodeStateStyles: {
       *    selected: { fill: '#ccc', stroke: '#666' },
       *    active: { lineWidth: 2 }
       *  },
       *  ...
       * });
       *
       */
      nodeStateStyles: {},

      /**
       * 边默认样式，用法同nodeStateStyle
       */
      edgeStateStyles: {},

      /**
       * graph 状态
       */
      states: {},

      /**
       * 是否启用全局动画
       */
      animate: false,

      /**
       * 动画设置,仅在 animate 为 true 时有效
       */
      animateCfg: {
        /**
         * 帧回调函数，用于自定义节点运动路径，为空时线性运动
         */
        onFrame: undefined,

        /**
         * 动画时长(ms)
         */
        duration: 500,

        /**
         * 指定动画动效
         */
        easing: 'easeLinear'
      },
      callback: undefined,

      /**
       * group类型
       */
      groupType: 'circle',

      /**
       * group bbox 对象
       * @private
       */
      groupBBoxs: {},

      /**
       * 以groupid分组的节点数据
       * @private
       */
      groupNodes: {},

      /**
       * group 数据
       */
      groups: [],

      /**
       * group样式
       */
      groupStyle: {}
    };
  };
  /**
   * 将值设置到 this.cfg 变量上面
   * @param key 键 或 对象值
   * @param val 值
   */


  Graph.prototype.set = function (key, val) {
    if ((0, _isPlainObject.default)(key)) {
      this.cfg = Object.assign({}, this.cfg, key);
    } else {
      this.cfg[key] = val;
    }

    return this;
  };
  /**
   * 获取 this.cfg 中的值
   * @param key 键
   */


  Graph.prototype.get = function (key) {
    return this.cfg[key];
  };
  /**
   * 清理元素多个状态
   * @param {string|Item} item 元素id或元素实例
   * @param {string[]} states 状态
   */


  Graph.prototype.clearItemStates = function (item, states) {
    if ((0, _isString.default)(item)) {
      item = this.findById(item);
    }

    var itemController = this.get('itemController');
    itemController.clearItemStates(item, states);

    if (!states) {
      states = item.get('states');
    }

    var stateController = this.get('stateController');
    stateController.updateStates(item, states, false);
  };
  /**
   * 设置各个节点样式，以及在各种状态下节点 keyShape 的样式。
   * 若是自定义节点切在各种状态下
   * graph.node(node => {
   *  return {
   *    type: 'rect',
   *    label: node.id,
   *    style: { fill: '#666' },
   *    stateStyles: {
   *       selected: { fill: 'blue' },
   *       custom: { fill: 'green' }
   *     }
   *   }
   * });
   * @param {function} nodeFn 指定每个节点样式
   */


  Graph.prototype.node = function (nodeFn) {
    if (typeof nodeFn === 'function') {
      this.set('nodeMapper', nodeFn);
    }
  };
  /**
   * 设置各个边样式
   * @param {function} edgeFn 指定每个边的样式,用法同 node
   */


  Graph.prototype.edge = function (edgeFn) {
    if (typeof edgeFn === 'function') {
      this.set('edgeMapper', edgeFn);
    }
  };
  /**
   * 设置各个 combo 的配置
   * @param comboFn
   */


  Graph.prototype.combo = function (comboFn) {
    if (typeof comboFn === 'function') {
      this.set('comboMapper', comboFn);
    }
  };
  /**
   * 根据 ID 查询图元素实例
   * @param id 图元素 ID
   */


  Graph.prototype.findById = function (id) {
    return this.get('itemMap')[id];
  };
  /**
   * 根据对应规则查找单个元素
   * @param {ITEM_TYPE} type 元素类型(node | edge | group)
   * @param {(item: T, index: number) => T} fn 指定规则
   * @return {T} 元素实例
   */


  Graph.prototype.find = function (type, fn) {
    var result;
    var items = this.get(type + "s"); // eslint-disable-next-line consistent-return

    (0, _each.default)(items, function (item, i) {
      if (fn(item, i)) {
        result = item;
        return result;
      }
    });
    return result;
  };
  /**
   * 查找所有满足规则的元素
   * @param {string} type 元素类型(node|edge)
   * @param {string} fn 指定规则
   * @return {array} 元素实例
   */


  Graph.prototype.findAll = function (type, fn) {
    var result = [];
    (0, _each.default)(this.get(type + "s"), function (item, i) {
      if (fn(item, i)) {
        result.push(item);
      }
    });
    return result;
  };
  /**
   * 查找所有处于指定状态的元素
   * @param {string} type 元素类型(node|edge)
   * @param {string} state 状态
   * @return {object} 元素实例
   */


  Graph.prototype.findAllByState = function (type, state) {
    return this.findAll(type, function (item) {
      return item.hasState(state);
    });
  };
  /**
   * 平移画布
   * @param dx 水平方向位移
   * @param dy 垂直方向位移
   */


  Graph.prototype.translate = function (dx, dy) {
    var group = this.get('group');
    var matrix = (0, _clone.default)(group.getMatrix());

    if (!matrix) {
      matrix = _lib.mat3.create();
    }

    _lib.mat3.translate(matrix, matrix, [dx, dy]);

    group.setMatrix(matrix);
    this.emit('viewportchange', {
      action: 'translate',
      matrix: group.getMatrix()
    });
    this.autoPaint();
  };
  /**
   * 平移画布到某点
   * @param {number} x 水平坐标
   * @param {number} y 垂直坐标
   */


  Graph.prototype.moveTo = function (x, y) {
    var group = this.get('group');
    (0, _math.move)(group, {
      x: x,
      y: y
    });
    this.emit('viewportchange', {
      action: 'move',
      matrix: group.getMatrix()
    });
  };
  /**
   * 调整视口适应视图
   * @param {object} padding 四周围边距
   */


  Graph.prototype.fitView = function (padding) {
    if (padding) {
      this.set('fitViewPadding', padding);
    }

    var viewController = this.get('viewController');
    viewController.fitView();
    this.autoPaint();
  };
  /**
   * 调整视口适应视图，不缩放，仅将图 bbox 中心对齐到画布中心
   */


  Graph.prototype.fitCenter = function () {
    var viewController = this.get('viewController');
    viewController.fitCenter();
    this.autoPaint();
  };
  /**
   * 新增行为
   * @param {string | ModeOption | ModeType[]} behaviors 添加的行为
   * @param {string | string[]} modes 添加到对应的模式
   * @return {Graph} Graph
   */


  Graph.prototype.addBehaviors = function (behaviors, modes) {
    var modeController = this.get('modeController');
    modeController.manipulateBehaviors(behaviors, modes, true);
    return this;
  };
  /**
   * 移除行为
   * @param {string | ModeOption | ModeType[]} behaviors 移除的行为
   * @param {string | string[]} modes 从指定的模式中移除
   * @return {Graph} Graph
   */


  Graph.prototype.removeBehaviors = function (behaviors, modes) {
    var modeController = this.get('modeController');
    modeController.manipulateBehaviors(behaviors, modes, false);
    return this;
  };
  /**
   * 伸缩窗口
   * @param ratio 伸缩比例
   * @param center 以center的x, y坐标为中心缩放
   */


  Graph.prototype.zoom = function (ratio, center) {
    var group = this.get('group');
    var matrix = (0, _clone.default)(group.getMatrix());
    var minZoom = this.get('minZoom');
    var maxZoom = this.get('maxZoom');

    if (!matrix) {
      matrix = _lib.mat3.create();
    }

    if (center) {
      _lib.mat3.translate(matrix, matrix, [-center.x, -center.y]);

      _lib.mat3.scale(matrix, matrix, [ratio, ratio]);

      _lib.mat3.translate(matrix, matrix, [center.x, center.y]);
    } else {
      _lib.mat3.scale(matrix, matrix, [ratio, ratio]);
    }

    if (minZoom && matrix[0] < minZoom || maxZoom && matrix[0] > maxZoom) {
      return;
    }

    group.setMatrix(matrix);
    this.emit('viewportchange', {
      action: 'zoom',
      matrix: matrix
    });
    this.autoPaint();
  };
  /**
   * 伸缩视口到一固定比例
   * @param {number} toRatio 伸缩比例
   * @param {Point} center 以center的x, y坐标为中心缩放
   */


  Graph.prototype.zoomTo = function (toRatio, center) {
    var ratio = toRatio / this.getZoom();
    this.zoom(ratio, center);
  };
  /**
   * 将元素移动到视口中心
   * @param {Item} item 指定元素
   */


  Graph.prototype.focusItem = function (item) {
    var viewController = this.get('viewController');
    viewController.focus(item);
    this.autoPaint();
  };
  /**
   * 自动重绘
   * @internal 仅供内部更新机制调用，外部根据需求调用 render 或 paint 接口
   */


  Graph.prototype.autoPaint = function () {
    if (this.get('autoPaint')) {
      this.paint();
    }
  };
  /**
   * 仅画布重新绘制
   */


  Graph.prototype.paint = function () {
    this.emit('beforepaint');
    this.get('canvas').draw();
    this.emit('afterpaint');
  };
  /**
   * 将屏幕坐标转换为视口坐标
   * @param {number} clientX 屏幕x坐标
   * @param {number} clientY 屏幕y坐标
   * @return {Point} 视口坐标
   */


  Graph.prototype.getPointByClient = function (clientX, clientY) {
    var viewController = this.get('viewController');
    return viewController.getPointByClient(clientX, clientY);
  };
  /**
   * 将视口坐标转换为屏幕坐标
   * @param {number} x 视口x坐标
   * @param {number} y 视口y坐标
   * @return {Point} 视口坐标
   */


  Graph.prototype.getClientByPoint = function (x, y) {
    var viewController = this.get('viewController');
    return viewController.getClientByPoint(x, y);
  };
  /**
   * 将画布坐标转换为视口坐标
   * @param {number} canvasX 画布 x 坐标
   * @param {number} canvasY 画布 y 坐标
   * @return {object} 视口坐标
   */


  Graph.prototype.getPointByCanvas = function (canvasX, canvasY) {
    var viewController = this.get('viewController');
    return viewController.getPointByCanvas(canvasX, canvasY);
  };
  /**
   * 将视口坐标转换为画布坐标
   * @param {number} x 视口 x 坐标
   * @param {number} y 视口 y 坐标
   * @return {object} 画布坐标
   */


  Graph.prototype.getCanvasByPoint = function (x, y) {
    var viewController = this.get('viewController');
    return viewController.getCanvasByPoint(x, y);
  };
  /**
   * 显示元素
   * @param {Item} item 指定元素
   */


  Graph.prototype.showItem = function (item) {
    var itemController = this.get('itemController');
    itemController.changeItemVisibility(item, true);
  };
  /**
   * 隐藏元素
   * @param {Item} item 指定元素
   */


  Graph.prototype.hideItem = function (item) {
    var itemController = this.get('itemController');
    itemController.changeItemVisibility(item, false);
  };
  /**
   * 刷新元素
   * @param {string|object} item 元素id或元素实例
   */


  Graph.prototype.refreshItem = function (item) {
    var itemController = this.get('itemController');
    itemController.refreshItem(item);
  };
  /**
   * 设置是否在更新/刷新后自动重绘
   * @param {boolean} auto 自动重绘
   */


  Graph.prototype.setAutoPaint = function (auto) {
    var self = this;
    self.set('autoPaint', auto);
    var canvas = self.get('canvas');
    canvas.set('autoDraw', auto);
  };
  /**
   * 删除元素
   * @param {Item} item 元素id或元素实例
   */


  Graph.prototype.remove = function (item) {
    this.removeItem(item);
  };
  /**
   * 删除元素
   * @param {Item} item 元素id或元素实例
   */


  Graph.prototype.removeItem = function (item) {
    // 如果item是字符串，且查询的节点实例不存在，则认为是删除group
    var nodeItem = item;
    if ((0, _isString.default)(item)) nodeItem = this.findById(item);

    if (!nodeItem && (0, _isString.default)(item)) {
      console.warn('The item to be removed does not exist!');
      var customGroupControll = this.get('customGroupControll');
      customGroupControll.remove(item);
    } else if (nodeItem) {
      var type = '';
      if (nodeItem.getType) type = nodeItem.getType();
      var itemController = this.get('itemController');
      itemController.removeItem(item);

      if (type === 'combo') {
        var newComboTrees = (0, _graphic.reconstructTree)(this.get('comboTrees'));
        this.set('comboTrees', newComboTrees);
      }
    }
  };
  /**
   * 新增元素 或 节点分组
   * @param {string} type 元素类型(node | edge | group)
   * @param {ModelConfig} model 元素数据模型
   * @return {Item} 元素实例
   */


  Graph.prototype.addItem = function (type, model) {
    var itemController = this.get('itemController');

    if (type === 'group') {
      var groupId = model.groupId,
          nodes = model.nodes,
          groupType = model.type,
          zIndex = model.zIndex,
          title = model.title;
      var groupTitle = title;

      if ((0, _isString.default)(title)) {
        groupTitle = {
          text: title
        };
      }

      return this.get('customGroupControll').create(groupId, nodes, groupType, zIndex, true, groupTitle);
    }

    if (model.id && this.findById(model.id)) {
      console.warn('This item exists already. Be sure the id is unique.');
      return;
    }

    var item;
    var comboTrees = this.get('comboTrees');
    if (!comboTrees) comboTrees = [];

    if (type === 'combo') {
      var itemMap_1 = this.get('itemMap');
      var foundParent_1 = false;
      comboTrees.forEach(function (ctree) {
        if (foundParent_1) return; // terminate the forEach after the tree containing the item is done

        (0, _graphic.traverseTreeUp)(ctree, function (child) {
          // find the parent
          if (model.parentId === child.id) {
            foundParent_1 = true;
            var newCombo = (0, _tslib.__assign)({
              id: model.id,
              depth: child.depth + 2
            }, model);
            if (child.children) child.children.push(newCombo);else child.children = [newCombo];
            model.depth = newCombo.depth;
            item = itemController.addItem(type, model);
          }

          var childItem = itemMap_1[child.id]; // after the parent is found, update all the ancestors

          if (foundParent_1 && childItem && childItem.getType && childItem.getType() === 'combo') {
            itemController.updateCombo(childItem, child.children);
          }

          return true;
        });
      }); // if the parent is not found, add it to the root

      if (!foundParent_1) {
        var newCombo = (0, _tslib.__assign)({
          id: model.id,
          depth: 0
        }, model);
        model.depth = newCombo.depth;
        comboTrees.push(newCombo);
        item = itemController.addItem(type, model);
      }

      this.set('comboTrees', comboTrees);
    } else if (type === 'node' && (0, _isString.default)(model.comboId) && comboTrees) {
      var parentCombo = this.findById(model.comboId);

      if (parentCombo && parentCombo.getType && parentCombo.getType() !== 'combo') {
        console.warn("'" + model.comboId + "' is a not id of a combo in the graph, the node will be added without combo.");
        return;
      }

      item = itemController.addItem(type, model);
      var itemMap_2 = this.get('itemMap');
      var foundParent_2 = false,
          foundNode_1 = false;
      comboTrees && comboTrees.forEach(function (ctree) {
        if (foundNode_1 || foundParent_2) return; // terminate the forEach

        (0, _graphic.traverseTreeUp)(ctree, function (child) {
          if (child.id === model.id) {
            // if the item exists in the tree already, terminate
            foundNode_1 = true;
            return false;
          }

          if (model.comboId === child.id && !foundNode_1) {
            // found the parent, add the item to the children of its parent in the tree
            foundParent_2 = true;
            var cloneNode = (0, _clone.default)(model);
            cloneNode.itemType = 'node';
            if (child.children) child.children.push(cloneNode);else child.children = [cloneNode];
            model.depth = child.depth + 1;
          } // update the size of all the ancestors


          if (foundParent_2 && itemMap_2[child.id].getType && itemMap_2[child.id].getType() === 'combo') {
            itemController.updateCombo(itemMap_2[child.id], child.children);
          }

          return true;
        });
      });
    } else {
      item = itemController.addItem(type, model);
    }

    if (type === 'node' && model.comboId || type === 'combo' && model.parentId) {
      // add the combo to the parent's children array
      var parentCombo = this.findById(model.comboId || model.parentId);
      if (parentCombo) parentCombo.addChild(item);
    }

    var combos = this.get('combos');

    if (combos && combos.length > 0) {
      this.sortCombos();
    }

    this.autoPaint();
    return item;
  };

  Graph.prototype.add = function (type, model) {
    return this.addItem(type, model);
  };
  /**
   * 更新元素
   * @param {Item} item 元素id或元素实例
   * @param {Partial<NodeConfig> | EdgeConfig} cfg 需要更新的数据
   */


  Graph.prototype.updateItem = function (item, cfg) {
    var _this = this;

    var itemController = this.get('itemController');
    var currentItem;

    if ((0, _isString.default)(item)) {
      currentItem = this.findById(item);
    } else {
      currentItem = item;
    }

    var type = '';
    if (currentItem.getType) type = currentItem.getType();
    var states = (0, _tslib.__spreadArrays)(currentItem.getStates());

    if (type === 'combo') {
      (0, _each.default)(states, function (state) {
        return _this.setItemState(currentItem, state, false);
      });
    }

    itemController.updateItem(currentItem, cfg);

    if (type === 'combo') {
      (0, _each.default)(states, function (state) {
        return _this.setItemState(currentItem, state, true);
      });
    }
  };
  /**
   * 更新元素
   * @param {Item} item 元素id或元素实例
   * @param {Partial<NodeConfig> | EdgeConfig} cfg 需要更新的数据
   */


  Graph.prototype.update = function (item, cfg) {
    this.updateItem(item, cfg);
  };
  /**
   * 设置元素状态
   * @param {Item} item 元素id或元素实例
   * @param {string} state 状态名称
   * @param {string | boolean} value 是否启用状态 或 状态值
   */


  Graph.prototype.setItemState = function (item, state, value) {
    if ((0, _isString.default)(item)) {
      item = this.findById(item);
    }

    var itemController = this.get('itemController');
    itemController.setItemState(item, state, value);
    var stateController = this.get('stateController');

    if ((0, _isString.default)(value)) {
      stateController.updateState(item, state + ":" + value, true);
    } else {
      stateController.updateState(item, state, value);
    }
  };
  /**
   * 设置视图初始化数据
   * @param {GraphData} data 初始化数据
   */


  Graph.prototype.data = function (data) {
    this.set('data', data);
  };
  /**
   * 根据data接口的数据渲染视图
   */


  Graph.prototype.render = function () {
    var self = this;
    var data = this.get('data');

    if (!data) {
      throw new Error('data must be defined first');
    }

    var _a = data.nodes,
        nodes = _a === void 0 ? [] : _a,
        _b = data.edges,
        edges = _b === void 0 ? [] : _b,
        _c = data.combos,
        combos = _c === void 0 ? [] : _c;
    this.clear();
    this.emit('beforerender');
    (0, _each.default)(nodes, function (node) {
      self.add('node', node);
    }); // process the data to tree structure

    if (combos && combos.length !== 0) {
      var comboTrees = (0, _graphic.plainCombosToTrees)(combos, nodes);
      this.set('comboTrees', comboTrees); // add combos

      self.addCombos(combos);
    }

    (0, _each.default)(edges, function (edge) {
      self.add('edge', edge);
    }); // layout

    var layoutController = self.get('layoutController');

    if (!layoutController.layout(success)) {
      success();
    }

    function success() {
      if (self.get('fitView')) {
        self.fitView();
      } else if (self.get('fitCenter')) {
        self.fitCenter();
      }

      self.autoPaint();
      self.emit('afterrender');
    }

    if (!this.get('groupByTypes')) {
      if (combos && combos.length !== 0) {
        this.sortCombos();
      } else {
        // 为提升性能，选择数量少的进行操作
        if (data.nodes && data.edges && data.nodes.length < data.edges.length) {
          var nodesArr = this.getNodes(); // 遍历节点实例，将所有节点提前。

          nodesArr.forEach(function (node) {
            node.toFront();
          });
        } else {
          var edgesArr = this.getEdges(); // 遍历节点实例，将所有节点提前。

          edgesArr.forEach(function (edge) {
            edge.toBack();
          });
        }
      }
    } // 防止传入的数据不存在nodes


    if (data.nodes) {
      // 获取所有有groupID的node
      var nodeInGroup = data.nodes.filter(function (node) {
        return node.groupId;
      }); // 所有node中存在groupID，则说明需要群组

      if (nodeInGroup.length > 0) {
        // 渲染群组
        var groupType = self.get('groupType');
        this.renderCustomGroup(data, groupType);
      }
    }
  };
  /**
   * 接收数据进行渲染
   * @Param {Object} data 初始化数据
   */


  Graph.prototype.read = function (data) {
    this.data(data);
    this.render();
  }; // 比较item


  Graph.prototype.diffItems = function (type, items, models) {
    var self = this;
    var item;
    var itemMap = this.get('itemMap');
    (0, _each.default)(models, function (model) {
      item = itemMap[model.id];

      if (item) {
        if (self.get('animate') && type === NODE) {
          var containerMatrix = item.getContainer().getMatrix();
          if (!containerMatrix) containerMatrix = _lib.mat3.create();
          item.set('originAttrs', {
            x: containerMatrix[6],
            y: containerMatrix[7]
          });
        }

        self.updateItem(item, model);
      } else {
        item = self.addItem(type, model);
      }

      items[type + "s"].push(item);
    });
  };
  /**
   * 更改源数据，根据新数据重新渲染视图
   * @param {object} data 源数据
   * @return {object} this
   */


  Graph.prototype.changeData = function (data) {
    var self = this;

    if (!data) {
      return this;
    } // 更改数据源后，取消所有状态


    this.getNodes().map(function (node) {
      return self.clearItemStates(node);
    });
    this.getEdges().map(function (edge) {
      return self.clearItemStates(edge);
    });
    var canvas = this.get('canvas');
    var localRefresh = canvas.get('localRefresh');
    canvas.set('localRefresh', false);

    if (!self.get('data')) {
      self.data(data);
      self.render();
    }

    var itemMap = this.get('itemMap');
    var items = {
      nodes: [],
      edges: []
    };
    var combosData = data.combos;

    if (combosData) {
      var comboTrees = (0, _graphic.plainCombosToTrees)(combosData, data.nodes);
      this.set('comboTrees', comboTrees);
    }

    this.diffItems('node', items, data.nodes);
    this.diffItems('edge', items, data.edges);
    (0, _each.default)(itemMap, function (item, id) {
      itemMap[id].getModel().depth = 0;

      if (item.getType && item.getType() === 'combo') {
        delete itemMap[id];
        item.destroy();
      } else if (items.nodes.indexOf(item) < 0 && items.edges.indexOf(item) < 0) {
        delete itemMap[id];
        self.remove(item);
      }
    }); // clear the destroyed combos here to avoid removing sub nodes before removing the parent combo

    var comboItems = this.getCombos();
    var combosLength = comboItems.length;

    for (var i = combosLength - 1; i >= 0; i--) {
      if (comboItems[i].destroyed) {
        comboItems.splice(i, 1);
      }
    } // process the data to tree structure


    if (combosData) {
      // add combos
      self.addCombos(combosData);
      if (!this.get('groupByTypes')) this.sortCombos();
    }

    this.set({
      nodes: items.nodes,
      edges: items.edges
    });
    var layoutController = this.get('layoutController');
    layoutController.changeData();

    if (self.get('animate') && !layoutController.getLayoutType()) {
      // 如果没有指定布局
      self.positionsAnimate();
    } else {
      self.autoPaint();
    }

    setTimeout(function () {
      canvas.set('localRefresh', localRefresh);
    }, 16);
    return this;
  };
  /**
   * 私有方法，在 render 和 changeData 的时候批量添加数据中所有平铺的 combos
   * @param {ComboConfig[]} combos 平铺的 combos 数据
   */


  Graph.prototype.addCombos = function (combos) {
    var self = this;
    var comboTrees = self.get('comboTrees');
    var itemController = this.get('itemController');
    itemController.addCombos(comboTrees, combos);
  };
  /**
   * 根据已经存在的节点或 combo 创建新的 combo
   * @param combo combo ID 或 Combo 配置
   * @param elements 添加到 Combo 中的元素，包括节点和 combo
   */


  Graph.prototype.createCombo = function (combo, elements) {
    var _this = this; // step 1: 创建新的 Combo


    var comboId = '';

    if ((0, _isString.default)(combo)) {
      comboId = combo;
      this.addItem('combo', {
        id: combo
      });
    } else {
      comboId = combo.id;

      if (!comboId) {
        console.warn('Create combo failed. Please assign a unique string id for the adding combo.');
        return;
      }

      this.addItem('combo', combo);
    }

    var currentCombo = this.findById(comboId);
    var trees = elements.map(function (elementId) {
      var item = _this.findById(elementId); // step 2: 将元素添加到 Combo 中


      currentCombo.addChild(item);
      var model = item.getModel();
      var type = '';
      if (item.getType) type = item.getType();

      if (type === 'combo') {
        model.parentId = comboId;
      } else if (type === 'node') {
        model.comboId = comboId;
      }

      return (0, _tslib.__assign)({
        depth: 1,
        itemType: type
      }, model);
    }); // step3: 更新 comboTrees 结构

    var comboTrees = this.get('comboTrees');
    comboTrees && comboTrees.forEach(function (ctree) {
      if (ctree.id === comboId) {
        ctree.itemType = 'combo';
        ctree.children = trees;
      }
    });
    this.updateCombos();
  };
  /**
   * 解散 combo
   * @param {String | INode | ICombo} combo 需要被解散的 Combo item 或 id
   */


  Graph.prototype.uncombo = function (combo) {
    var _this = this;

    var self = this;
    var comboItem = combo;

    if ((0, _isString.default)(combo)) {
      comboItem = this.findById(combo);
    }

    if (!comboItem || comboItem.getType && comboItem.getType() !== 'combo') {
      console.warn('The item is not a combo!');
      return;
    }

    var parentId = comboItem.getModel().parentId;
    var comboTrees = self.get('comboTrees');
    if (!comboTrees) comboTrees = [];
    var itemMap = this.get('itemMap');
    var comboId = comboItem.get('id');
    var treeToBeUncombo;
    var brothers = [];
    var comboItems = this.get('combos');
    var parentItem = this.findById(parentId);
    comboTrees.forEach(function (ctree) {
      if (treeToBeUncombo) return; // terminate the forEach

      (0, _graphic.traverseTreeUp)(ctree, function (subtree) {
        // find the combo to be uncomboed, delete the combo from map and cache
        if (subtree.id === comboId) {
          treeToBeUncombo = subtree; // delete the related edges

          var edges = comboItem.getEdges();
          edges.forEach(function (edge) {
            _this.removeItem(edge);
          });
          var index = comboItems.indexOf(combo);
          comboItems.splice(index, 1);
          delete itemMap[comboId];
          comboItem.destroy();
        } // find the parent to remove the combo from the combo's brothers array and add the combo's children to the combo's brothers array in the tree


        if (parentId && treeToBeUncombo && subtree.id === parentId) {
          parentItem.removeCombo(comboItem);
          brothers = subtree.children; // the combo's brothers
          // remove the combo from its brothers array

          var index = brothers.indexOf(treeToBeUncombo);

          if (index !== -1) {
            brothers.splice(index, 1);
          } // append the combo's children to the combo's brothers array


          treeToBeUncombo.children && treeToBeUncombo.children.forEach(function (child) {
            var item = _this.findById(child.id);

            var childModel = item.getModel();

            if (item.getType && item.getType() === 'combo') {
              child.parentId = parentId;
              delete child.comboId;
              childModel.parentId = parentId; // update the parentId of the model

              delete childModel.comboId;
            } else if (item.getType && item.getType() === 'node') {
              child.comboId = parentId;
              childModel.comboId = parentId; // update the parentId of the model
            }

            parentItem.addChild(item);
            brothers.push(child);
          });
          return false;
        }

        return true;
      });
    }); // if the parentId is not found, remove the combo from the roots

    if (!parentId && treeToBeUncombo) {
      var index = comboTrees.indexOf(treeToBeUncombo);
      comboTrees.splice(index, 1); // modify the parentId of the children

      treeToBeUncombo.children && treeToBeUncombo.children.forEach(function (child) {
        child.parentId = undefined;

        var childModel = _this.findById(child.id).getModel();

        childModel.parentId = undefined; // update the parentId of the model

        if (child.itemType !== 'node') comboTrees.push(child);
      });
    }
  };
  /**
   * 根据节点的 bbox 更新所有 combos 的绘制，包括 combos 的位置和范围
   */


  Graph.prototype.updateCombos = function () {
    var _this = this;

    var self = this;
    var comboTrees = this.get('comboTrees');
    var itemController = self.get('itemController');
    var itemMap = self.get('itemMap');
    comboTrees && comboTrees.forEach(function (ctree) {
      (0, _graphic.traverseTreeUp)(ctree, function (child) {
        if (!child) {
          return true;
        }

        var childItem = itemMap[child.id];

        if (childItem && childItem.getType && childItem.getType() === 'combo') {
          // 更新具体的 Combo 之前先清除所有的已有状态，以免将 state 中的样式更新为 Combo 的样式
          var states = (0, _tslib.__spreadArrays)(childItem.getStates());
          (0, _each.default)(states, function (state) {
            return _this.setItemState(childItem, state, false);
          }); // 更新具体的 Combo

          itemController.updateCombo(childItem, child.children); // 更新 Combo 后，还原已有的状态

          (0, _each.default)(states, function (state) {
            return _this.setItemState(childItem, state, true);
          });
        }

        return true;
      });
    });
    self.sortCombos();
  };
  /**
   * 根据节点的 bbox 更新 combo 及其祖先 combos 的绘制，包括 combos 的位置和范围
   * @param {String | ICombo} combo 需要被更新的 Combo 或 id，若指定，则该 Combo 及所有祖先 Combod 都会被更新
   */


  Graph.prototype.updateCombo = function (combo) {
    var _this = this;

    var self = this;
    var comboItem = combo;
    var comboId;

    if ((0, _isString.default)(combo)) {
      comboItem = this.findById(combo);
    }

    if (!comboItem || comboItem.getType && comboItem.getType() !== 'combo') {
      console.warn('The item to be updated is not a combo!');
      return;
    }

    comboId = comboItem.get('id');
    var comboTrees = this.get('comboTrees');
    var itemController = self.get('itemController');
    var itemMap = self.get('itemMap');
    comboTrees && comboTrees.forEach(function (ctree) {
      (0, _graphic.traverseTreeUp)(ctree, function (child) {
        if (!child) {
          return true;
        }

        var childItem = itemMap[child.id];

        if (comboId === child.id && childItem && childItem.getType && childItem.getType() === 'combo') {
          // 更新具体的 Combo 之前先清除所有的已有状态，以免将 state 中的样式更新为 Combo 的样式
          var states = (0, _tslib.__spreadArrays)(childItem.getStates());
          (0, _each.default)(states, function (state) {
            return _this.setItemState(childItem, state, false);
          }); // 更新具体的 Combo

          itemController.updateCombo(childItem, child.children); // 更新 Combo 后，还原已有的状态

          (0, _each.default)(states, function (state) {
            return _this.setItemState(childItem, state, true);
          });
          if (comboId) comboId = child.parentId;
        }

        return true;
      });
    });
  };
  /**
   * 更新树结构，例如移动子树等
   * @param {String | INode | ICombo} item 需要被更新的 Combo 或 节点 id
   * @param {string | undefined} parentId 新的父 combo id，undefined 代表没有父 combo
   */


  Graph.prototype.updateComboTree = function (item, parentId) {
    var self = this;
    var uItem;

    if ((0, _isString.default)(item)) {
      uItem = self.findById(item);
    } else {
      uItem = item;
    }

    var model = uItem.getModel();
    var oldParentId = model.comboId || model.parentId; // 当 combo 存在parentId 或 comboId 时，才将其移除

    if (model.parentId || model.comboId) {
      var combo = this.findById(model.parentId || model.comboId);

      if (combo) {
        combo.removeChild(uItem);
      }
    }

    var type = '';
    if (uItem.getType) type = uItem.getType();

    if (type === 'combo') {
      model.parentId = parentId;
    } else if (type === 'node') {
      model.comboId = parentId;
    } // 只有当移入到指定 combo 时才添加


    if (parentId) {
      var parentCombo = this.findById(parentId);

      if (parentCombo) {
        // 将元素添加到 parentCombo 中
        parentCombo.addChild(uItem);
      }
    } // 如果原先有父亲 combo，则从原父 combo 的子元素数组中删除


    if (oldParentId) {
      var parentCombo = this.findById(oldParentId);

      if (parentCombo) {
        // 将元素从 parentCombo 中移除
        parentCombo.removeChild(uItem);
      }
    }

    var newComboTrees = (0, _graphic.reconstructTree)(this.get('comboTrees'), model.id, parentId);
    this.set('comboTrees', newComboTrees);
    this.updateCombos();
  };
  /**
   * 根据数据渲染群组
   * @param {GraphData} data 渲染图的数据
   * @param {string} groupType group类型
   */


  Graph.prototype.renderCustomGroup = function (data, groupType) {
    var _this = this;

    var groups = data.groups,
        _a = data.nodes,
        nodes = _a === void 0 ? [] : _a; // 第一种情况，，不存在groups，则不存在嵌套群组

    var groupIndex = 10;

    if (!groups) {
      // 存在单个群组
      // 获取所有有groupID的node
      var nodeInGroup = nodes.filter(function (node) {
        return node.groupId;
      });
      var groupsArr_1 = []; // 根据groupID分组

      var groupIds_1 = (0, _lodash.groupBy)(nodeInGroup, 'groupId'); // tslint:disable-next-line:forin

      Object.keys(groupIds_1).forEach(function (groupId) {
        var nodeIds = groupIds_1[groupId].map(function (node) {
          return node.id;
        });

        _this.get('customGroupControll').create(groupId, nodeIds, groupType, groupIndex);

        groupIndex--; // 获取所有不重复的 groupId

        if (!groupsArr_1.find(function (d) {
          return d.id === groupId;
        })) {
          groupsArr_1.push({
            id: groupId
          });
        }
      });
      this.set({
        groups: groupsArr_1
      });
    } else {
      // 将groups的数据存到groups中
      this.set({
        groups: groups
      }); // 第二种情况，存在嵌套的群组，数据中有groups字段

      var groupNodes_1 = (0, _group.getAllNodeInGroups)(data); // tslint:disable-next-line:forin

      Object.keys(groupNodes_1).forEach(function (groupId) {
        var tmpNodes = groupNodes_1[groupId];

        _this.get('customGroupControll').create(groupId, tmpNodes, groupType, groupIndex);

        groupIndex--;
      }); // 对所有Group排序

      var customGroup = this.get('customGroup');
      customGroup.sort();
    }
  };
  /**
   * 导出图数据
   * @return {object} data
   */


  Graph.prototype.save = function () {
    var nodes = [];
    var edges = [];
    var combos = [];
    (0, _each.default)(this.get('nodes'), function (node) {
      nodes.push(node.getModel());
    });
    (0, _each.default)(this.get('edges'), function (edge) {
      edges.push(edge.getModel());
    });
    (0, _each.default)(this.get('combos'), function (combo) {
      combos.push(combo.getModel());
    });
    return {
      nodes: nodes,
      edges: edges,
      combos: combos,
      groups: this.get('groups')
    };
  };
  /**
   * 改变画布大小
   * @param  {number} width  画布宽度
   * @param  {number} height 画布高度
   * @return {object} this
   */


  Graph.prototype.changeSize = function (width, height) {
    var viewController = this.get('viewController');
    viewController.changeSize(width, height);
    return this;
  };
  /**
   * 当源数据在外部发生变更时，根据新数据刷新视图。但是不刷新节点位置
   */


  Graph.prototype.refresh = function () {
    var self = this;
    self.emit('beforegraphrefresh');

    if (self.get('animate')) {
      self.positionsAnimate();
    } else {
      var nodes = self.get('nodes');
      var edges = self.get('edges');
      var vedges = self.get('edges');
      (0, _each.default)(nodes, function (node) {
        node.refresh();
      });
      (0, _each.default)(edges, function (edge) {
        edge.refresh();
      });
      (0, _each.default)(vedges, function (vedge) {
        vedge.refresh();
      });
    }

    self.emit('aftergraphrefresh');
    self.autoPaint();
  };
  /**
   * 获取当前图中所有节点的item实例
   * @return {INode} item数组
   */


  Graph.prototype.getNodes = function () {
    return this.get('nodes');
  };
  /**
   * 获取当前图中所有边的item实例
   * @return {IEdge} item数组
   */


  Graph.prototype.getEdges = function () {
    return this.get('edges');
  };
  /**
   * 获取图中所有的 combo 实例
   */


  Graph.prototype.getCombos = function () {
    return this.get('combos');
  };
  /**
   * 获取指定 Combo 中所有的节点
   * @param comboId combo ID
   */


  Graph.prototype.getComboChildren = function (combo) {
    if ((0, _isString.default)(combo)) {
      combo = this.findById(combo);
    }

    if (!combo || combo.getType && combo.getType() !== 'combo') {
      console.warn('The combo does not exist!');
      return;
    }

    return combo.getChildren();
  };
  /**
   * 根据 graph 上的 animateCfg 进行视图中节点位置动画接口
   */


  Graph.prototype.positionsAnimate = function () {
    var self = this;
    self.emit('beforeanimate');
    var animateCfg = self.get('animateCfg');
    var onFrame = animateCfg.onFrame;
    var nodes = self.getNodes();
    var toNodes = nodes.map(function (node) {
      var model = node.getModel();
      return {
        id: model.id,
        x: model.x,
        y: model.y
      };
    });

    if (self.isAnimating()) {
      self.stopAnimate();
    }

    var canvas = self.get('canvas');
    canvas.animate(function (ratio) {
      (0, _each.default)(toNodes, function (data) {
        var node = self.findById(data.id);

        if (!node || node.destroyed) {
          return;
        }

        var originAttrs = node.get('originAttrs');
        var model = node.get('model');

        if (!originAttrs) {
          var containerMatrix = node.getContainer().getMatrix();
          if (!containerMatrix) containerMatrix = _lib.mat3.create();
          originAttrs = {
            x: containerMatrix[6],
            y: containerMatrix[7]
          };
          node.set('originAttrs', originAttrs);
        }

        if (onFrame) {
          var attrs = onFrame(node, ratio, data, originAttrs);
          node.set('model', Object.assign(model, attrs));
        } else {
          model.x = originAttrs.x + (data.x - originAttrs.x) * ratio;
          model.y = originAttrs.y + (data.y - originAttrs.y) * ratio;
        }
      });
      self.refreshPositions();
    }, {
      duration: animateCfg.duration,
      easing: animateCfg.easing,
      callback: function callback() {
        (0, _each.default)(nodes, function (node) {
          node.set('originAttrs', null);
        });

        if (animateCfg.callback) {
          animateCfg.callback();
        }

        self.emit('afteranimate');
        self.animating = false;
      }
    });
  };
  /**
   * 当节点位置在外部发生改变时，刷新所有节点位置，重计算边
   */


  Graph.prototype.refreshPositions = function () {
    var self = this;
    self.emit('beforegraphrefreshposition');
    var nodes = self.get('nodes');
    var edges = self.get('edges');
    var vedges = self.get('vedges');
    var combos = self.get('combos');
    var model;
    var updatedNodes = {};
    (0, _each.default)(nodes, function (node) {
      model = node.getModel();
      var originAttrs = node.get('originAttrs');

      if (originAttrs && model.x === originAttrs.x && model.y === originAttrs.y) {
        return;
      }

      node.updatePosition({
        x: model.x,
        y: model.y
      });
      updatedNodes[model.id] = true;
      if (model.comboId) updatedNodes[model.comboId] = true;
    });

    if (combos && combos.length !== 0) {
      self.updateCombos();
    }

    (0, _each.default)(edges, function (edge) {
      var sourceModel = edge.getSource().getModel();
      var targetModel = edge.getTarget().getModel();

      if (updatedNodes[sourceModel.id] || updatedNodes[targetModel.id] || edge.getModel().isComboEdge) {
        edge.refresh();
      }
    });
    (0, _each.default)(vedges, function (vedge) {
      vedge.refresh();
    });
    self.emit('aftergraphrefreshposition');
    self.autoPaint();
  };

  Graph.prototype.stopAnimate = function () {
    this.get('canvas').stopAnimate();
  };

  Graph.prototype.isAnimating = function () {
    return this.animating;
  };
  /**
   * 获取当前视口伸缩比例
   * @return {number} 比例
   */


  Graph.prototype.getZoom = function () {
    var matrix = this.get('group').getMatrix();
    return matrix ? matrix[0] : 1;
  };
  /**
   * 获取当前的行为模式
   * @return {string} 当前行为模式
   */


  Graph.prototype.getCurrentMode = function () {
    var modeController = this.get('modeController');
    return modeController.getMode();
  };
  /**
   * 切换行为模式
   * @param {string} mode 指定模式
   * @return {object} this
   */


  Graph.prototype.setMode = function (mode) {
    var modeController = this.get('modeController');
    modeController.setMode(mode);
    return this;
  };
  /**
   * 清除画布元素
   * @return {object} this
   */


  Graph.prototype.clear = function () {
    var canvas = this.get('canvas');
    canvas.clear();
    this.initGroups(); // 清空画布时同时清除数据

    this.set({
      itemMap: {},
      nodes: [],
      edges: [],
      groups: []
    });
    return this;
  };
  /**
   * 返回图表的 dataUrl 用于生成图片
   * @return {string} 图片 dataURL
   */


  Graph.prototype.toDataURL = function (type, backgroundColor) {
    var canvas = this.get('canvas');
    var renderer = canvas.getRenderer();
    var canvasDom = canvas.get('el');
    if (!type) type = 'image/png';
    var dataURL = '';

    if (renderer === 'svg') {
      var clone_1 = canvasDom.cloneNode(true);
      var svgDocType = document.implementation.createDocumentType('svg', '-//W3C//DTD SVG 1.1//EN', 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd');
      var svgDoc = document.implementation.createDocument('http://www.w3.org/2000/svg', 'svg', svgDocType);
      svgDoc.replaceChild(clone_1, svgDoc.documentElement);
      var svgData = new XMLSerializer().serializeToString(svgDoc);
      dataURL = 'data:image/svg+xml;charset=utf8,' + encodeURIComponent(svgData);
    } else {
      var imageData = void 0;
      var context = canvasDom.getContext('2d');
      var width = this.get('width');
      var height = this.get('height');
      var compositeOperation = void 0;

      if (backgroundColor) {
        var pixelRatio = window.devicePixelRatio;
        imageData = context.getImageData(0, 0, width * pixelRatio, height * pixelRatio);
        compositeOperation = context.globalCompositeOperation;
        context.globalCompositeOperation = "destination-over";
        context.fillStyle = backgroundColor;
        context.fillRect(0, 0, width, height);
      }

      dataURL = canvasDom.toDataURL(type);

      if (backgroundColor) {
        context.clearRect(0, 0, width, height);
        context.putImageData(imageData, 0, 0);
        context.globalCompositeOperation = compositeOperation;
      }
    }

    return dataURL;
  };
  /**
   * 导出包含全图的图片
   * @param {String} name 图片的名称
   */


  Graph.prototype.downloadFullImage = function (name, imageConfig) {
    var _this = this;

    var bbox = this.get('group').getCanvasBBox();
    var height = bbox.height;
    var width = bbox.width;
    var renderer = this.get('renderer');
    var vContainerDOM = (0, _createDom.default)('<id="virtual-image"></div>');
    var backgroundColor = imageConfig ? imageConfig.backgroundColor : undefined;
    var padding = imageConfig ? imageConfig.padding : undefined;
    if (!padding) padding = [0, 0, 0, 0];else if ((0, _isNumber.default)(padding)) padding = [padding, padding, padding, padding];
    var vHeight = height + padding[0] + padding[2];
    var vWidth = width + padding[1] + padding[3];
    var canvasOptions = {
      container: vContainerDOM,
      height: vHeight,
      width: vWidth
    };
    var vCanvas = renderer === 'svg' ? new _canvas2.default(canvasOptions) : new _canvas.default(canvasOptions);
    var group = this.get('group');
    var vGroup = group.clone();
    var matrix = (0, _clone.default)(vGroup.getMatrix());
    if (!matrix) matrix = _lib.mat3.create();
    var centerX = (bbox.maxX + bbox.minX) / 2;
    var centerY = (bbox.maxY + bbox.minY) / 2;

    _lib.mat3.translate(matrix, matrix, [-centerX, -centerY]);

    _lib.mat3.translate(matrix, matrix, [width / 2 + padding[3], height / 2 + padding[0]]);

    vGroup.resetMatrix();
    vGroup.setMatrix(matrix);
    vCanvas.add(vGroup);
    var vCanvasEl = vCanvas.get('el');
    setTimeout(function () {
      var type = 'image/png';
      var dataURL = '';

      if (renderer === 'svg') {
        var clone_2 = vCanvasEl.cloneNode(true);
        var svgDocType = document.implementation.createDocumentType('svg', '-//W3C//DTD SVG 1.1//EN', 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd');
        var svgDoc = document.implementation.createDocument('http://www.w3.org/2000/svg', 'svg', svgDocType);
        svgDoc.replaceChild(clone_2, svgDoc.documentElement);
        var svgData = new XMLSerializer().serializeToString(svgDoc);
        dataURL = 'data:image/svg+xml;charset=utf8,' + encodeURIComponent(svgData);
      } else {
        var imageData = void 0;
        var context = vCanvasEl.getContext('2d');
        var compositeOperation = void 0;

        if (backgroundColor) {
          var pixelRatio = window.devicePixelRatio;
          imageData = context.getImageData(0, 0, vWidth * pixelRatio, vHeight * pixelRatio);
          compositeOperation = context.globalCompositeOperation;
          context.globalCompositeOperation = "destination-over";
          context.fillStyle = backgroundColor;
          context.fillRect(0, 0, vWidth, vHeight);
        }

        dataURL = vCanvasEl.toDataURL(type);

        if (backgroundColor) {
          context.clearRect(0, 0, vWidth, vHeight);
          context.putImageData(imageData, 0, 0);
          context.globalCompositeOperation = compositeOperation;
        }
      }

      var link = document.createElement('a');
      var fileName = (name || 'graph') + (renderer === 'svg' ? '.svg' : '.png');

      _this.dataURLToImage(dataURL, renderer, link, fileName);

      var e = document.createEvent('MouseEvents');
      e.initEvent('click', false, false);
      link.dispatchEvent(e);
    }, 16);
  };
  /**
   * 画布导出图片，图片仅包含画布可见区域部分内容
   * @param {String} name 图片的名称
   */


  Graph.prototype.downloadImage = function (name, backgroundColor) {
    var _this = this;

    var self = this;

    if (self.isAnimating()) {
      self.stopAnimate();
    }

    var canvas = self.get('canvas');
    var renderer = canvas.getRenderer();
    var fileName = (name || 'graph') + (renderer === 'svg' ? '.svg' : '.png');
    var link = document.createElement('a');
    setTimeout(function () {
      var dataURL = self.toDataURL('image/png', backgroundColor);

      _this.dataURLToImage(dataURL, renderer, link, fileName);

      var e = document.createEvent('MouseEvents');
      e.initEvent('click', false, false);
      link.dispatchEvent(e);
    }, 16);
  };

  Graph.prototype.dataURLToImage = function (dataURL, renderer, link, fileName) {
    if (typeof window !== 'undefined') {
      if (window.Blob && window.URL && renderer !== 'svg') {
        var arr = dataURL.split(',');
        var mime = '';

        if (arr && arr.length > 0) {
          var match = arr[0].match(/:(.*?);/); // eslint-disable-next-line prefer-destructuring

          if (match && match.length >= 2) mime = match[1];
        }

        var bstr = atob(arr[1]);
        var n = bstr.length;
        var u8arr = new Uint8Array(n);

        while (n--) {
          u8arr[n] = bstr.charCodeAt(n);
        }

        var blobObj_1 = new Blob([u8arr], {
          type: mime
        });

        if (window.navigator.msSaveBlob) {
          window.navigator.msSaveBlob(blobObj_1, fileName);
        } else {
          link.addEventListener('click', function () {
            link.download = fileName;
            link.href = window.URL.createObjectURL(blobObj_1);
          });
        }
      } else {
        link.addEventListener('click', function () {
          link.download = fileName;
          link.href = dataURL;
        });
      }
    }
  };
  /**
   * 更换布局配置项
   * @param {object} cfg 新布局配置项
   * 若 cfg 含有 type 字段或为 String 类型，且与现有布局方法不同，则更换布局
   * 若 cfg 不包括 type ，则保持原有布局方法，仅更新布局配置项
   */


  Graph.prototype.updateLayout = function (cfg) {
    var layoutController = this.get('layoutController');
    var newLayoutType;

    if ((0, _isString.default)(cfg)) {
      newLayoutType = cfg;
      cfg = {
        type: newLayoutType
      };
    } else {
      newLayoutType = cfg.type;
    }

    var oriLayoutCfg = this.get('layout');
    var oriLayoutType = oriLayoutCfg ? oriLayoutCfg.type : undefined;

    if (!newLayoutType || oriLayoutType === newLayoutType) {
      // no type or same type, update layout
      var layoutCfg = {};
      Object.assign(layoutCfg, oriLayoutCfg, cfg);
      layoutCfg.type = oriLayoutType || 'random';
      this.set('layout', layoutCfg);
      layoutController.updateLayoutCfg(layoutCfg);
    } else {
      // has different type, change layout
      this.set('layout', cfg);
      layoutController.changeLayout(newLayoutType);
    }
  };
  /**
   * 重新以当前示例中配置的属性进行一次布局
   */


  Graph.prototype.layout = function () {
    var layoutController = this.get('layoutController');
    var layoutCfg = this.get('layout');
    if (!layoutCfg) return;

    if (layoutCfg.workerEnabled) {
      // 如果使用web worker布局
      layoutController.layout();
      return;
    }

    if (layoutController.layoutMethod) {
      layoutController.relayout(true);
    } else {
      layoutController.layout();
    }
  };
  /**
   * 收起指定的 combo
   * @param {string | ICombo} combo combo ID 或 combo item
   */


  Graph.prototype.collapseCombo = function (combo) {
    var _this = this;

    if ((0, _isString.default)(combo)) {
      combo = this.findById(combo);
    }

    if (!combo) {
      console.warn('The combo to be collapsed does not exist!');
      return;
    }

    var comboModel = combo.getModel(); // add virtual edges

    var edges = this.getEdges().concat(this.get('vedges')); // find all the descendant nodes and combos

    var cnodes = [];
    var ccombos = [];
    var comboTrees = this.get('comboTrees');
    var found = false;
    var brothers = {};
    comboTrees && comboTrees.forEach(function (ctree) {
      brothers[ctree.id] = ctree;
    });
    comboTrees && comboTrees.forEach(function (ctree) {
      if (found) return; // if the combo is found, terminate the forEach

      (0, _graphic.traverseTree)(ctree, function (subTree) {
        // if the combo is found and the it is traversing the other brothers, terminate
        if (found && brothers[subTree.id]) return false;

        if (comboModel.parentId === subTree.id) {
          // if the parent is found, store the brothers
          brothers = {};
          subTree.children.forEach(function (child) {
            brothers[child.id] = child;
          });
        } else if (comboModel.id === subTree.id) {
          // if the combo is found
          found = true;
        }

        if (found) {
          // if the combo is found, concat the descendant nodes and combos
          var item = _this.findById(subTree.id);

          if (item && item.getType && item.getType() === 'combo') {
            cnodes = cnodes.concat(item.getNodes());
            ccombos = ccombos.concat(item.getCombos());
          }
        }

        return true;
      });
    });
    var edgeWeightMap = {};
    var addedVEdges = [];
    edges.forEach(function (edge) {
      var source = edge.getSource();
      var target = edge.getTarget();

      if ((cnodes.includes(source) || ccombos.includes(source)) && !cnodes.includes(target) && !ccombos.includes(target) || source.getModel().id === comboModel.id) {
        var edgeModel = edge.getModel();

        if (edgeModel.isVEdge) {
          _this.removeItem(edge);

          return;
        }

        var targetModel = target.getModel();

        while (!target.isVisible()) {
          target = _this.findById(targetModel.parentId || targetModel.comboId);
          if (!target || !targetModel.parentId && !targetModel.comboId) return; // all the ancestors are hidden, then ignore the edge

          targetModel = target.getModel();
        }

        var targetId = targetModel.id;

        if (edgeWeightMap[comboModel.id + "-" + targetId]) {
          edgeWeightMap[comboModel.id + "-" + targetId] += edgeModel.size || 1;
          return;
        } // the source is in the combo, the target is not


        var vedge = _this.addItem('vedge', {
          source: comboModel.id,
          target: targetId,
          isVEdge: true
        });

        edgeWeightMap[comboModel.id + "-" + targetId] = edgeModel.size || 1;
        addedVEdges.push(vedge);
      } else if (!cnodes.includes(source) && !ccombos.includes(source) && (cnodes.includes(target) || ccombos.includes(target)) || target.getModel().id === comboModel.id) {
        var edgeModel = edge.getModel();

        if (edgeModel.isVEdge) {
          _this.removeItem(edge);

          return;
        }

        var sourceModel = source.getModel();

        while (!source.isVisible()) {
          source = _this.findById(sourceModel.parentId || sourceModel.comboId);
          if (!source || !sourceModel.parentId && !sourceModel.comboId) return; // all the ancestors are hidden, then ignore the edge

          sourceModel = source.getModel();
        }

        var sourceId = sourceModel.id;

        if (edgeWeightMap[sourceId + "-" + comboModel.id]) {
          edgeWeightMap[sourceId + "-" + comboModel.id] += edgeModel.size || 1;
          return;
        } // the target is in the combo, the source is not


        var vedge = _this.addItem('vedge', {
          target: comboModel.id,
          source: sourceId,
          isVEdge: true
        });

        edgeWeightMap[sourceId + "-" + comboModel.id] = edgeModel.size || 1;
        addedVEdges.push(vedge);
      }
    }); // update the width of the virtual edges, which is the sum of merged actual edges
    // be attention that the actual edges with same endpoints but different directions will be represented by two different virtual edges

    addedVEdges.forEach(function (vedge) {
      var vedgeModel = vedge.getModel();

      _this.updateItem(vedge, {
        size: edgeWeightMap[vedgeModel.source + "-" + vedgeModel.target]
      });
    });
    var itemController = this.get('itemController');
    itemController.collapseCombo(combo);
    comboModel.collapsed = true;
  };
  /**
   * 展开指定的 combo
   * @param {string | ICombo} combo combo ID 或 combo item
   */


  Graph.prototype.expandCombo = function (combo) {
    var _this = this;

    if ((0, _isString.default)(combo)) {
      combo = this.findById(combo);
    }

    if (!combo || combo.getType && combo.getType() !== 'combo') {
      console.warn('The combo to be collapsed does not exist!');
      return;
    }

    var comboModel = combo.getModel();
    var itemController = this.get('itemController');
    itemController.expandCombo(combo);
    comboModel.collapsed = false; // add virtual edges

    var edges = this.getEdges().concat(this.get('vedges')); // find all the descendant nodes and combos

    var cnodes = [];
    var ccombos = [];
    var comboTrees = this.get('comboTrees');
    var found = false;
    var brothers = {};
    comboTrees && comboTrees.forEach(function (ctree) {
      brothers[ctree.id] = ctree;
    });
    comboTrees && comboTrees.forEach(function (ctree) {
      if (found) return; // if the combo is found, terminate

      (0, _graphic.traverseTree)(ctree, function (subTree) {
        if (found && brothers[subTree.id]) {
          return false;
        }

        if (comboModel.parentId === subTree.id) {
          brothers = {};
          subTree.children.forEach(function (child) {
            brothers[child.id] = child;
          });
        } else if (comboModel.id === subTree.id) {
          found = true;
        }

        if (found) {
          var item = _this.findById(subTree.id);

          if (item && item.getType && item.getType() === 'combo') {
            cnodes = cnodes.concat(item.getNodes());
            ccombos = ccombos.concat(item.getCombos());
          }
        }

        return true;
      });
    });
    var edgeWeightMap = {};
    var addedVEdges = {};
    edges.forEach(function (edge) {
      var source = edge.getSource();
      var target = edge.getTarget();
      var sourceId = source.get('id');
      var targetId = target.get('id');

      if ((cnodes.includes(source) || ccombos.includes(source)) && !cnodes.includes(target) && !ccombos.includes(target) || sourceId === comboModel.id) {
        // the source is in the combo, the target is not
        // ignore the virtual edges
        if (edge.getModel().isVEdge) {
          _this.removeItem(edge);

          return;
        }

        var targetModel = target.getModel(); // find the nearest visible ancestor

        while (!target.isVisible()) {
          target = _this.findById(targetModel.comboId || targetModel.parentId);

          if (!target || !targetModel.parentId && !targetModel.comboId) {
            return; // if all the ancestors of the oppsite are all hidden, ignore the edge
          }

          targetModel = target.getModel();
        }

        targetId = targetModel.id;
        var sourceModel = source.getModel(); // find the nearest visible ancestor

        while (!source.isVisible()) {
          source = _this.findById(sourceModel.comboId || sourceModel.parentId);

          if (!source || !sourceModel.parentId && !sourceModel.comboId) {
            return; // if all the ancestors of the oppsite are all hidden, ignore the edge
          }

          if (sourceModel.comboId === comboModel.id || sourceModel.parentId === comboModel.id) {
            break; // if the next ancestor is the combo, break the while
          }

          sourceModel = source.getModel();
        }

        sourceId = sourceModel.id;

        if (targetId) {
          var vedgeId = sourceId + "-" + targetId; // update the width of the virtual edges, which is the sum of merged actual edges
          // be attention that the actual edges with same endpoints but different directions will be represented by two different virtual edges

          if (edgeWeightMap[vedgeId]) {
            edgeWeightMap[vedgeId] += edge.getModel().size || 1;

            _this.updateItem(addedVEdges[vedgeId], {
              size: edgeWeightMap[vedgeId]
            });

            return;
          }

          var vedge = _this.addItem('vedge', {
            source: sourceId,
            target: targetId,
            isVEdge: true
          });

          edgeWeightMap[vedgeId] = edge.getModel().size || 1;
          addedVEdges[vedgeId] = vedge;
        }
      } else if (!cnodes.includes(source) && !ccombos.includes(source) && (cnodes.includes(target) || ccombos.includes(target)) || targetId === comboModel.id) {
        // the target is in the combo, the source is not
        // ignore the virtual edges
        if (edge.getModel().isVEdge) {
          _this.removeItem(edge);

          return;
        }

        var sourceModel = source.getModel(); // find the nearest visible ancestor

        while (!source.isVisible()) {
          source = _this.findById(sourceModel.comboId || sourceModel.parentId);

          if (!source || !sourceModel.parentId && !sourceModel.comboId) {
            return; // if all the ancestors of the oppsite are all hidden, ignore the edge
          }

          sourceModel = source.getModel();
        }

        sourceId = sourceModel.id;
        var targetModel = target.getModel(); // find the nearest visible ancestor

        while (!target.isVisible()) {
          target = _this.findById(targetModel.comboId || targetModel.parentId);

          if (!target || !targetModel.parentId && !targetModel.comboId) {
            return; // if all the ancestors of the oppsite are all hidden, ignore the edge
          }

          if (targetModel.comboId === comboModel.id || targetModel.parentId === comboModel.id) {
            break; // if the next ancestor is the combo, break the while
          }

          targetModel = target.getModel();
        }

        targetId = targetModel.id;

        if (sourceId) {
          var vedgeId = sourceId + "-" + targetId; // update the width of the virtual edges, which is the sum of merged actual edges
          // be attention that the actual edges with same endpoints but different directions will be represented by two different virtual edges

          if (edgeWeightMap[vedgeId]) {
            edgeWeightMap[vedgeId] += edge.getModel().size || 1;

            _this.updateItem(addedVEdges[vedgeId], {
              size: edgeWeightMap[vedgeId]
            });

            return;
          }

          var vedge = _this.addItem('vedge', {
            target: targetId,
            source: sourceId,
            isVEdge: true
          });

          edgeWeightMap[vedgeId] = edge.getModel().size || 1;
          addedVEdges[vedgeId] = vedge;
        }
      }
    });
  };

  Graph.prototype.collapseExpandCombo = function (combo) {
    if ((0, _isString.default)(combo)) {
      combo = this.findById(combo);
    }

    if (combo.getType && combo.getType() !== 'combo') return;
    var comboModel = combo.getModel(); // if one ancestor combo of the combo is collapsed, it should not be collapsed or expanded

    var parentItem = this.findById(comboModel.parentId);

    while (parentItem) {
      var parentModel = parentItem.getModel();

      if (parentModel.collapsed) {
        console.warn("Fail to expand the combo since it's ancestor combo is collapsed.");
        parentItem = undefined;
        return;
      }

      parentItem = this.findById(parentModel.parentId);
    }

    var collapsed = comboModel.collapsed; // 该群组已经处于收起状态，需要展开

    if (collapsed) {
      this.expandCombo(combo);
    } else {
      this.collapseCombo(combo);
    }
  };
  /**
   * 收起分组
   * @param {string} groupId 分组ID
   */


  Graph.prototype.collapseGroup = function (groupId) {
    var customGroupControll = this.get('customGroupControll');
    customGroupControll.collapseGroup(groupId);
  };
  /**
   * 展开分组
   * @param {string} groupId 分组ID
   */


  Graph.prototype.expandGroup = function (groupId) {
    var customGroupControll = this.get('customGroupControll');
    customGroupControll.expandGroup(groupId);
  };
  /**
   * 添加插件
   * @param {object} plugin 插件实例
   */


  Graph.prototype.addPlugin = function (plugin) {
    var self = this;

    if (plugin.destroyed) {
      return;
    }

    self.get('plugins').push(plugin);
    plugin.initPlugin(self);
  };
  /**
   * 添加插件
   * @param {object} plugin 插件实例
   */


  Graph.prototype.removePlugin = function (plugin) {
    var plugins = this.get('plugins');
    var index = plugins.indexOf(plugin);

    if (index >= 0) {
      plugin.destroyPlugin();
      plugins.splice(index, 1);
    }
  };
  /**
   * 根据 comboTree 结构整理 Combo 相关的图形绘制层级，包括 Combo 本身、节点、边
   * @param {GraphData} data 数据
   */


  Graph.prototype.sortCombos = function () {
    var _this = this;

    var depthMap = [];
    var dataDepthMap = {};
    var comboTrees = this.get('comboTrees');
    comboTrees && comboTrees.forEach(function (cTree) {
      (0, _graphic.traverseTree)(cTree, function (child) {
        if (depthMap[child.depth]) depthMap[child.depth].push(child.id);else depthMap[child.depth] = [child.id];
        dataDepthMap[child.id] = child.depth;
        return true;
      });
    });
    var edges = this.getEdges().concat(this.get('vedges'));
    edges && edges.forEach(function (edgeItem) {
      var edge = edgeItem.getModel();
      var sourceDepth = dataDepthMap[edge.source] || 0;
      var targetDepth = dataDepthMap[edge.target] || 0;
      var depth = Math.max(sourceDepth, targetDepth);
      if (depthMap[depth]) depthMap[depth].push(edge.id);else depthMap[depth] = [edge.id];
    });
    depthMap.forEach(function (array) {
      if (!array || !array.length) return;

      for (var i = array.length - 1; i >= 0; i--) {
        var item = _this.findById(array[i]);

        item && item.toFront();
      }
    });
  };
  /**
   * 获取节点所有的邻居节点
   *
   * @param {(string | INode)} node 节点 ID 或实例
   * @returns {INode[]}
   * @memberof IGraph
   */


  Graph.prototype.getNeighbors = function (node) {
    var item = node;

    if ((0, _isString.default)(node)) {
      item = this.findById(node);
    }

    return item.getNeighbors();
  };
  /**
   * 获取以 node 为起点的所有邻居节点
   *
   * @param {(string | INode)} node 节点 ID 或实例
   * @returns {INode[]}
   * @memberof IGraph
   */


  Graph.prototype.getSourceNeighbors = function (node) {
    var item = node;

    if ((0, _isString.default)(node)) {
      item = this.findById(node);
    }

    return item.getSourceNeighbors();
  };
  /**
   * 获取以 node 为终点的所有邻居节点
   *
   * @param {(string | INode)} node 节点 ID 或实例
   * @returns {INode[]}
   * @memberof IGraph
   */


  Graph.prototype.getTargetNeighbors = function (node) {
    var item = node;

    if ((0, _isString.default)(node)) {
      item = this.findById(node);
    }

    return item.getTargetNeighbors();
  };
  /**
   * 获取 node 的度数
   *
   * @param {(string | INode)} node 节点 ID 或实例
   * @param {('in' | 'out' | 'total' | 'all' | undefined)} 度数类型，in 入度，out 出度，total 总度数，all 返回三种类型度数的对象
   * @returns {Number | Object} 该节点的度数
   * @memberof IGraph
   */


  Graph.prototype.getNodeDegree = function (node, type) {
    if (type === void 0) {
      type = undefined;
    }

    var item = node;

    if ((0, _isString.default)(node)) {
      item = this.findById(node);
    }

    var degrees = this.get('degrees');

    if (!degrees) {
      degrees = (0, _degree.default)(this);
    }

    this.set('degees', degrees);
    var nodeDegrees = degrees[item.getID()];
    var res;

    switch (type) {
      case 'in':
        res = nodeDegrees.inDegree;
        break;

      case 'out':
        res = nodeDegrees.outDegree;
        break;

      case 'all':
        res = nodeDegrees;
        break;

      default:
        res = nodeDegrees.degree;
        break;
    }

    return res;
  };
  /**
   * 销毁画布
   */


  Graph.prototype.destroy = function () {
    this.clear();
    (0, _each.default)(this.get('plugins'), function (plugin) {
      plugin.destroyPlugin();
    });
    this.get('eventController').destroy();
    this.get('itemController').destroy();
    this.get('modeController').destroy();
    this.get('viewController').destroy();
    this.get('stateController').destroy();
    this.get('layoutController').destroy();
    this.get('customGroupControll').destroy();
    this.get('canvas').destroy();
    this.cfg = null;
    this.destroyed = true;
  };

  return Graph;
}(_eventEmitter.default);

var _default = Graph;
exports.default = _default;