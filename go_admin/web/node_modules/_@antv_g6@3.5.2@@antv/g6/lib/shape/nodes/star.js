"use strict";

var _tslib = require("tslib");

var _util = require("@antv/util");

var _global = _interopRequireDefault(require("../../global"));

var _shape = _interopRequireDefault(require("../shape"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// 五角星shape
_shape.default.registerNode('star', {
  // 自定义节点时的配置
  options: {
    size: 60,
    style: {
      stroke: _global.default.defaultShapeStrokeColor,
      fill: _global.default.defaultShapeFillColor,
      lineWidth: _global.default.defaultNode.style.lineWidth
    },
    // 文本样式配置
    labelCfg: {
      style: {
        fill: '#595959'
      }
    },
    // 节点上左右上下四个方向上的链接circle配置
    linkPoints: {
      top: false,
      right: false,
      left: false,
      leftBottom: false,
      rightBottom: false,
      // circle的大小
      size: 3,
      lineWidth: 1,
      fill: '#fff',
      stroke: '#72CC4A'
    },
    // 节点中icon配置
    icon: {
      // 是否显示icon，值为 false 则不渲染icon
      show: false,
      // icon的地址，字符串类型
      img: 'https://gw.alipayobjects.com/zos/basement_prod/012bcf4f-423b-4922-8c24-32a89f8c41ce.svg',
      width: 16,
      height: 16
    }
  },
  shapeType: 'star',
  // 文本位置
  labelPosition: 'center',
  drawShape: function drawShape(cfg, group) {
    var defaultIcon = this.options.icon;
    var style = this.getShapeStyle(cfg);
    var icon = (0, _util.mix)({}, defaultIcon, cfg.icon);
    var keyShape = group.addShape('path', {
      attrs: style,
      className: 'star-keyShape',
      name: 'star-keyShape',
      draggable: true
    });
    var w = icon.width,
        h = icon.height,
        show = icon.show;

    if (show) {
      var image = group.addShape('image', {
        attrs: (0, _tslib.__assign)({
          x: -w / 2,
          y: -h / 2
        }, icon),
        className: 'star-icon',
        name: 'star-icon',
        draggable: true
      });
    }

    this.drawLinkPoints(cfg, group);
    return keyShape;
  },

  /**
   * 绘制节点上的LinkPoints
   * @param {Object} cfg data数据配置项
   * @param {Group} group Group实例
   */
  drawLinkPoints: function drawLinkPoints(cfg, group) {
    var defaultLinkPoints = this.options.linkPoints;
    var linkPoints = (0, _util.mix)({}, defaultLinkPoints, cfg.linkPoints);
    var top = linkPoints.top,
        left = linkPoints.left,
        right = linkPoints.right,
        leftBottom = linkPoints.leftBottom,
        rightBottom = linkPoints.rightBottom,
        markSize = linkPoints.size,
        markStyle = (0, _tslib.__rest)(linkPoints, ["top", "left", "right", "leftBottom", "rightBottom", "size"]);
    var size = this.getSize(cfg);
    var outerR = size[0];

    if (right) {
      // right circle
      // up down left right 四个方向的坐标均不相同
      var x1 = Math.cos((18 + 72 * 0) / 180 * Math.PI) * outerR;
      var y1 = Math.sin((18 + 72 * 0) / 180 * Math.PI) * outerR;
      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, markStyle), {
          x: x1,
          y: -y1,
          r: markSize
        }),
        className: 'link-point-right',
        name: 'link-point-right'
      });
    }

    if (top) {
      // up down left right 四个方向的坐标均不相同
      var x1 = Math.cos((18 + 72 * 1) / 180 * Math.PI) * outerR;
      var y1 = Math.sin((18 + 72 * 1) / 180 * Math.PI) * outerR; // top circle

      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, markStyle), {
          x: x1,
          y: -y1,
          r: markSize
        }),
        className: 'link-point-top',
        name: 'link-point-top'
      });
    }

    if (left) {
      // up down left right 四个方向的坐标均不相同
      var x1 = Math.cos((18 + 72 * 2) / 180 * Math.PI) * outerR;
      var y1 = Math.sin((18 + 72 * 2) / 180 * Math.PI) * outerR; // left circle

      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, markStyle), {
          x: x1,
          y: -y1,
          r: markSize
        }),
        className: 'link-point-left',
        name: 'link-point-left'
      });
    }

    if (leftBottom) {
      // up down left right 四个方向的坐标均不相同
      var x1 = Math.cos((18 + 72 * 3) / 180 * Math.PI) * outerR;
      var y1 = Math.sin((18 + 72 * 3) / 180 * Math.PI) * outerR; // left bottom circle

      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, markStyle), {
          x: x1,
          y: -y1,
          r: markSize
        }),
        className: 'link-point-left-bottom',
        name: 'link-point-left-bottom'
      });
    }

    if (rightBottom) {
      // up down left right 四个方向的坐标均不相同
      var x1 = Math.cos((18 + 72 * 4) / 180 * Math.PI) * outerR;
      var y1 = Math.sin((18 + 72 * 4) / 180 * Math.PI) * outerR; // left bottom circle

      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, markStyle), {
          x: x1,
          y: -y1,
          r: markSize
        }),
        className: 'link-point-right-bottom',
        name: 'link-point-right-bottom'
      });
    }
  },
  getPath: function getPath(cfg) {
    var size = this.getSize(cfg);
    var outerR = size[0];
    var defaultInnerR = outerR * 3 / 8;
    var innerR = cfg.innerR || defaultInnerR;
    var path = [];

    for (var i = 0; i < 5; i++) {
      var x1 = Math.cos((18 + 72 * i) / 180 * Math.PI) * outerR;
      var y1 = Math.sin((18 + 72 * i) / 180 * Math.PI) * outerR;
      var x2 = Math.cos((54 + 72 * i) / 180 * Math.PI) * innerR;
      var y2 = Math.sin((54 + 72 * i) / 180 * Math.PI) * innerR;

      if (i === 0) {
        path.push(['M', x1, -y1]);
      } else {
        path.push(['L', x1, -y1]);
      }

      path.push(['L', x2, -y2]);
    }

    path.push(['Z']);
    return path;
  },

  /**
   * 获取节点的样式，供基于该节点自定义时使用
   * @param {Object} cfg 节点数据模型
   * @return {Object} 节点的样式
   */
  getShapeStyle: function getShapeStyle(cfg) {
    var defaultStyle = this.options.style;
    var strokeStyle = {
      stroke: cfg.color
    }; // 如果设置了color，则覆盖原来默认的 stroke 属性。但 cfg 中但 stroke 属性优先级更高

    var style = (0, _util.mix)({}, defaultStyle, strokeStyle, cfg.style);
    var path = this.getPath(cfg);
    var styles = (0, _tslib.__assign)({
      path: path
    }, style);
    return styles;
  },
  update: function update(cfg, item) {
    var group = item.getContainer();
    var defaultStyle = this.options.style;
    var path = this.getPath(cfg); // 下面这些属性需要覆盖默认样式与目前样式，但若在 cfg 中有指定则应该被 cfg 的相应配置覆盖。

    var strokeStyle = {
      stroke: cfg.color,
      path: path
    }; // 与 getShapeStyle 不同在于，update 时需要获取到当前的 style 进行融合。即新传入的配置项中没有涉及的属性，保留当前的配置。

    var keyShape = item.get('keyShape');
    var style = (0, _util.mix)({}, defaultStyle, keyShape.attr(), strokeStyle);
    style = (0, _util.mix)(style, cfg.style);
    this.updateShape(cfg, item, style, true);
    this.updateLinkPoints(cfg, group);
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
    var markLeftBottom = group.find(function (element) {
      return element.get('className') === 'link-point-left-bottom';
    });
    var markRightBottom = group.find(function (element) {
      return element.get('className') === 'link-point-right-bottom';
    });
    var currentLinkPoints = defaultLinkPoints;
    var existLinkPoint = markLeft || markRight || markTop || markLeftBottom || markRightBottom;

    if (existLinkPoint) {
      currentLinkPoints = existLinkPoint.attr();
    }

    var linkPoints = (0, _util.mix)({}, currentLinkPoints, cfg.linkPoints);
    var markFill = linkPoints.fill,
        markStroke = linkPoints.stroke,
        borderWidth = linkPoints.lineWidth;
    var markSize = linkPoints.size;
    if (!markSize) markSize = linkPoints.r;

    var _a = cfg.linkPoints ? cfg.linkPoints : {
      left: undefined,
      right: undefined,
      top: undefined,
      leftBottom: undefined,
      rightBottom: undefined
    },
        left = _a.left,
        right = _a.right,
        top = _a.top,
        leftBottom = _a.leftBottom,
        rightBottom = _a.rightBottom;

    var size = this.getSize(cfg);
    var outerR = size[0];
    var styles = {
      r: markSize,
      fill: markFill,
      stroke: markStroke,
      lineWidth: borderWidth
    };
    var x = Math.cos((18 + 72 * 0) / 180 * Math.PI) * outerR;
    var y = Math.sin((18 + 72 * 0) / 180 * Math.PI) * outerR;

    if (markRight) {
      if (!right && right !== undefined) {
        markRight.remove();
      } else {
        markRight.attr((0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }));
      }
    } else if (right) {
      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }),
        className: 'link-point-right',
        name: 'link-point-right',
        isAnchorPoint: true
      });
    }

    x = Math.cos((18 + 72 * 1) / 180 * Math.PI) * outerR;
    y = Math.sin((18 + 72 * 1) / 180 * Math.PI) * outerR;

    if (markTop) {
      if (!top && top !== undefined) {
        markTop.remove();
      } else {
        markTop.attr((0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }));
      }
    } else if (top) {
      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }),
        className: 'link-point-top',
        name: 'link-point-top',
        isAnchorPoint: true
      });
    }

    x = Math.cos((18 + 72 * 2) / 180 * Math.PI) * outerR;
    y = Math.sin((18 + 72 * 2) / 180 * Math.PI) * outerR;

    if (markLeft) {
      if (!left && left !== undefined) {
        markLeft.remove();
      } else {
        markLeft.attr((0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }));
      }
    } else if (left) {
      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }),
        className: 'link-point-left',
        name: 'link-point-left',
        isAnchorPoint: true
      });
    }

    x = Math.cos((18 + 72 * 3) / 180 * Math.PI) * outerR;
    y = Math.sin((18 + 72 * 3) / 180 * Math.PI) * outerR;

    if (markLeftBottom) {
      if (!leftBottom && leftBottom !== undefined) {
        markLeftBottom.remove();
      } else {
        markLeftBottom.attr((0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }));
      }
    } else if (leftBottom) {
      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }),
        className: 'link-point-left-bottom',
        name: 'link-point-left-bottom',
        isAnchorPoint: true
      });
    }

    x = Math.cos((18 + 72 * 4) / 180 * Math.PI) * outerR;
    y = Math.sin((18 + 72 * 4) / 180 * Math.PI) * outerR;

    if (markRightBottom) {
      if (!rightBottom && rightBottom !== undefined) {
        markLeftBottom.remove();
      } else {
        markRightBottom.attr((0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }));
      }
    } else if (rightBottom) {
      group.addShape('circle', {
        attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
          x: x,
          y: -y
        }),
        className: 'link-point-right-bottom',
        name: 'link-point-right-bottom',
        isAnchorPoint: true
      });
    }
  }
}, 'single-node');