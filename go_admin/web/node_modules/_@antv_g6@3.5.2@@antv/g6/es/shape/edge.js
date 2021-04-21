/**
 * @fileOverview 自定义边
 * @description 自定义边中有大量逻辑同自定义节点重复，虽然可以提取成为 mixin ，但是考虑到代码的可读性，还是单独实现。
 * @author dxq613@gmail.com
 */
import { __assign } from "tslib";
import { deepMix, mix, each, isNil } from '@antv/util';
import { getLabelPosition, getLoopCfgs } from '../util/graphic';
import { distance, getCircleCenterByPoints } from '../util/math';
import { getControlPoint, getSpline } from '../util/path';
import Global from '../global';
import Shape from './shape';
import { shapeBase, CLS_LABEL_BG_SUFFIX } from './shapeBase';
import isArray from '@antv/util/lib/is-array';
import isNumber from '@antv/util/lib/is-number';
var CLS_SHAPE = 'edge-shape'; // start,end 倒置，center 不变

function revertAlign(labelPosition) {
  var textAlign = labelPosition;

  if (labelPosition === 'start') {
    textAlign = 'end';
  } else if (labelPosition === 'end') {
    textAlign = 'start';
  }

  return textAlign;
}

var singleEdge = {
  itemType: 'edge',

  /**
   * 文本的位置
   * @type {String}
   */
  labelPosition: 'center',

  /**
   * 文本的 x 偏移
   * @type {Number}
   */
  refX: 0,

  /**
   * 文本的 y 偏移
   * @type {Number}
   */
  refY: 0,

  /**
   * 文本是否跟着线自动旋转，默认 false
   * @type {Boolean}
   */
  labelAutoRotate: false,

  /**
   * 获取边的 path
   * @internal 供扩展的边覆盖
   * @param  {Array} points 构成边的点的集合
   * @return {Array} 构成 path 的数组
   */
  getPath: function getPath(points) {
    var path = [];
    each(points, function (point, index) {
      if (index === 0) {
        path.push(['M', point.x, point.y]);
      } else {
        path.push(['L', point.x, point.y]);
      }
    });
    return path;
  },
  getShapeStyle: function getShapeStyle(cfg) {
    var defaultStyle = this.options.style;
    var strokeStyle = {
      stroke: cfg.color
    }; // 如果设置了color，则覆盖默认的stroke属性

    var style = mix({}, defaultStyle, strokeStyle, cfg.style);
    var size = cfg.size || Global.defaultEdge.size;
    cfg = this.getPathPoints(cfg);
    var startPoint = cfg.startPoint,
        endPoint = cfg.endPoint;
    var controlPoints = this.getControlPoints(cfg);
    var points = [startPoint]; // 添加起始点
    // 添加控制点

    if (controlPoints) {
      points = points.concat(controlPoints);
    } // 添加结束点


    points.push(endPoint);
    var path = this.getPath(points);
    var styles = mix({}, Global.defaultEdge.style, {
      stroke: Global.defaultEdge.color,
      lineWidth: size,
      path: path
    }, style);
    return styles;
  },
  updateShapeStyle: function updateShapeStyle(cfg, item) {
    var group = item.getContainer();
    var strokeStyle = {
      stroke: cfg.color
    };
    var shape = group.find(function (element) {
      return element.get('className') === 'edge-shape';
    }) || item.getKeyShape();
    var size = cfg.size;
    cfg = this.getPathPoints(cfg);
    var startPoint = cfg.startPoint,
        endPoint = cfg.endPoint;
    var controlPoints = this.getControlPoints(cfg); // || cfg.controlPoints;

    var points = [startPoint]; // 添加起始点
    // 添加控制点

    if (controlPoints) {
      points = points.concat(controlPoints);
    } // 添加结束点


    points.push(endPoint);
    var previousStyle = mix({}, strokeStyle, shape.attr(), cfg.style);
    var source = cfg.sourceNode;
    var target = cfg.targetNode;
    var routeCfg = {
      radius: previousStyle.radius
    };

    if (!controlPoints) {
      routeCfg = {
        source: source,
        target: target,
        offset: previousStyle.offset,
        radius: previousStyle.radius
      };
    }

    var path = this.getPath(points, routeCfg);
    var style = mix(strokeStyle, shape.attr(), {
      lineWidth: size,
      path: path
    }, cfg.style);

    if (shape) {
      shape.attr(style);
    }
  },
  getLabelStyleByPosition: function getLabelStyleByPosition(cfg, labelCfg, group) {
    var labelPosition = labelCfg.position || this.labelPosition; // 文本的位置用户可以传入

    var style = {};
    var pathShape = group && group.find(function (element) {
      return element.get('className') === CLS_SHAPE;
    }); // 不对 pathShape 进行判空，如果线不存在，说明有问题了

    var pointPercent;

    if (labelPosition === 'start') {
      pointPercent = 0;
    } else if (labelPosition === 'end') {
      pointPercent = 1;
    } else {
      pointPercent = 0.5;
    } // 偏移量


    var offsetX = labelCfg.refX || this.refX;
    var offsetY = labelCfg.refY || this.refY; // 如果两个节点重叠，线就变成了一个点，这时候label的位置，就是这个点 + 绝对偏移

    if (cfg.startPoint.x === cfg.endPoint.x && cfg.startPoint.y === cfg.endPoint.y) {
      style.x = cfg.startPoint.x + offsetX;
      style.y = cfg.startPoint.y + offsetY;
      style.text = cfg.label;
      return style;
    }

    var autoRotate = isNil(labelCfg.autoRotate) ? this.labelAutoRotate : labelCfg.autoRotate;
    var offsetStyle = getLabelPosition(pathShape, pointPercent, offsetX, offsetY, autoRotate);
    style.x = offsetStyle.x;
    style.y = offsetStyle.y;
    style.rotate = offsetStyle.rotate;
    style.textAlign = this._getTextAlign(labelPosition, offsetStyle.angle);
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

    var padding = backgroundStyle.padding;
    var backgroundWidth = bbox.width + padding[1] + padding[3];
    var backgroundHeight = bbox.height + padding[0] + padding[2];
    var labelPosition = labelCfg.position || this.labelPosition;

    var style = __assign(__assign({}, backgroundStyle), {
      width: backgroundWidth,
      height: backgroundHeight,
      x: bbox.minX - padding[2],
      y: bbox.minY - padding[0],
      rotate: 0
    });

    var autoRotate = isNil(labelCfg.autoRotate) ? this.labelAutoRotate : labelCfg.autoRotate;
    var pathShape = group && group.find(function (element) {
      return element.get('className') === CLS_SHAPE;
    }); // 不对 pathShape 进行判空，如果线不存在，说明有问题了

    var pointPercent;

    if (labelPosition === 'start') {
      pointPercent = 0;
    } else if (labelPosition === 'end') {
      pointPercent = 1;
    } else {
      pointPercent = 0.5;
    } // 偏移量


    var offsetX = labelCfg.refX || this.refX;
    var offsetY = labelCfg.refY || this.refY; // // 如果两个节点重叠，线就变成了一个点，这时候label的位置，就是这个点 + 绝对偏移

    if (cfg.startPoint.x === cfg.endPoint.x && cfg.startPoint.y === cfg.endPoint.y) {
      style.x = cfg.startPoint.x + offsetX;
      style.y = cfg.startPoint.y + offsetY;
      return style;
    }

    var offsetStyle = getLabelPosition(pathShape, pointPercent, offsetX - backgroundWidth / 2, offsetY + backgroundHeight / 2, autoRotate);
    var rad = offsetStyle.angle;

    if (rad > 1 / 2 * Math.PI && rad < 3 * 1 / 2 * Math.PI) {
      offsetStyle = getLabelPosition(pathShape, pointPercent, offsetX + backgroundWidth / 2, offsetY + backgroundHeight / 2, autoRotate);
    }

    if (autoRotate) {
      style.x = offsetStyle.x;
      style.y = offsetStyle.y;
    }

    style.rotate = offsetStyle.rotate;
    return style;
  },
  // 获取文本对齐方式
  _getTextAlign: function _getTextAlign(labelPosition, angle) {
    var textAlign = 'center';

    if (!angle) {
      return labelPosition;
    }

    angle = angle % (Math.PI * 2); // 取模

    if (labelPosition !== 'center') {
      if (angle >= 0 && angle <= Math.PI / 2 || angle >= 3 / 2 * Math.PI && angle < 2 * Math.PI) {
        textAlign = labelPosition;
      } else {
        textAlign = revertAlign(labelPosition);
      }
    }

    return textAlign;
  },

  /**
   * @internal 获取边的控制点
   * @param  {Object} cfg 边的配置项
   * @return {Array} 控制点的数组
   */
  getControlPoints: function getControlPoints(cfg) {
    return cfg.controlPoints;
  },

  /**
   * @internal 处理需要重计算点和边的情况
   * @param {Object} cfg 边的配置项
   * @return {Object} 边的配置项
   */
  getPathPoints: function getPathPoints(cfg) {
    return cfg;
  },

  /**
   * 绘制边
   * @override
   * @param  {Object} cfg   边的配置项
   * @param  {G.Group} group 边的容器
   * @return {IShape} 图形
   */
  drawShape: function drawShape(cfg, group) {
    var shapeStyle = this.getShapeStyle(cfg);
    var shape = group.addShape('path', {
      className: CLS_SHAPE,
      name: CLS_SHAPE,
      attrs: shapeStyle
    });
    return shape;
  },
  drawLabel: function drawLabel(cfg, group) {
    var defaultLabelCfg = this.options.labelCfg;
    var labelCfg = deepMix({}, defaultLabelCfg, cfg.labelCfg);
    var labelStyle = this.getLabelStyle(cfg, labelCfg, group);
    var rotate = labelStyle.rotate;
    delete labelStyle.rotate;
    var label = group.addShape('text', {
      attrs: labelStyle,
      name: 'text-shape'
    });

    if (rotate) {
      label.rotateAtStart(rotate);
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
    var labelCfg = deepMix({}, defaultLabelCfg, cfg.labelCfg);
    var labelStyle = this.getLabelStyle(cfg, labelCfg, group);
    var rotate = labelStyle.rotate;
    var style = this.getLabelBgStyleByPosition(label, cfg, labelCfg, group);
    delete style.rotate;
    var rect = group.addShape('rect', {
      name: 'text-bg-shape',
      attrs: style
    });
    if (rotate) rect.rotateAtStart(rotate);
    return rect;
  }
};
var singleEdgeDef = Object.assign({}, shapeBase, singleEdge);
Shape.registerEdge('single-edge', singleEdgeDef); // 直线, 不支持控制点

Shape.registerEdge('line', {
  // 控制点不生效
  getControlPoints: function getControlPoints() {
    return undefined;
  }
}, 'single-edge'); // 直线

Shape.registerEdge('spline', {
  getPath: function getPath(points) {
    var path = getSpline(points);
    return path;
  }
}, 'single-edge');
Shape.registerEdge('arc', {
  curveOffset: 20,
  clockwise: 1,
  getControlPoints: function getControlPoints(cfg) {
    var startPoint = cfg.startPoint,
        endPoint = cfg.endPoint;
    var midPoint = {
      x: (startPoint.x + endPoint.x) / 2,
      y: (startPoint.y + endPoint.y) / 2
    };
    var center;
    var arcPoint; // 根据给定点计算圆弧

    if (cfg.controlPoints !== undefined) {
      arcPoint = cfg.controlPoints[0];
      center = getCircleCenterByPoints(startPoint, arcPoint, endPoint); // 根据控制点和直线关系决定 clockwise值

      if (startPoint.x <= endPoint.x && startPoint.y > endPoint.y) {
        this.clockwise = center.x > arcPoint.x ? 0 : 1;
      } else if (startPoint.x <= endPoint.x && startPoint.y < endPoint.y) {
        this.clockwise = center.x > arcPoint.x ? 1 : 0;
      } else if (startPoint.x > endPoint.x && startPoint.y <= endPoint.y) {
        this.clockwise = center.y < arcPoint.y ? 0 : 1;
      } else {
        this.clockwise = center.y < arcPoint.y ? 1 : 0;
      } // 若给定点和两端点共线，无法生成圆弧，绘制直线


      if ((arcPoint.x - startPoint.x) / (arcPoint.y - startPoint.y) === (endPoint.x - startPoint.x) / (endPoint.y - startPoint.y)) {
        return [];
      }
    } else {
      // 根据直线连线中点的的偏移计算圆弧
      // 若用户给定偏移量则根据其计算，否则按照默认偏移值计算
      if (cfg.curveOffset === undefined) {
        cfg.curveOffset = this.curveOffset;
      }

      if (isArray(cfg.curveOffset)) {
        cfg.curveOffset = cfg.curveOffset[0];
      }

      if (cfg.curveOffset < 0) {
        this.clockwise = 0;
      } else {
        this.clockwise = 1;
      }

      var vec = {
        x: endPoint.x - startPoint.x,
        y: endPoint.y - startPoint.y
      };
      var edgeAngle = Math.atan2(vec.y, vec.x);
      arcPoint = {
        x: cfg.curveOffset * Math.cos(-Math.PI / 2 + edgeAngle) + midPoint.x,
        y: cfg.curveOffset * Math.sin(-Math.PI / 2 + edgeAngle) + midPoint.y
      };
      center = getCircleCenterByPoints(startPoint, arcPoint, endPoint);
    }

    var radius = distance(startPoint, center);
    var controlPoints = [{
      x: radius,
      y: radius
    }];
    return controlPoints;
  },
  getPath: function getPath(points) {
    var path = [];
    path.push(['M', points[0].x, points[0].y]); // 控制点与端点共线

    if (points.length === 2) {
      path.push(['L', points[1].x, points[1].y]);
    } else {
      path.push(['A', points[1].x, points[1].y, 0, 0, this.clockwise, points[2].x, points[2].y]);
    }

    return path;
  }
}, 'single-edge');
Shape.registerEdge('quadratic', {
  curvePosition: 0.5,
  curveOffset: -20,
  getControlPoints: function getControlPoints(cfg) {
    var controlPoints = cfg.controlPoints; // 指定controlPoints

    if (!controlPoints || !controlPoints.length) {
      var startPoint = cfg.startPoint,
          endPoint = cfg.endPoint;
      if (cfg.curveOffset === undefined) cfg.curveOffset = this.curveOffset;
      if (cfg.curvePosition === undefined) cfg.curvePosition = this.curvePosition;
      if (isArray(this.curveOffset)) cfg.curveOffset = cfg.curveOffset[0];
      if (isArray(this.curvePosition)) cfg.curvePosition = cfg.curveOffset[0];
      var innerPoint = getControlPoint(startPoint, endPoint, cfg.curvePosition, cfg.curveOffset);
      controlPoints = [innerPoint];
    }

    return controlPoints;
  },
  getPath: function getPath(points) {
    var path = [];
    path.push(['M', points[0].x, points[0].y]);
    path.push(['Q', points[1].x, points[1].y, points[2].x, points[2].y]);
    return path;
  }
}, 'single-edge');
Shape.registerEdge('cubic', {
  curvePosition: [1 / 2, 1 / 2],
  curveOffset: [-20, 20],
  getControlPoints: function getControlPoints(cfg) {
    var controlPoints = cfg.controlPoints; // 指定controlPoints

    if (cfg.curveOffset === undefined) cfg.curveOffset = this.curveOffset;
    if (cfg.curvePosition === undefined) cfg.curvePosition = this.curvePosition;
    if (isNumber(cfg.curveOffset)) cfg.curveOffset = [cfg.curveOffset, -cfg.curveOffset];
    if (isNumber(cfg.curvePosition)) cfg.curvePosition = [cfg.curvePosition, 1 - cfg.curvePosition];

    if (!controlPoints || !controlPoints.length || controlPoints.length < 2) {
      var startPoint = cfg.startPoint,
          endPoint = cfg.endPoint;
      var innerPoint1 = getControlPoint(startPoint, endPoint, cfg.curvePosition[0], cfg.curveOffset[0]);
      var innerPoint2 = getControlPoint(startPoint, endPoint, cfg.curvePosition[1], cfg.curveOffset[1]);
      controlPoints = [innerPoint1, innerPoint2];
    }

    return controlPoints;
  },
  getPath: function getPath(points) {
    var path = [];
    path.push(['M', points[0].x, points[0].y]);
    path.push(['C', points[1].x, points[1].y, points[2].x, points[2].y, points[3].x, points[3].y]);
    return path;
  }
}, 'single-edge'); // 垂直方向的三阶贝塞尔曲线，不再考虑用户外部传入的控制点

Shape.registerEdge('cubic-vertical', {
  curvePosition: [1 / 2, 1 / 2],
  getControlPoints: function getControlPoints(cfg) {
    var startPoint = cfg.startPoint,
        endPoint = cfg.endPoint;
    if (cfg.curvePosition !== undefined) this.curvePosition = cfg.curvePosition;
    if (isNumber(this.curvePosition)) this.curvePosition = [this.curvePosition, 1 - this.curvePosition];
    var innerPoint1 = {
      x: startPoint.x,
      y: (endPoint.y - startPoint.y) * this.curvePosition[0] + startPoint.y
    };
    var innerPoint2 = {
      x: endPoint.x,
      y: (endPoint.y - startPoint.y) * this.curvePosition[1] + startPoint.y
    };
    var controlPoints = [innerPoint1, innerPoint2];
    return controlPoints;
  }
}, 'cubic'); // 水平方向的三阶贝塞尔曲线，不再考虑用户外部传入的控制点

Shape.registerEdge('cubic-horizontal', {
  curvePosition: [1 / 2, 1 / 2],
  getControlPoints: function getControlPoints(cfg) {
    var startPoint = cfg.startPoint,
        endPoint = cfg.endPoint;
    if (cfg.curvePosition !== undefined) this.curvePosition = cfg.curvePosition;
    if (isNumber(this.curvePosition)) this.curvePosition = [this.curvePosition, 1 - this.curvePosition];
    var innerPoint1 = {
      x: (endPoint.x - startPoint.x) * this.curvePosition[0] + startPoint.x,
      y: startPoint.y
    };
    var innerPoint2 = {
      x: (endPoint.x - startPoint.x) * this.curvePosition[1] + startPoint.x,
      y: endPoint.y
    };
    var controlPoints = [innerPoint1, innerPoint2];
    return controlPoints;
  }
}, 'cubic');
Shape.registerEdge('loop', {
  getPathPoints: function getPathPoints(cfg) {
    return getLoopCfgs(cfg);
  },
  getControlPoints: function getControlPoints(cfg) {
    return cfg.controlPoints;
  },
  afterDraw: function afterDraw(cfg) {
    cfg.controlPoints = undefined;
  },
  afterUpdate: function afterUpdate(cfg) {
    cfg.controlPoints = undefined;
  }
}, 'cubic');