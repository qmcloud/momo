"use strict";

var _util = require("@antv/util");

var _path = require("../../util/path");

var _global = _interopRequireDefault(require("../../global"));

var _shape = _interopRequireDefault(require("../shape"));

var _polylineUtil = require("./polyline-util");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// 折线
_shape.default.registerEdge('polyline', {
  options: {
    color: _global.default.defaultEdge.color,
    style: {
      radius: 0,
      offset: 5,
      x: 0,
      y: 0
    },
    // 文本样式配置
    labelCfg: {
      style: {
        fill: '#595959'
      }
    }
  },
  shapeType: 'polyline',
  // 文本位置
  labelPosition: 'center',
  drawShape: function drawShape(cfg, group) {
    var shapeStyle = this.getShapeStyle(cfg);
    var keyShape = group.addShape('path', {
      className: 'edge-shape',
      name: 'edge-shape',
      attrs: shapeStyle
    });
    return keyShape;
  },
  getShapeStyle: function getShapeStyle(cfg) {
    var defaultStyle = this.options.style;
    var strokeStyle = {
      stroke: cfg.color
    };
    var style = (0, _util.mix)({}, defaultStyle, strokeStyle, cfg.style);
    cfg = this.getPathPoints(cfg);
    this.radius = style.radius;
    this.offset = style.offset;
    var startPoint = cfg.startPoint,
        endPoint = cfg.endPoint;
    var controlPoints = this.getControlPoints(cfg);
    var points = [startPoint]; // 添加起始点
    // 添加控制点

    if (controlPoints) {
      points = points.concat(controlPoints);
    } // 添加结束点


    points.push(endPoint);
    var source = cfg.sourceNode;
    var target = cfg.targetNode;
    var routeCfg = {
      radius: style.radius
    };

    if (!controlPoints) {
      routeCfg = {
        source: source,
        target: target,
        offset: style.offset,
        radius: style.radius
      };
    }

    var path = this.getPath(points, routeCfg);

    if ((0, _util.isArray)(path) && path.length <= 1 || (0, _util.isString)(path) && path.indexOf('L') === -1) {
      path = 'M0 0, L0 0';
    }

    if (isNaN(startPoint.x) || isNaN(startPoint.y) || isNaN(endPoint.x) || isNaN(endPoint.y)) {
      path = 'M0 0, L0 0';
    }

    var attrs = (0, _util.mix)({}, _global.default.defaultEdge.style, style, {
      lineWidth: cfg.size,
      path: path
    });
    return attrs;
  },
  getPath: function getPath(points, routeCfg) {
    var _a = routeCfg,
        source = _a.source,
        target = _a.target,
        offset = _a.offset,
        radius = _a.radius;

    if (!offset || points.length > 2) {
      if (radius) {
        return (0, _polylineUtil.getPathWithBorderRadiusByPolyline)(points, radius);
      }

      var pathArray_1 = [];
      (0, _util.each)(points, function (point, index) {
        if (index === 0) {
          pathArray_1.push(['M', point.x, point.y]);
        } else {
          pathArray_1.push(['L', point.x, point.y]);
        }
      });
      return pathArray_1;
    }

    var polylinePoints;

    if (radius) {
      polylinePoints = (0, _polylineUtil.simplifyPolyline)((0, _polylineUtil.getPolylinePoints)(points[0], points[points.length - 1], source, target, offset));
      return (0, _polylineUtil.getPathWithBorderRadiusByPolyline)(polylinePoints, radius);
    }

    polylinePoints = (0, _polylineUtil.getPolylinePoints)(points[0], points[points.length - 1], source, target, offset);
    var res = (0, _path.pointsToPolygon)(polylinePoints);
    return res;
  }
}, 'single-edge');