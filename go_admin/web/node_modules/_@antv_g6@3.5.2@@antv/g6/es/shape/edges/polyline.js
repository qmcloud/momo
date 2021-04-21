import { mix, each, isArray, isString } from '@antv/util';
import { pointsToPolygon } from '../../util/path';
import Global from '../../global';
import Shape from '../shape';
import { getPathWithBorderRadiusByPolyline, getPolylinePoints, simplifyPolyline } from './polyline-util'; // 折线

Shape.registerEdge('polyline', {
  options: {
    color: Global.defaultEdge.color,
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
    var style = mix({}, defaultStyle, strokeStyle, cfg.style);
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

    if (isArray(path) && path.length <= 1 || isString(path) && path.indexOf('L') === -1) {
      path = 'M0 0, L0 0';
    }

    if (isNaN(startPoint.x) || isNaN(startPoint.y) || isNaN(endPoint.x) || isNaN(endPoint.y)) {
      path = 'M0 0, L0 0';
    }

    var attrs = mix({}, Global.defaultEdge.style, style, {
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
        return getPathWithBorderRadiusByPolyline(points, radius);
      }

      var pathArray_1 = [];
      each(points, function (point, index) {
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
      polylinePoints = simplifyPolyline(getPolylinePoints(points[0], points[points.length - 1], source, target, offset));
      return getPathWithBorderRadiusByPolyline(polylinePoints, radius);
    }

    polylinePoints = getPolylinePoints(points[0], points[points.length - 1], source, target, offset);
    var res = pointsToPolygon(polylinePoints);
    return res;
  }
}, 'single-edge');