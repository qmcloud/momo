import { __assign } from "tslib";
import { mix, isNumber, clone } from '@antv/util';
import Global from '../../global';
import Shape from '../shape';
import { isNil } from '@antv/util';
Shape.registerCombo('rect', {
  // 自定义 Combo 时的配置
  options: {
    size: [40, 5],
    padding: [25, 20, 15, 20],
    animate: true,
    style: {
      radius: 0,
      stroke: Global.defaultCombo.style.stroke,
      fill: Global.defaultCombo.style.fill,
      lineWidth: Global.defaultCombo.style.lineWidth,
      fillOpacity: 1
    },
    // 文本样式配置
    labelCfg: {
      style: {
        fill: '#595959'
      }
    },
    // 连接点，默认为左右
    anchorPoints: [[0, 0.5], [1, 0.5]]
  },
  shapeType: 'rect',
  labelPosition: 'top',
  drawShape: function drawShape(cfg, group) {
    var style = this.getShapeStyle(cfg);
    var keyShape = group.addShape('rect', {
      attrs: style,
      className: 'rect-combo',
      name: 'rect-combo',
      draggable: true
    });
    return keyShape;
  },
  // 私有方法，不希望扩展的 Combo 复写这个方法
  getLabelStyleByPosition: function getLabelStyleByPosition(cfg, labelCfg) {
    var labelPosition = labelCfg.position || this.labelPosition;
    var cfgStyle = cfg.style;
    var padding = cfg.padding || this.options.padding;
    if (isNumber(padding)) padding = [padding, padding, padding, padding];
    var refX = labelCfg.refX,
        refY = labelCfg.refY; // 考虑 refX 和 refY = 0 的场景，不用用 labelCfg.refX || Global.nodeLabel.refY

    if (isNil(refX)) {
      refX = this.refX; // 不居中时的偏移量
    }

    if (isNil(refY)) {
      refY = this.refY; // 不居中时的偏移量
    }

    var leftDis = cfgStyle.width / 2 + padding[3];
    var topDis = cfgStyle.height / 2 + padding[0];
    var style;

    switch (labelPosition) {
      case 'top':
        style = {
          x: 0 - leftDis + refX,
          y: 0 - topDis + refY,
          textBaseline: 'top',
          textAlign: 'left'
        };
        break;

      case 'bottom':
        style = {
          x: 0,
          y: topDis + refY,
          textBaseline: 'bottom',
          textAlign: 'center'
        };
        break;

      case 'left':
        style = {
          x: 0 - leftDis + refY,
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
          x: leftDis + refX,
          y: 0,
          textAlign: 'right'
        };
        break;
    }

    style.text = cfg.label;
    return style;
  },

  /**
   * 获取节点的样式，供基于该节点自定义时使用
   * @param {Object} cfg 节点数据模型
   * @return {Object} 节点的样式
   */
  getShapeStyle: function getShapeStyle(cfg) {
    var defaultStyle = this.options.style;
    var padding = cfg.padding || this.options.padding;
    if (isNumber(padding)) padding = [padding, padding, padding, padding];
    var strokeStyle = {
      stroke: cfg.color
    }; // 如果设置了color，则覆盖默认的stroke属性

    var style = mix({}, defaultStyle, strokeStyle, cfg.style);
    var size = this.getSize(cfg);
    var width;
    var height;
    if (!isNumber(style.width) || isNaN(style.width)) width = size[0] || Global.defaultCombo.style.width;else width = Math.max(style.width, size[0]) || size[0];
    if (!isNumber(style.height) || isNaN(style.height)) height = size[1] || Global.defaultCombo.style.height;else height = Math.max(style.height, size[1]) || size[1];
    var x = -width / 2 - padding[3];
    var y = -height / 2 - padding[0];
    style.width = width + padding[1] + padding[3];
    style.height = height + padding[0] + padding[2];
    var styles = Object.assign({}, {
      x: x,
      y: y
    }, style);

    if (!cfg.style) {
      cfg.style = {
        width: width,
        height: height
      };
    } else {
      cfg.style.width = width;
      cfg.style.height = height;
    }

    return styles;
  },
  update: function update(cfg, item) {
    var size = this.getSize(cfg);
    var padding = cfg.padding || this.options.padding;
    if (isNumber(padding)) padding = [padding, padding, padding, padding];
    var cfgStyle = clone(cfg.style);
    var width = Math.max(cfgStyle.width, size[0]) || size[0];
    var height = Math.max(cfgStyle.height, size[1]) || size[1];
    cfgStyle.width = width + padding[1] + padding[3];
    cfgStyle.height = height + padding[0] + padding[2];
    var itemCacheSize = item.get('sizeCache');

    if (itemCacheSize) {
      itemCacheSize.width = cfgStyle.width;
      itemCacheSize.height = cfgStyle.height;
    }

    cfgStyle.x = -width / 2 - padding[3];
    cfgStyle.y = -height / 2 - padding[0]; // 下面这些属性需要覆盖默认样式与目前样式，但若在 cfg 中有指定则应该被 cfg 的相应配置覆盖。

    var strokeStyle = {
      stroke: cfg.color
    }; // 与 getShapeStyle 不同在于，update 时需要获取到当前的 style 进行融合。即新传入的配置项中没有涉及的属性，保留当前的配置。 

    var keyShape = item.get('keyShape');
    var style = mix({}, keyShape.attr(), strokeStyle, cfgStyle);

    if (cfg.style) {
      cfg.style.width = width;
      cfg.style.height = height;
    } else {
      cfg.style = {
        width: width,
        height: height
      };
    }

    this.updateShape(cfg, item, style, false);
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

    this.updateLabel(cfg, item);
  }
}, 'single-combo');