"use strict";

var _global = _interopRequireDefault(require("../../global"));

var _shape = _interopRequireDefault(require("../shape"));

var _util = require("@antv/util");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// 圆形 Combo
_shape.default.registerCombo('circle', {
  // 自定义节点时的配置
  options: {
    size: [_global.default.defaultCombo.size[0], _global.default.defaultCombo.size[0]],
    padding: _global.default.defaultCombo.padding[0],
    animate: true,
    style: {
      stroke: _global.default.defaultCombo.style.stroke,
      fill: _global.default.defaultCombo.style.fill,
      lineWidth: _global.default.defaultCombo.style.lineWidth,
      opacity: 0.8
    },
    labelCfg: {
      style: {
        fill: '#595959'
      },
      refX: 0,
      refY: 0
    }
  },
  shapeType: 'circle',
  // 文本位置
  labelPosition: 'top',
  drawShape: function drawShape(cfg, group) {
    var style = this.getShapeStyle(cfg);
    delete style.height;
    delete style.width;
    var keyShape = group.addShape('circle', {
      attrs: style,
      className: 'circle-combo',
      name: 'circle-combo',
      draggable: true
    });
    return keyShape;
  },

  /**
   * 获取 Combo 的样式，供基于该 Combo 自定义时使用
   * @param {Object} cfg Combo 数据模型
   * @return {Object} Combo 的样式
   */
  getShapeStyle: function getShapeStyle(cfg) {
    var defaultStyle = this.options.style;
    var padding = cfg.padding || this.options.padding;
    if ((0, _util.isArray)(padding)) padding = padding[0];
    var strokeStyle = {
      stroke: cfg.color
    }; // 如果设置了color，则覆盖默认的stroke属性

    var style = (0, _util.mix)({}, defaultStyle, strokeStyle, cfg.style);
    var size = this.getSize(cfg);
    var r;
    if (!(0, _util.isNumber)(style.r) || isNaN(style.r)) r = size[0] / 2 || _global.default.defaultCombo.style.r;else r = Math.max(style.r, size[0] / 2) || size[0] / 2;
    style.r = r + padding;
    var styles = Object.assign({}, {
      x: 0,
      y: 0
    }, style);
    if (cfg.style) cfg.style.r = r;else {
      cfg.style = {
        r: r
      };
    }
    return styles;
  },
  update: function update(cfg, item) {
    var size = this.getSize(cfg);
    var padding = cfg.padding || this.options.padding;
    if ((0, _util.isArray)(padding)) padding = padding[0];
    var cfgStyle = (0, _util.clone)(cfg.style);
    var r = Math.max(cfgStyle.r, size[0] / 2) || size[0] / 2;
    ;
    cfgStyle.r = r + padding;
    var itemCacheSize = item.get('sizeCache');

    if (itemCacheSize) {
      itemCacheSize.r = cfgStyle.r;
    } // 下面这些属性需要覆盖默认样式与目前样式，但若在 cfg 中有指定则应该被 cfg 的相应配置覆盖。


    var strokeStyle = {
      stroke: cfg.color
    }; // 与 getShapeStyle 不同在于，update 时需要获取到当前的 style 进行融合。即新传入的配置项中没有涉及的属性，保留当前的配置。

    var keyShape = item.get('keyShape');
    var style = (0, _util.mix)({}, keyShape.attr(), strokeStyle, cfgStyle);
    if (cfg.style) cfg.style.r = r;else {
      cfg.style = {
        r: r
      };
    }
    this.updateShape(cfg, item, style, true);
  }
}, 'single-combo');