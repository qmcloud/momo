import { __assign } from "tslib";
import { isArray, isNil, mix } from '@antv/util';
import { formatPadding } from '../util/base';
import Global from '../global';
import Shape from './shape';
import { shapeBase } from './shapeBase';
var singleNode = {
  itemType: 'node',
  // 单个图形的类型
  shapeType: 'single-node',

  /**
   * 文本相对图形的位置，默认以中心点
   * 位置包括： top, bottom, left, right, center
   * @type {String}
   */
  labelPosition: 'center',

  /**
   * 文本相对偏移，当 labelPosition 不为 center 时有效
   * @type {Number}
   */
  offset: Global.nodeLabel.offset,

  /**
   * 获取节点宽高
   * @internal 返回节点的大小，以 [width, height] 的方式维护
   * @param  {Object} cfg 节点的配置项
   * @return {Array} 宽高
   */
  getSize: function getSize(cfg) {
    var size = cfg.size || this.options.size || Global.defaultNode.size; // size 是数组，但长度为1，则补长度为2

    if (isArray(size) && size.length === 1) {
      size = [size[0], size[0]];
    } // size 为数字，则转换为数组


    if (!isArray(size)) {
      size = [size, size];
    }

    return size;
  },
  // 私有方法，不希望扩展的节点复写这个方法
  getLabelStyleByPosition: function getLabelStyleByPosition(cfg, labelCfg) {
    var labelPosition = labelCfg.position || this.labelPosition; // 默认的位置（最可能的情形），所以放在最上面

    if (labelPosition === 'center') {
      return {
        x: 0,
        y: 0,
        text: cfg.label
      };
    }

    var offset = labelCfg.offset;

    if (isNil(offset)) {
      // 考虑 offset = 0 的场景，不用用 labelCfg.offset || Global.nodeLabel.offset
      offset = this.offset; // 不居中时的偏移量
    }

    var size = this.getSize(cfg);
    var width = size[0];
    var height = size[1];
    var style;

    switch (labelPosition) {
      case 'top':
        style = {
          x: 0,
          y: 0 - height / 2 - offset,
          textBaseline: 'bottom'
        };
        break;

      case 'bottom':
        style = {
          x: 0,
          y: height / 2 + offset,
          textBaseline: 'top'
        };
        break;

      case 'left':
        style = {
          x: 0 - width / 2 - offset,
          y: 0,
          textAlign: 'right'
        };
        break;

      default:
        style = {
          x: width / 2 + offset,
          y: 0,
          textAlign: 'left'
        };
        break;
    }

    style.text = cfg.label;
    return style;
  },
  getLabelBgStyleByPosition: function getLabelBgStyleByPosition(label, cfg, labelCfg, group) {
    if (!label) {
      return {};
    }

    var bbox = label.getBBox();
    var backgroundStyle = labelCfg.style && labelCfg.style.background;

    if (!backgroundStyle) {
      return {};
    }

    var padding = formatPadding(backgroundStyle.padding);
    var backgroundWidth = bbox.width + padding[1] + padding[3];
    var backgroundHeight = bbox.height + padding[0] + padding[2];
    var labelPosition = labelCfg.position || this.labelPosition;
    var offset = labelCfg.offset;

    if (isNil(offset)) {
      // 考虑 offset = 0 的场景，不用用 labelCfg.offset || Global.nodeLabel.offset
      offset = this.offset; // 不居中时的偏移量
    }

    var size = this.getSize(cfg);
    var width = size[0];
    var height = size[1];
    var style;

    switch (labelPosition) {
      case 'top':
        style = {
          x: 0 - bbox.width / 2 - padding[3],
          y: 0 - height / 2 - offset - bbox.height - padding[0]
        };
        break;

      case 'bottom':
        style = {
          x: 0 - bbox.width / 2 - padding[3],
          y: height / 2 + offset - padding[2]
        };
        break;

      case 'left':
        style = {
          x: 0 - width / 2 - offset - bbox.width - padding[3],
          y: padding[0] + padding[2] === 0 ? 0 : -bbox.height / 2 + (padding[0] + padding[2]) / 2
        };
        break;

      default:
        style = {
          x: width / 2 + offset - padding[3],
          y: padding[0] + padding[2] === 0 ? 0 : -bbox.height / 2 + (padding[0] + padding[2]) / 2
        };
        break;
    }

    style = __assign(__assign(__assign({}, style), backgroundStyle), {
      width: backgroundWidth,
      height: backgroundHeight
    });
    return style;
  },
  drawShape: function drawShape(cfg, group) {
    var shapeType = this.shapeType; // || this.type，都已经加了 shapeType

    var style = this.getShapeStyle(cfg);
    var shape = group.addShape(shapeType, {
      attrs: style,
      draggable: true,
      name: 'node-shape'
    });
    return shape;
  },

  /**
   * 更新linkPoints
   * @param {Object} cfg 节点数据配置项
   * @param {Group} group Item所在的group
   */
  updateLinkPoints: function updateLinkPoints(cfg, group) {
    var defaultLinkPoints = this.options.linkPoints;
    var markLeft = group.find(function (element) {
      return element.get('className') === 'link-point-left';
    });
    var markRight = group.find(function (element) {
      return element.get('className') === 'link-point-right';
    });
    var markTop = group.find(function (element) {
      return element.get('className') === 'link-point-top';
    });
    var markBottom = group.find(function (element) {
      return element.get('className') === 'link-point-bottom';
    });
    var currentLinkPoints;

    if (markLeft) {
      currentLinkPoints = markLeft.attr();
    }

    if (markRight && !currentLinkPoints) {
      currentLinkPoints = markRight.attr();
    }

    if (markTop && !currentLinkPoints) {
      currentLinkPoints = markTop.attr();
    }

    if (markBottom && !currentLinkPoints) {
      currentLinkPoints = markBottom.attr();
    }

    if (!currentLinkPoints) currentLinkPoints = defaultLinkPoints;
    var linkPoints = mix({}, currentLinkPoints, cfg.linkPoints);
    var markFill = linkPoints.fill,
        markStroke = linkPoints.stroke,
        borderWidth = linkPoints.lineWidth;
    var markSize = linkPoints.size / 2;
    if (!markSize) markSize = linkPoints.r;

    var _a = cfg.linkPoints ? cfg.linkPoints : {
      left: undefined,
      right: undefined,
      top: undefined,
      bottom: undefined
    },
        left = _a.left,
        right = _a.right,
        top = _a.top,
        bottom = _a.bottom;

    var size = this.getSize(cfg);
    var width = size[0];
    var height = size[1];
    var styles = {
      r: markSize,
      fill: markFill,
      stroke: markStroke,
      lineWidth: borderWidth
    };

    if (markLeft) {
      if (!left && left !== undefined) {
        markLeft.remove();
      } else {
        markLeft.attr(__assign(__assign({}, styles), {
          x: -width / 2,
          y: 0
        }));
      }
    } else if (left) {
      group.addShape('circle', {
        attrs: __assign(__assign({}, styles), {
          x: -width / 2,
          y: 0
        }),
        className: 'link-point-left',
        name: 'link-point-left',
        isAnchorPoint: true
      });
    }

    if (markRight) {
      if (!right && right !== undefined) {
        markRight.remove();
      }

      markRight.attr(__assign(__assign({}, styles), {
        x: width / 2,
        y: 0
      }));
    } else if (right) {
      group.addShape('circle', {
        attrs: __assign(__assign({}, styles), {
          x: width / 2,
          y: 0
        }),
        className: 'link-point-right',
        name: 'link-point-right',
        isAnchorPoint: true
      });
    }

    if (markTop) {
      if (!top && top !== undefined) {
        markTop.remove();
      }

      markTop.attr(__assign(__assign({}, styles), {
        x: 0,
        y: -height / 2
      }));
    } else if (top) {
      group.addShape('circle', {
        attrs: __assign(__assign({}, styles), {
          x: 0,
          y: -height / 2
        }),
        className: 'link-point-top',
        name: 'link-point-top',
        isAnchorPoint: true
      });
    }

    if (markBottom) {
      if (!bottom && bottom !== undefined) {
        markBottom.remove();
      } else {
        markBottom.attr(__assign(__assign({}, styles), {
          x: 0,
          y: height / 2
        }));
      }
    } else if (bottom) {
      group.addShape('circle', {
        attrs: __assign(__assign({}, styles), {
          x: 0,
          y: height / 2
        }),
        className: 'link-point-bottom',
        name: 'link-point-bottom',
        isAnchorPoint: true
      });
    }
  },
  updateShape: function updateShape(cfg, item, keyShapeStyle, hasIcon) {
    var keyShape = item.get('keyShape');
    keyShape.attr(__assign({}, keyShapeStyle));
    this.updateLabel(cfg, item); // special for some types of nodes

    if (hasIcon) {
      this.updateIcon(cfg, item);
    }
  },
  updateIcon: function updateIcon(cfg, item) {
    var _this = this;

    var group = item.getContainer();
    var defaultIcon = this.options.icon;
    var icon = mix({}, defaultIcon, cfg.icon);
    var show = (cfg.icon ? cfg.icon : {
      show: undefined
    }).show;
    var iconShape = group.find(function (element) {
      return element.get('className') === _this.type + "-icon";
    });

    if (iconShape) {
      // 若原先存在 icon
      if (show || show === undefined) {
        // 若传入 show: true, 或没有设置，则更新原有的 icon 样式
        var iconConfig = mix({}, defaultIcon, iconShape.attr(), cfg.icon);
        var w = iconConfig.width,
            h = iconConfig.height;
        iconShape.attr(__assign(__assign({}, iconConfig), {
          x: -w / 2,
          y: -h / 2
        }));
      } else {
        // 若传入了 show: false 则删除原先的 icon
        iconShape.remove();
      }
    } else if (show) {
      // 如果原先不存在 icon，但传入了 show: true，则新增 icon
      var w = icon.width,
          h = icon.height;
      group.addShape('image', {
        attrs: __assign(__assign({}, icon), {
          x: -w / 2,
          y: -h / 2
        }),
        className: this.type + "-icon",
        name: this.type + "-icon"
      }); // to ensure the label is on the top of all the shapes

      var labelShape = group.find(function (element) {
        return element.get('className') === "node-label";
      });

      if (labelShape) {
        labelShape.toFront();
      }
    }
  }
};
var singleNodeDef = Object.assign({}, shapeBase, singleNode);
Shape.registerNode('single-node', singleNodeDef);