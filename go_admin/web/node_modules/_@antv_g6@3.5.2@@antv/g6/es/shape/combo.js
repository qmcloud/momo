import { __assign } from "tslib";
import { isArray, isNil, clone } from '@antv/util';
import Global from '../global';
import Shape from './shape';
import { shapeBase } from './shapeBase';
var singleCombo = {
  itemType: 'combo',
  // 单个图形的类型
  shapeType: 'single-combo',

  /**
   * Combo 标题文本相对图形的位置，默认为 top
   * 位置包括： top, bottom, left, right, center
   * @type {String}
   */
  labelPosition: 'top',

  /**
   * 标题文本相对偏移，当 labelPosition 不为 center 时有效
   * @type {Number}
   */
  refX: Global.comboLabel.refX,
  refY: Global.comboLabel.refY,

  /**
   * 获取 Combo 宽高
   * @internal 返回 Combo 的大小，以 [width, height] 的方式维护
   * @param  {Object} cfg Combo 的配置项
   * @return {Array} 宽高
   */
  getSize: function getSize(cfg) {
    var size = clone(cfg.size || this.options.size || Global.defaultCombo.size); // size 是数组，若长度为 1，则补长度为 2

    if (isArray(size) && size.length === 1) {
      size = [size[0], size[0]];
    } // size 为数字，则转换为数组


    if (!isArray(size)) {
      size = [size, size];
    }

    return size;
  },
  // 私有方法，不希望扩展的 Combo 复写这个方法
  getLabelStyleByPosition: function getLabelStyleByPosition(cfg, labelCfg) {
    var labelPosition = labelCfg.position || this.labelPosition;
    var cfgStyle = cfg.style;
    var padding = cfg.padding || this.options.padding;
    if (isArray(padding)) padding = padding[0];
    var refX = labelCfg.refX,
        refY = labelCfg.refY; // 考虑 refX 和 refY = 0 的场景，不用用 labelCfg.refX || Global.nodeLabel.refX

    if (isNil(refX)) {
      refX = this.refX; // 不居中时的偏移量
    }

    if (isNil(refY)) {
      refY = this.refY; // 不居中时的偏移量
    }

    var size = this.getSize(cfg);
    var r = Math.max(cfgStyle.r, size[0] / 2) || size[0] / 2;
    var dis = r + padding;
    var style;

    switch (labelPosition) {
      case 'top':
        style = {
          x: 0,
          y: -dis - refY,
          textBaseline: 'bottom',
          textAlign: 'center'
        };
        break;

      case 'bottom':
        style = {
          x: 0,
          y: dis + refY,
          textBaseline: 'bottom',
          textAlign: 'center'
        };
        break;

      case 'left':
        style = {
          x: -dis + refX,
          y: 0,
          textAlign: 'left'
        };
        break;

      case 'center':
        style = {
          x: 0,
          y: 0,
          text: cfg.label,
          textAlign: 'center'
        };
        break;

      default:
        style = {
          x: dis + refX,
          y: 0,
          textAlign: 'right'
        };
        break;
    }

    style.text = cfg.label;
    return style;
  },
  drawShape: function drawShape(cfg, group) {
    var shapeType = this.shapeType; // || this.type，都已经加了 shapeType

    var style = this.getShapeStyle(cfg);
    var shape = group.addShape(shapeType, {
      attrs: style,
      draggable: true,
      name: 'combo-shape'
    });
    return shape;
  },
  updateShape: function updateShape(cfg, item, keyShapeStyle) {
    var keyShape = item.get('keyShape');
    var animate = this.options.animate;

    if (animate && keyShape.animate) {
      keyShape.animate(keyShapeStyle, {
        duration: 200,
        easing: 'easeLinear'
      });
    } else {
      keyShape.attr(__assign({}, keyShapeStyle));
    }

    this.updateLabel(cfg, item); // special for some types of nodes
  }
};
var singleComboDef = Object.assign({}, shapeBase, singleCombo);
Shape.registerCombo('single-combo', singleComboDef);