import { __assign } from "tslib";
import Global from '../global';
import { mat3, transform } from '@antv/matrix-util';
import { deepMix, each, mix, isBoolean, isPlainObject, clone } from '@antv/util';
var CLS_SHAPE_SUFFIX = '-shape';
var CLS_LABEL_SUFFIX = '-label';
export var CLS_LABEL_BG_SUFFIX = '-label-bg'; // 单个 shape 带有一个 label，共用这段代码

export var shapeBase = {
  // 默认样式及配置
  options: {},
  itemType: '',

  /**
   * 形状的类型，例如 circle，ellipse，polyline...
   */
  type: '',

  /**
   * 绘制节点/边，包含文本
   * @override
   * @param  {Object} cfg 节点的配置项
   * @param  {G.Group} group 节点的容器
   * @return {IShape} 绘制的图形
   */
  draw: function draw(cfg, group) {
    var shape = this.drawShape(cfg, group);
    shape.set('className', this.itemType + CLS_SHAPE_SUFFIX);

    if (cfg.label) {
      var label = this.drawLabel(cfg, group);
      label.set('className', this.itemType + CLS_LABEL_SUFFIX);
    }

    return shape;
  },

  /**
   * 绘制完成后的操作，便于用户继承现有的节点、边
   * @param cfg
   * @param group
   * @param keyShape
   */
  afterDraw: function afterDraw(cfg, group, keyShape) {},
  drawShape: function drawShape(cfg, group) {
    return null;
  },
  drawLabel: function drawLabel(cfg, group) {
    var defaultLabelCfg = this.options.labelCfg;
    var labelCfg = mix({}, defaultLabelCfg, cfg.labelCfg);
    var labelStyle = this.getLabelStyle(cfg, labelCfg, group);
    var rotate = labelStyle.rotate;
    delete labelStyle.rotate;
    var label = group.addShape('text', {
      attrs: labelStyle,
      draggable: true,
      className: 'text-shape',
      name: 'text-shape'
    });

    if (rotate) {
      var labelBBox = label.getBBox();
      var labelMatrix = label.getMatrix();

      if (!labelMatrix) {
        labelMatrix = mat3.create();
      }

      if (labelStyle.rotateCenter) {
        switch (labelStyle.rotateCenter) {
          case 'center':
            labelMatrix = transform(labelMatrix, [['t', -labelBBox.width / 2, -labelBBox.height / 2], ['r', rotate], ['t', labelBBox.width / 2, labelBBox.height / 2]]);
            break;

          case 'lefttop':
            labelMatrix = transform(labelMatrix, [['t', -labelStyle.x, -labelStyle.y], ['r', rotate], ['t', labelStyle.x, labelStyle.y]]);
            break;

          case 'leftcenter':
            labelMatrix = transform(labelMatrix, [['t', -labelStyle.x, -labelStyle.y - labelBBox.height / 2], ['r', rotate], ['t', labelStyle.x, labelStyle.y + labelBBox.height / 2]]);
            break;

          default:
            labelMatrix = transform(labelMatrix, [['t', -labelBBox.width / 2, -labelBBox.height / 2], ['r', rotate], ['t', labelBBox.width / 2, labelBBox.height / 2]]);
            break;
        }
      } else {
        labelMatrix = transform(labelMatrix, [['t', -labelStyle.x, -labelStyle.y - labelBBox.height / 2], ['r', rotate], ['t', labelStyle.x, labelStyle.y + labelBBox.height / 2]]);
      }

      label.setMatrix(labelMatrix);
    }

    if (labelStyle.background) {
      var rect = this.drawLabelBg(cfg, group, label);
      var labelBgClassname = this.itemType + CLS_LABEL_BG_SUFFIX;
      rect.set('classname', labelBgClassname);
      label.toFront();
    }

    return label;
  },
  drawLabelBg: function drawLabelBg(cfg, group, label) {
    var defaultLabelCfg = this.options.labelCfg;
    var labelCfg = mix({}, defaultLabelCfg, cfg.labelCfg);
    var style = this.getLabelBgStyleByPosition(label, cfg, labelCfg, group);
    var rect = group.addShape('rect', {
      name: 'text-bg-shape',
      attrs: style
    });
    return rect;
  },
  getLabelStyleByPosition: function getLabelStyleByPosition(cfg, labelCfg, group) {
    return {
      text: cfg.label
    };
  },
  getLabelBgStyleByPosition: function getLabelBgStyleByPosition(label, cfg, labelCfg, group) {
    return {};
  },

  /**
   * 获取文本的配置项
   * @param cfg 节点的配置项
   * @param labelCfg 文本的配置项
   * @param group 父容器，label 的定位可能与图形相关
   */
  getLabelStyle: function getLabelStyle(cfg, labelCfg, group) {
    var calculateStyle = this.getLabelStyleByPosition(cfg, labelCfg, group);
    var attrName = this.itemType + "Label"; // 取 nodeLabel，edgeLabel 的配置项

    var defaultStyle = Global[attrName] ? Global[attrName].style : null;
    var labelStyle = Object.assign({}, defaultStyle, calculateStyle, labelCfg.style);
    return labelStyle;
  },

  /**
   * 获取图形的配置项
   * @param cfg
   */
  getShapeStyle: function getShapeStyle(cfg) {
    return cfg.style;
  },

  /**
   * 更新节点，包含文本
   * @override
   * @param  {Object} cfg 节点/边的配置项
   * @param  {G6.Item} item 节点/边
   */
  update: function update(cfg, item) {
    this.updateShapeStyle(cfg, item);
    this.updateLabel(cfg, item);
  },
  updateShapeStyle: function updateShapeStyle(cfg, item) {
    var group = item.getContainer();
    var shape = item.getKeyShape();
    var shapeStyle = mix({}, shape.attr(), cfg.style);

    var _loop_1 = function _loop_1(key) {
      var _a;

      var style = shapeStyle[key];

      if (isPlainObject(style)) {
        // 更新图元素样式，支持更新子元素
        var subShape = group.find(function (element) {
          return element.get('name') === key;
        });

        if (subShape) {
          subShape.attr(style);
        }
      } else {
        shape.attr((_a = {}, _a[key] = style, _a));
      }
    };

    for (var key in shapeStyle) {
      _loop_1(key);
    }
  },
  updateLabel: function updateLabel(cfg, item) {
    var group = item.getContainer();
    var defaultLabelCfg = this.options.labelCfg;
    var labelClassName = this.itemType + CLS_LABEL_SUFFIX;
    var label = group.find(function (element) {
      return element.get('className') === labelClassName;
    });
    var labelBgClassname = this.itemType + CLS_LABEL_BG_SUFFIX;
    var labelBg = group.find(function (element) {
      return element.get('classname') === labelBgClassname;
    }); // 防止 cfg.label = "" 的情况

    if (cfg.label || cfg.label === '') {
      // 若传入的新配置中有 label，（用户没传入但原先有 label，label 也会有值）
      if (!label) {
        // 若原先不存在 label，则绘制一个新的 label
        var newLabel = this.drawLabel(cfg, group);
        newLabel.set('className', labelClassName);
      } else {
        // 若原先存在 label，则更新样式。与 getLabelStyle 不同在于这里需要融合当前 label 的样式
        // 用于融合 style 以外的属性：position, offset, ...
        var currentLabelCfg = {};

        if (item.getModel) {
          currentLabelCfg = item.getModel().labelCfg;
        }

        var labelCfg = deepMix({}, defaultLabelCfg, currentLabelCfg, cfg.labelCfg); // 获取位置信息

        var calculateStyle = this.getLabelStyleByPosition(cfg, labelCfg, group); // 取 nodeLabel，edgeLabel 的配置项

        var cfgStyle = cfg.labelCfg ? cfg.labelCfg.style : undefined;
        var cfgBgStyle = labelCfg.style && labelCfg.style.background; // 需要融合当前 label 的样式 label.attr()。不再需要全局/默认样式，因为已经应用在当前的 label 上

        var labelStyle = Object.assign({}, label.attr(), calculateStyle, cfgStyle);
        var rotate = labelStyle.rotate;
        delete labelStyle.rotate; // 计算 label 的旋转矩阵

        if (rotate) {
          // if G 4.x define the rotateAtStart, use it directly instead of using the following codes
          var rotateMatrix = mat3.create();
          rotateMatrix = transform(rotateMatrix, [['t', -labelStyle.x, -labelStyle.y], ['r', rotate], ['t', labelStyle.x, labelStyle.y]]);
          label.resetMatrix();
          label.attr(__assign(__assign({}, labelStyle), {
            matrix: rotateMatrix
          }));
        } else {
          label.resetMatrix();
          label.attr(labelStyle);
        }

        if (!labelBg) {
          if (labelStyle.background) {
            labelBg = this.drawLabelBg(cfg, group, label);
            labelBg.set('classname', labelBgClassname);
            label.toFront();
          }
        } else if (labelStyle.background) {
          var calculateBgStyle = this.getLabelBgStyleByPosition(label, cfg, labelCfg, group); // const labelBgStyle = Object.assign({}, labelBg.attr(), calculateBgStyle, cfgBgStyle);

          var labelBgStyle = Object.assign({}, calculateBgStyle, cfgBgStyle);
          labelBg.resetMatrix();

          if (rotate) {
            labelBg.rotateAtStart(rotate);
          }

          labelBg.attr(labelBgStyle);
        } else {
          group.removeChild(labelBg);
        }
      }
    }
  },
  // update(cfg, item) // 默认不定义
  afterUpdate: function afterUpdate(cfg, item) {},

  /**
   * 设置节点的状态，主要是交互状态，业务状态请在 draw 方法中实现
   * 单图形的节点仅考虑 selected、active 状态，有其他状态需求的用户自己复写这个方法
   * @override
   * @param  {String} name 状态名称
   * @param  {String | Boolean} value 状态值
   * @param  {G6.Item} item 节点
   */
  setState: function setState(name, value, item) {
    var _a, _b;

    var shape = item.get('keyShape');

    if (!shape) {
      return;
    }

    var type = item.getType();
    var stateName = isBoolean(value) ? name : name + ":" + value;
    var shapeStateStyle = this.getStateStyle(stateName, true, item);
    var itemStateStyle = item.getStateStyle(stateName); // 要设置或取消的状态的样式
    // 当没有 state 状态时，默认使用 model.stateStyles 中的样式

    var styles = mix({}, itemStateStyle || shapeStateStyle);
    var group = item.getContainer();

    if (value) {
      var _loop_2 = function _loop_2(key) {
        var _a;

        var style = styles[key];

        if (isPlainObject(style)) {
          var subShape = group.find(function (element) {
            return element.get('name') === key;
          });

          if (subShape) {
            subShape.attr(style);
          }
        } else {
          // 非纯对象，则认为是设置到 keyShape 上面的
          shape.attr((_a = {}, _a[key] = style, _a));
        }
      }; // style 为要设置的状态的样式


      for (var key in styles) {
        _loop_2(key);
      }
    } else {
      // 所有生效的 state 的样式
      var enableStatesStyle = clone(item.getCurrentStatesStyle()); // 原始样式

      var originStyle = clone(item.getOriginStyle());
      var keyShapeName = shape.get('name');
      var keyShapeStyles = shape.attr(); // 已有样式 - 要取消的状态的样式

      var filtetDisableStatesStyle = {};

      var _loop_3 = function _loop_3(p) {
        var style = styles[p];

        if (isPlainObject(style)) {
          var subShape = group.find(function (element) {
            return element.get('name') === p;
          });

          if (subShape) {
            var subShapeStyles_1 = subShape.attr(); // const current = subShapeStyles[p]

            each(style, function (value, key) {
              if (subShapeStyles_1[key]) {
                delete subShapeStyles_1[key];
              }
            });
            filtetDisableStatesStyle[p] = subShapeStyles_1;
          }
        } else {
          // 从图元素现有的样式中删除本次要取消的 states 中存在的属性值
          var keptAttrs = ['x', 'y', 'cx', 'cy'];

          if (keyShapeStyles[p] && !(keptAttrs.indexOf(p) > -1)) {
            delete keyShapeStyles[p];
          }
        }
      }; // style 为要取消的状态的样式


      for (var p in styles) {
        _loop_3(p);
      } // 从图元素现有的样式中删除本次要取消的 states 中存在的属性值后，
      // 如果 keyShape 有 name 属性，则 filtetDisableStatesStyle 的格式为 { keyShapeName: {} }
      // 否则为普通对象


      if (!keyShapeName) {
        mix(filtetDisableStatesStyle, keyShapeStyles);
      } else {
        filtetDisableStatesStyle[keyShapeName] = keyShapeStyles;
      }

      for (var key in enableStatesStyle) {
        var enableStyle = enableStatesStyle[key];

        if (!isPlainObject(enableStyle)) {
          // 把样式属性merge到keyShape中
          if (!keyShapeName) {
            mix(originStyle, (_a = {}, _a[key] = enableStyle, _a));
          } else {
            mix(originStyle[keyShapeName], (_b = {}, _b[key] = enableStyle, _b));
          }

          delete enableStatesStyle[key];
        }
      }

      var originstyles = {};
      deepMix(originstyles, originStyle, filtetDisableStatesStyle, enableStatesStyle);

      var _loop_4 = function _loop_4(originKey) {
        var _a, _b;

        var style = originstyles[originKey];

        if (isPlainObject(style)) {
          var subShape = group.find(function (element) {
            return element.get('name') === originKey;
          });

          if (subShape) {
            subShape.attr(style);
          }
        } else {
          // 当更新 combo 状态时，当不存在 keyShapeName 时候，则认为是设置到 keyShape 上面的
          if (type === 'combo') {
            if (!keyShapeName) {
              shape.attr((_a = {}, _a[originKey] = style, _a));
            }
          } else {
            shape.attr((_b = {}, _b[originKey] = style, _b));
          }
        }
      };

      for (var originKey in originstyles) {
        _loop_4(originKey);
      }
    }
  },

  /**
   * 获取不同状态下的样式
   *
   * @param {string} name 状态名称
   * @param {boolean} value 是否启用该状态
   * @param {Item} item Node或Edge的实例
   * @return {object} 样式
   */
  getStateStyle: function getStateStyle(name, value, item) {
    var model = item.getModel();

    if (value) {
      var modelStateStyle = model.stateStyles ? model.stateStyles[name] : this.options.stateStyles && this.options.stateStyles[name];
      return mix({}, model.style, modelStateStyle);
    }

    return {};
  },

  /**
   * 获取控制点
   * @param  {Object} cfg 节点、边的配置项
   * @return {Array|null} 控制点的数组,如果为 null，则没有控制点
   */
  getControlPoints: function getControlPoints(cfg) {
    return cfg.controlPoints;
  },

  /**
   * 获取控制点
   * @param  {Object} cfg 节点、边的配置项
   * @return {Array|null} 锚点的数组,如果为 null，则没有锚点
   */
  getAnchorPoints: function getAnchorPoints(cfg) {
    var defaultAnchorPoints = this.options.anchorPoints;
    var anchorPoints = cfg.anchorPoints || defaultAnchorPoints;
    return anchorPoints;
  }
};