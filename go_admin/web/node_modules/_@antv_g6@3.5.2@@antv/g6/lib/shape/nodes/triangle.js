"use strict";

var _tslib = require("tslib");

var _shape = _interopRequireDefault(require("../shape"));

var _util = require("@antv/util");

var _global = _interopRequireDefault(require("../../global"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// 菱形shape
_shape.default.registerNode('triangle', {
  // 自定义节点时的配置
  options: {
    size: 40,
    direction: 'up',
    style: {
      stroke: _global.default.defaultShapeStrokeColor,
      fill: _global.default.defaultShapeFillColor,
      lineWidth: _global.default.defaultNode.style.lineWidth
    },
    // 文本样式配置
    labelCfg: {
      style: {
        fill: '#595959'
      },
      offset: 15
    },
    // 节点上左右上下四个方向上的链接circle配置
    linkPoints: {
      top: false,
      right: false,
      bottom: false,
      left: false,
      // circle的大小
      size: 5,
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
      height: 16,
      offset: 6
    }
  },
  shapeType: 'triangle',
  // 文本位置
  labelPosition: 'bottom',
  drawShape: function drawShape(cfg, group) {
    var _a = this.options,
        defaultIcon = _a.icon,
        defaultDirection = _a.direction;
    var style = this.getShapeStyle(cfg);
    var icon = (0, _util.mix)({}, defaultIcon, cfg.icon);
    var direction = cfg.direction || defaultDirection;
    var keyShape = group.addShape('path', {
      attrs: style,
      className: 'triangle-keyShape',
      name: 'triangle-keyShape',
      draggable: true
    });
    var w = icon.width,
        h = icon.height,
        show = icon.show,
        offset = icon.offset;

    if (show) {
      var iconW = -w / 2;
      var iconH = -h / 2;

      if (direction === 'up' || direction === 'down') {
        iconH += offset;
      }

      if (direction === 'left' || direction === 'right') {
        iconW += offset;
      }

      var image = group.addShape('image', {
        attrs: (0, _tslib.__assign)({
          x: iconW,
          y: iconH
        }, icon),
        className: 'triangle-icon',
        name: 'triangle-icon',
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
    var _a = this.options,
        defaultLinkPoints = _a.linkPoints,
        defaultDirection = _a.direction;
    var linkPoints = (0, _util.mix)({}, defaultLinkPoints, cfg.linkPoints);
    var direction = cfg.direction || defaultDirection;
    var top = linkPoints.top,
        left = linkPoints.left,
        right = linkPoints.right,
        bottom = linkPoints.bottom,
        markSize = linkPoints.size,
        markStyle = (0, _tslib.__rest)(linkPoints, ["top", "left", "right", "bottom", "size"]);
    var size = this.getSize(cfg);
    var len = size[0];

    if (left) {
      // up down left right 四个方向的坐标均不相同
      var leftPos = null;
      var diffY = len * Math.sin(1 / 3 * Math.PI);
      var r = len * Math.sin(1 / 3 * Math.PI);

      if (direction === 'up') {
        leftPos = [-r, diffY];
      } else if (direction === 'down') {
        leftPos = [-r, -diffY];
      } else if (direction === 'left') {
        leftPos = [-r, r - diffY];
      }

      if (leftPos) {
        // left circle
        group.addShape('circle', {
          attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, markStyle), {
            x: leftPos[0],
            y: leftPos[1],
            r: markSize
          }),
          className: 'link-point-left',
          name: 'link-point-left'
        });
      }
    }

    if (right) {
      // right circle
      // up down left right 四个方向的坐标均不相同
      var rightPos = null;
      var diffY = len * Math.sin(1 / 3 * Math.PI);
      var r = len * Math.sin(1 / 3 * Math.PI);

      if (direction === 'up') {
        rightPos = [r, diffY];
      } else if (direction === 'down') {
        rightPos = [r, -diffY];
      } else if (direction === 'right') {
        rightPos = [r, r - diffY];
      }

      if (rightPos) {
        group.addShape('circle', {
          attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, markStyle), {
            x: rightPos[0],
            y: rightPos[1],
            r: markSize
          }),
          className: 'link-point-right',
          name: 'link-point-right'
        });
      }
    }

    if (top) {
      // up down left right 四个方向的坐标均不相同
      var topPos = null;
      var diffY = len * Math.sin(1 / 3 * Math.PI);
      var r = len * Math.sin(1 / 3 * Math.PI);

      if (direction === 'up') {
        topPos = [r - diffY, -diffY];
      } else if (direction === 'left') {
        topPos = [r, -diffY];
      } else if (direction === 'right') {
        topPos = [-r, -diffY];
      }

      if (topPos) {
        // top circle
        group.addShape('circle', {
          attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, markStyle), {
            x: topPos[0],
            y: topPos[1],
            r: markSize
          }),
          className: 'link-point-top',
          name: 'link-point-top'
        });
      }
    }

    if (bottom) {
      // up down left right 四个方向的坐标均不相同
      var bottomPos = null;
      var diffY = len * Math.sin(1 / 3 * Math.PI);
      var r = len * Math.sin(1 / 3 * Math.PI);

      if (direction === 'down') {
        bottomPos = [-r + diffY, diffY];
      } else if (direction === 'left') {
        bottomPos = [r, diffY];
      } else if (direction === 'right') {
        bottomPos = [-r, diffY];
      }

      if (bottomPos) {
        // bottom circle
        group.addShape('circle', {
          attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, markStyle), {
            x: bottomPos[0],
            y: bottomPos[1],
            r: markSize
          }),
          className: 'link-point-bottom',
          name: 'link-point-bottom'
        });
      }
    }
  },
  getPath: function getPath(cfg) {
    var defaultDirection = this.options.direction;
    var direction = cfg.direction || defaultDirection;
    var size = this.getSize(cfg);
    var len = size[0];
    var diffY = len * Math.sin(1 / 3 * Math.PI);
    var r = len * Math.sin(1 / 3 * Math.PI);
    var path = [['M', -r, diffY], ['L', 0, -diffY], ['L', r, diffY], ['Z']];

    if (direction === 'down') {
      path = [['M', -r, -diffY], ['L', r, -diffY], ['L', 0, diffY], ['Z']];
    } else if (direction === 'left') {
      path = [['M', -r, r - diffY], ['L', r, -r], ['L', r, r], ['Z']];
    } else if (direction === 'right') {
      path = [['M', r, r - diffY], ['L', -r, r], ['L', -r, -r], ['Z']];
    }

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
    }; // 如果设置了color，则覆盖默认的stroke属性

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
    var _a = this.options,
        defaultLinkPoints = _a.linkPoints,
        defaultDirection = _a.direction;
    var direction = cfg.direction || defaultDirection;
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
    var currentLinkPoints = defaultLinkPoints;
    var existLinkPoint = markLeft || markRight || markTop || markBottom;

    if (existLinkPoint) {
      currentLinkPoints = existLinkPoint.attr();
    }

    var linkPoints = (0, _util.mix)({}, currentLinkPoints, cfg.linkPoints);
    var markFill = linkPoints.fill,
        markStroke = linkPoints.stroke,
        borderWidth = linkPoints.lineWidth;
    var markSize = linkPoints.size;
    if (!markSize) markSize = linkPoints.r;

    var _b = cfg.linkPoints ? cfg.linkPoints : {
      left: undefined,
      right: undefined,
      top: undefined,
      bottom: undefined
    },
        left = _b.left,
        right = _b.right,
        top = _b.top,
        bottom = _b.bottom;

    var size = this.getSize(cfg);
    var len = size[0];
    var styles = {
      r: markSize,
      fill: markFill,
      stroke: markStroke,
      lineWidth: borderWidth
    };
    var leftPos = null;
    var diffY = len * Math.sin(1 / 3 * Math.PI);
    var r = len * Math.sin(1 / 3 * Math.PI);

    if (direction === 'up') {
      leftPos = [-r, diffY];
    } else if (direction === 'down') {
      leftPos = [-r, -diffY];
    } else if (direction === 'left') {
      leftPos = [-r, r - diffY];
    }

    if (leftPos) {
      if (markLeft) {
        if (!left && left !== undefined) {
          markLeft.remove();
        } else {
          markLeft.attr((0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
            x: leftPos[0],
            y: leftPos[1]
          }));
        }
      } else if (left) {
        group.addShape('circle', {
          attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
            x: leftPos[0],
            y: leftPos[1]
          }),
          className: 'link-point-left',
          name: 'link-point-left',
          isAnchorPoint: true
        });
      }
    }

    var rightPos = null;

    if (direction === 'up') {
      rightPos = [r, diffY];
    } else if (direction === 'down') {
      rightPos = [r, -diffY];
    } else if (direction === 'right') {
      rightPos = [r, r - diffY];
    }

    if (rightPos) {
      if (markRight) {
        if (!right && right !== undefined) {
          markRight.remove();
        } else {
          markRight.attr((0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
            x: rightPos[0],
            y: rightPos[1]
          }));
        }
      } else if (right) {
        group.addShape('circle', {
          attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
            x: rightPos[0],
            y: rightPos[1]
          }),
          className: 'link-point-right',
          name: 'link-point-right',
          isAnchorPoint: true
        });
      }
    }

    var topPos = null;

    if (direction === 'up') {
      topPos = [r - diffY, -diffY];
    } else if (direction === 'left') {
      topPos = [r, -diffY];
    } else if (direction === 'right') {
      topPos = [-r, -diffY];
    }

    if (topPos) {
      if (markTop) {
        if (!top && top !== undefined) {
          markTop.remove();
        } else {
          // top circle
          markTop.attr((0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
            x: topPos[0],
            y: topPos[1]
          }));
        }
      } else if (top) {
        group.addShape('circle', {
          attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
            x: topPos[0],
            y: topPos[1]
          }),
          className: 'link-point-top',
          name: 'link-point-top',
          isAnchorPoint: true
        });
      }
    }

    var bottomPos = null;

    if (direction === 'down') {
      bottomPos = [-r + diffY, diffY];
    } else if (direction === 'left') {
      bottomPos = [r, diffY];
    } else if (direction === 'right') {
      bottomPos = [-r, diffY];
    }

    if (bottomPos) {
      if (markBottom) {
        if (!bottom && bottom !== undefined) {
          markBottom.remove();
        } else {
          markBottom.attr((0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
            x: bottomPos[0],
            y: bottomPos[1]
          }));
        }
      } else if (bottom) {
        group.addShape('circle', {
          attrs: (0, _tslib.__assign)((0, _tslib.__assign)({}, styles), {
            x: bottomPos[0],
            y: bottomPos[1]
          }),
          className: 'link-point-bottom',
          name: 'link-point-bottom',
          isAnchorPoint: true
        });
      }
    }
  }
}, 'single-node');