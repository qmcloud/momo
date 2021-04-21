import { each, isNil, isPlainObject, isString, isBoolean, uniqueId, mix, deepMix } from '@antv/util';
import Shape from '../shape/shape';
import { getBBox } from '../util/graphic';
import { translate } from '../util/math';
var CACHE_BBOX = 'bboxCache';
var CACHE_CANVAS_BBOX = 'bboxCanvasCache';
var RESERVED_STYLES = ['fillStyle', 'strokeStyle', 'path', 'points', 'img', 'symbol'];

var ItemBase =
/** @class */
function () {
  function ItemBase(cfg) {
    this._cfg = {};
    this.destroyed = false;
    var defaultCfg = {
      /**
       * id
       * @type {string}
       */
      id: undefined,

      /**
       * 类型
       * @type {string}
       */
      type: 'item',

      /**
       * data model
       * @type {object}
       */
      model: {},

      /**
       * g group
       * @type {G.Group}
       */
      group: undefined,

      /**
       * is open animate
       * @type {boolean}
       */
      animate: false,

      /**
       * visible - not group visible
       * @type {boolean}
       */
      visible: true,

      /**
       * locked - lock node
       * @type {boolean}
       */
      locked: false,

      /**
       * capture event
       * @type {boolean}
       */
      event: true,

      /**
       * key shape to calculate item's bbox
       * @type object
       */
      keyShape: undefined,

      /**
       * item's states, such as selected or active
       * @type Array
       */
      states: []
    };
    this._cfg = Object.assign(defaultCfg, this.getDefaultCfg(), cfg);
    var id = this.get('model').id;

    if (!id) {
      id = uniqueId(this.get('type'));
      this.get('model').id = id;
    }

    this.set('id', id);
    var group = cfg.group;

    if (group) {
      group.set('item', this);
      group.set('id', id);
    }

    this.init();
    this.draw();
  }
  /**
   * 根据 keyshape 计算包围盒
   */


  ItemBase.prototype.calculateBBox = function () {
    var keyShape = this.get('keyShape');
    var group = this.get('group'); // 因为 group 可能会移动，所以必须通过父元素计算才能计算出正确的包围盒

    var bbox = getBBox(keyShape, group);
    bbox.x = bbox.minX;
    bbox.y = bbox.minY;
    bbox.width = bbox.maxX - bbox.minX;
    bbox.height = bbox.maxY - bbox.minY;
    bbox.centerX = (bbox.minX + bbox.maxX) / 2;
    bbox.centerY = (bbox.minY + bbox.maxY) / 2;
    return bbox;
  };
  /**
   * 根据 keyshape 计算包围盒
   */


  ItemBase.prototype.calculateCanvasBBox = function () {
    var keyShape = this.get('keyShape');
    var group = this.get('group'); // 因为 group 可能会移动，所以必须通过父元素计算才能计算出正确的包围盒

    var bbox = getBBox(keyShape, group);
    bbox.x = bbox.minX;
    bbox.y = bbox.minY;
    bbox.width = bbox.maxX - bbox.minX;
    bbox.height = bbox.maxY - bbox.minY;
    bbox.centerX = (bbox.minX + bbox.maxX) / 2;
    bbox.centerY = (bbox.minY + bbox.maxY) / 2;
    return bbox;
  };
  /**
   * draw shape
   */


  ItemBase.prototype.drawInner = function () {
    var self = this;
    var shapeFactory = self.get('shapeFactory');
    var group = self.get('group');
    var model = self.get('model');
    group.clear();

    if (!shapeFactory) {
      return;
    }

    self.updatePosition(model);
    var cfg = self.getShapeCfg(model); // 可能会附加额外信息

    var shapeType = cfg.shape || cfg.type;
    var keyShape = shapeFactory.draw(shapeType, cfg, group);

    if (keyShape) {
      self.set('keyShape', keyShape);
      keyShape.set('isKeyShape', true);
      keyShape.set('draggable', true);
    }

    this.setOriginStyle(); // 防止由于用户外部修改 model 中的 shape 导致 shape 不更新

    this.set('currentShape', shapeType);
    this.restoreStates(shapeFactory, shapeType);
  };
  /**
   * 设置图元素原始样式
   * @param keyShape 图元素 keyShape
   * @param group Group 容器
   */


  ItemBase.prototype.setOriginStyle = function (cfg) {
    var originStyles = {};
    var group = this.get('group');
    var children = group.get('children');
    var keyShape = this.getKeyShape();
    var self = this;
    each(children, function (child) {
      var name = child.get('name');

      if (name) {
        originStyles[name] = self.getShapeStyleByName(name);
      } else {
        var keyShapeName = keyShape.get('name');
        var keyShapeStyle = self.getShapeStyleByName();

        if (!keyShapeName) {
          Object.assign(originStyles, keyShapeStyle);
        } else {
          originStyles[keyShapeName] = keyShapeStyle;
        }
      }
    });
    var drawOriginStyle = this.getOriginStyle();
    var styles = {};

    if (cfg) {
      styles = deepMix({}, drawOriginStyle, originStyles, cfg.style, {
        labelCfg: cfg.labelCfg
      });
    } else {
      styles = deepMix({}, drawOriginStyle, originStyles);
    }

    self.set('originStyle', styles);
  };
  /**
   * restore shape states
   * @param shapeFactory
   * @param shapeType
   */


  ItemBase.prototype.restoreStates = function (shapeFactory, shapeType) {
    var self = this;
    var states = self.get('states');
    each(states, function (state) {
      shapeFactory.setState(shapeType, state, true, self);
    });
  };

  ItemBase.prototype.init = function () {
    var shapeFactory = Shape.getFactory(this.get('type'));
    this.set('shapeFactory', shapeFactory);
  };
  /**
   * 获取属性
   * @internal 仅内部类使用
   * @param  {String} key 属性名
   * @return {object | string | number} 属性值
   */


  ItemBase.prototype.get = function (key) {
    return this._cfg[key];
  };
  /**
   * 设置属性
   * @internal 仅内部类使用
   * @param {String|Object} key 属性名，也可以是对象
   * @param {object | string | number} val 属性值
   */


  ItemBase.prototype.set = function (key, val) {
    if (isPlainObject(key)) {
      this._cfg = Object.assign({}, this._cfg, key);
    } else {
      this._cfg[key] = val;
    }
  };

  ItemBase.prototype.getDefaultCfg = function () {
    return {};
  };
  /**
   * 更新/刷新等操作后，清除 cache
   */


  ItemBase.prototype.clearCache = function () {
    this.set(CACHE_BBOX, null);
    this.set(CACHE_CANVAS_BBOX, null);
  };
  /**
   * 渲染前的逻辑，提供给子类复写
   */


  ItemBase.prototype.beforeDraw = function () {};
  /**
   * 渲染后的逻辑，提供给子类复写
   */


  ItemBase.prototype.afterDraw = function () {};
  /**
   * 更新后做一些工作
   */


  ItemBase.prototype.afterUpdate = function () {};
  /**
   * draw shape
   */


  ItemBase.prototype.draw = function () {
    this.beforeDraw();
    this.drawInner();
    this.afterDraw();
  };

  ItemBase.prototype.getShapeStyleByName = function (name) {
    var _this = this;

    var group = this.get('group');
    var currentShape = this.getKeyShape();

    if (name) {
      currentShape = group.find(function (element) {
        return element.get('name') === name;
      });
    }

    if (currentShape) {
      var styles_1 = {}; // 这里要排除掉所有 states 中样式

      var states = this.get('states');
      states.map(function (state) {
        var style = _this.getStateStyle(state);

        for (var key in style) {
          if (!isPlainObject(style[key])) {
            if (!RESERVED_STYLES.includes(key)) {
              RESERVED_STYLES.push(key);
            }
          } else {
            var subStyle = style[key];

            for (var subKey in subStyle) {
              if (!RESERVED_STYLES.includes(subKey)) {
                RESERVED_STYLES.push(subKey);
              }
            }
          }
        }
      });
      each(currentShape.attr(), function (val, key) {
        if (RESERVED_STYLES.indexOf(key) < 0) {
          styles_1[key] = val;
        }
      });
      return styles_1;
    }

    return {};
  };

  ItemBase.prototype.getShapeCfg = function (model) {
    var styles = this.get('styles');

    if (styles) {
      // merge graph的item样式与数据模型中的样式
      var newModel = model;
      newModel.style = Object.assign({}, styles, model.style);
      return newModel;
    }

    return model;
  };
  /**
   * 获取指定状态的样式，去除了全局样式
   * @param state 状态名称
   */


  ItemBase.prototype.getStateStyle = function (state) {
    var styles = this.get('styles');
    var stateStyle = styles && styles[state];
    return stateStyle;
  };
  /**
   * get keyshape style
   */


  ItemBase.prototype.getOriginStyle = function () {
    return this.get('originStyle');
  };

  ItemBase.prototype.getCurrentStatesStyle = function () {
    var self = this;
    var styles = {};
    each(self.getStates(), function (state) {
      Object.assign(styles, self.getStateStyle(state));
    });
    return styles;
  };
  /**
   * 更改元素状态， visible 不属于这个范畴
   * @internal 仅提供内部类 graph 使用
   * @param {String} state 状态名
   * @param {Boolean} value 节点状态值
   */


  ItemBase.prototype.setState = function (state, value) {
    var states = this.get('states');
    var shapeFactory = this.get('shapeFactory');
    var stateName = state;
    var filterStateName = state;

    if (isString(value)) {
      stateName = state + ":" + value;
      filterStateName = state + ":";
    }

    var newStates = states;

    if (isBoolean(value)) {
      var index = states.indexOf(filterStateName);

      if (value) {
        if (index > -1) {
          return;
        }

        states.push(stateName);
      } else if (index > -1) {
        states.splice(index, 1);
      }
    } else if (isString(value)) {
      // 过滤掉 states 中 filterStateName 相关的状态
      var filterStates = states.filter(function (name) {
        return name.includes(filterStateName);
      });

      if (filterStates.length > 0) {
        this.clearStates(filterStates);
      }

      newStates = newStates.filter(function (name) {
        return !name.includes(filterStateName);
      });
      newStates.push(stateName);
      this.set('states', newStates);
    }

    if (shapeFactory) {
      var model = this.get('model');
      var type = model.shape || model.type; // 调用 shape/shape.ts 中的 setState

      shapeFactory.setState(type, state, value, this);
    }
  };
  /**
   * 清除指定的状态，如果参数为空，则不做任务处理
   * @param states 状态名称
   */


  ItemBase.prototype.clearStates = function (states) {
    var self = this;
    var originStates = self.getStates();
    var shapeFactory = self.get('shapeFactory');
    var model = self.get('model');
    var shape = model.shape || model.type;

    if (!states) {
      states = originStates;
    }

    if (isString(states)) {
      states = [states];
    }

    var newStates = originStates.filter(function (state) {
      return states.indexOf(state) === -1;
    });
    self.set('states', newStates);
    states.forEach(function (state) {
      shapeFactory.setState(shape, state, false, self);
    });
  };
  /**
   * 节点的图形容器
   * @return {G.Group} 图形容器
   */


  ItemBase.prototype.getContainer = function () {
    return this.get('group');
  };
  /**
   * 节点的关键形状，用于计算节点大小，连线截距等
   * @return {IShapeBase} 关键形状
   */


  ItemBase.prototype.getKeyShape = function () {
    return this.get('keyShape');
  };
  /**
   * 节点数据模型
   * @return {Object} 数据模型
   */


  ItemBase.prototype.getModel = function () {
    return this.get('model');
  };
  /**
   * 节点类型
   * @return {string} 节点的类型
   */


  ItemBase.prototype.getType = function () {
    return this.get('type');
  };
  /**
   * 获取 Item 的ID
   */


  ItemBase.prototype.getID = function () {
    return this.get('id');
  };
  /**
   * 是否是 Item 对象，悬空边情况下进行判定
   */


  ItemBase.prototype.isItem = function () {
    return true;
  };
  /**
   * 获取当前元素的所有状态
   * @return {Array} 元素的所有状态
   */


  ItemBase.prototype.getStates = function () {
    return this.get('states');
  };
  /**
   * 当前元素是否处于某状态
   * @param {String} state 状态名
   * @return {Boolean} 是否处于某状态
   */


  ItemBase.prototype.hasState = function (state) {
    var states = this.getStates();
    return states.indexOf(state) >= 0;
  };
  /**
   * 刷新一般用于处理几种情况
   * 1. item model 在外部被改变
   * 2. 边的节点位置发生改变，需要重新计算边
   *
   * 因为数据从外部被修改无法判断一些属性是否被修改，直接走位置和 shape 的更新
   */


  ItemBase.prototype.refresh = function () {
    var model = this.get('model'); // 更新元素位置

    this.updatePosition(model); // 更新元素内容，样式

    this.updateShape(); // 做一些更新之后的操作

    this.afterUpdate(); // 清除缓存

    this.clearCache();
  };

  ItemBase.prototype.isOnlyMove = function (cfg) {
    return false;
  };
  /**
   * 将更新应用到 model 上，刷新属性
   * @internal 仅提供给 Graph 使用，外部直接调用 graph.update 接口
   * @param  {Object} cfg       配置项，可以是增量信息
   */


  ItemBase.prototype.update = function (cfg) {
    var model = this.get('model');
    var originPosition = {
      x: model.x,
      y: model.y
    };
    cfg.x = isNaN(cfg.x) ? model.x : cfg.x;
    cfg.y = isNaN(cfg.y) ? model.y : cfg.y;
    var styles = this.get('styles');

    if (cfg.stateStyles) {
      // 更新 item 时更新 this.get('styles') 中的值
      var stateStyles = cfg.stateStyles;
      mix(styles, stateStyles);
      delete cfg.stateStyles;
    } // 直接将更新合到原数据模型上，可以保证用户在外部修改源数据然后刷新时的样式符合期待。


    Object.assign(model, cfg); // isOnlyMove 仅用于node

    var onlyMove = this.isOnlyMove(cfg); // 仅仅移动位置时，既不更新，也不重绘

    if (onlyMove) {
      this.updatePosition(cfg);
    } else {
      // 如果 x,y 有变化，先重置位置
      if (originPosition.x !== cfg.x || originPosition.y !== cfg.y) {
        this.updatePosition(cfg);
      }

      this.updateShape();
    }

    this.afterUpdate();
    this.clearCache();
  };
  /**
   * 更新元素内容，样式
   */


  ItemBase.prototype.updateShape = function () {
    var shapeFactory = this.get('shapeFactory');
    var model = this.get('model');
    var shape = model.shape || model.type; // 判定是否允许更新
    // 1. 注册的节点允许更新
    // 2. 更新后的 shape 等于原先的 shape

    if (shapeFactory.shouldUpdate(shape) && shape === this.get('currentShape')) {
      var updateCfg = this.getShapeCfg(model);
      shapeFactory.baseUpdate(shape, updateCfg, this);
    } else {
      // 如果不满足上面两种状态，重新绘制
      this.draw();
    } // 更新完以后重新设置原始样式


    this.setOriginStyle(model); // 更新后重置节点状态

    this.restoreStates(shapeFactory, shape);
  };
  /**
   * 更新位置，避免整体重绘
   * @param {object} cfg 待更新数据
   */


  ItemBase.prototype.updatePosition = function (cfg) {
    var model = this.get('model');
    var x = isNil(cfg.x) ? model.x : cfg.x;
    var y = isNil(cfg.y) ? model.y : cfg.y;
    var group = this.get('group');

    if (isNil(x) || isNil(y)) {
      return;
    }

    group.resetMatrix(); // G 4.0 element 中移除了矩阵相关方法，详见https://www.yuque.com/antv/blog/kxzk9g#4rMMV

    translate(group, {
      x: x,
      y: y
    });
    model.x = x;
    model.y = y;
    this.clearCache(); // 位置更新后需要清除缓存
  };
  /**
   * 获取 item 的包围盒，这个包围盒是相对于 item 自己，不会将 matrix 计算在内
   * @return {Object} 包含 x,y,width,height, centerX, centerY
   */


  ItemBase.prototype.getBBox = function () {
    // 计算 bbox 开销有些大，缓存
    var bbox = this.get(CACHE_BBOX);

    if (!bbox) {
      bbox = this.calculateBBox();
      this.set(CACHE_BBOX, bbox);
    }

    return bbox;
  };
  /**
   * 获取 item 相对于画布的包围盒，会将从顶层到当前元素的 matrix 都计算在内
   * @return {Object} 包含 x,y,width,height, centerX, centerY
   */


  ItemBase.prototype.getCanvasBBox = function () {
    // 计算 bbox 开销有些大，缓存
    var bbox = this.get(CACHE_CANVAS_BBOX);

    if (!bbox) {
      bbox = this.calculateCanvasBBox();
      this.set(CACHE_CANVAS_BBOX, bbox);
    }

    return bbox;
  };
  /**
   * 将元素放到最前面
   */


  ItemBase.prototype.toFront = function () {
    var group = this.get('group');
    group.toFront();
  };
  /**
   * 将元素放到最后面
   */


  ItemBase.prototype.toBack = function () {
    var group = this.get('group');
    group.toBack();
  };
  /**
   * 显示元素
   */


  ItemBase.prototype.show = function () {
    this.changeVisibility(true);
  };
  /**
   * 隐藏元素
   */


  ItemBase.prototype.hide = function () {
    this.changeVisibility(false);
  };
  /**
   * 更改是否显示
   * @param  {Boolean} visible 是否显示
   */


  ItemBase.prototype.changeVisibility = function (visible) {
    var group = this.get('group');

    if (visible) {
      group.show();
    } else {
      group.hide();
    }

    this.set('visible', visible);
  };
  /**
   * 元素是否可见
   * @return {Boolean} 返回该元素是否可见
   */


  ItemBase.prototype.isVisible = function () {
    return this.get('visible');
  };
  /**
   * 是否拾取及出发该元素的交互事件
   * @param {Boolean} enable 标识位
   */


  ItemBase.prototype.enableCapture = function (enable) {
    var group = this.get('group');

    if (group) {
      group.set('capture', enable);
    }
  };

  ItemBase.prototype.destroy = function () {
    if (!this.destroyed) {
      var animate = this.get('animate');
      var group = this.get('group');

      if (animate) {
        group.stopAnimate();
      }

      this.clearCache();
      group.remove();
      this._cfg = null;
      this.destroyed = true;
    }
  };

  return ItemBase;
}();

export default ItemBase;