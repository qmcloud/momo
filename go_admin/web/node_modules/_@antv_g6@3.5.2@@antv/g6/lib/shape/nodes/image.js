"use strict";

var _tslib = require("tslib");

var _shape = _interopRequireDefault(require("../shape"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * 基本的图片，可以添加文本，默认文本在图片的下面
 */
_shape.default.registerNode('image', {
  options: {
    img: 'https://gw.alipayobjects.com/mdn/rms_f8c6a0/afts/img/A*eD7nT6tmYgAAAAAAAAAAAABkARQnAQ',
    size: 200,
    clipCfg: {
      show: false,
      type: 'circle',
      // circle
      r: 50,
      // ellipse
      rx: 50,
      ry: 35,
      // rect
      width: 50,
      height: 35,
      // polygon
      points: [[30, 12], [12, 30], [30, 48], [48, 30]],
      // path
      path: [['M', 25, 25], ['L', 50, 25], ['A', 12.5, 12.5, 0, 1, 1, 50, 50], ['A', 12.5, 12.5, 0, 1, 0, 50, 50], ['L', 25, 75], ['Z']],
      // 坐标
      x: 0,
      y: 0
    }
  },
  shapeType: 'image',
  labelPosition: 'bottom',
  drawShape: function drawShape(cfg, group) {
    var shapeType = this.shapeType; // || this.type，都已经加了 shapeType

    var style = this.getShapeStyle(cfg);
    delete style.fill;
    var shape = group.addShape(shapeType, {
      attrs: style,
      className: 'image-keyShape',
      name: 'image-keyShape',
      draggable: true
    });
    this.drawClip(cfg, shape);
    return shape;
  },
  drawClip: function drawClip(cfg, shape) {
    var clip = Object.assign({}, this.options.clipCfg, cfg.clipCfg);

    if (!clip.show) {
      return;
    } // 支持 circle、rect、ellipse、Polygon 及自定义 path clip


    var type = clip.type,
        x = clip.x,
        y = clip.y,
        style = clip.style;

    if (type === 'circle') {
      var r = clip.r;
      shape.setClip({
        type: 'circle',
        attrs: (0, _tslib.__assign)({
          r: r,
          x: x,
          y: y
        }, style)
      });
    } else if (type === 'rect') {
      var width = clip.width,
          height = clip.height;
      var rectX = x - width / 2;
      var rectY = y - height / 2;
      shape.setClip({
        type: 'rect',
        attrs: (0, _tslib.__assign)({
          x: rectX,
          y: rectY,
          width: width,
          height: height
        }, style)
      });
    } else if (type === 'ellipse') {
      var rx = clip.rx,
          ry = clip.ry;
      shape.setClip({
        type: 'ellipse',
        attrs: (0, _tslib.__assign)({
          x: x,
          y: y,
          rx: rx,
          ry: ry
        }, style)
      });
    } else if (type === 'polygon') {
      var points = clip.points;
      shape.setClip({
        type: 'polygon',
        attrs: (0, _tslib.__assign)({
          points: points
        }, style)
      });
    } else if (type === 'path') {
      var path = clip.path;
      shape.setClip({
        type: 'path',
        attrs: (0, _tslib.__assign)({
          path: path
        }, style)
      });
    }
  },
  getShapeStyle: function getShapeStyle(cfg) {
    var size = this.getSize(cfg);
    var img = cfg.img || this.options.img;
    var width = size[0];
    var height = size[1];

    if (cfg.style) {
      width = cfg.style.width || size[0];
      height = cfg.style.height || size[1];
    }

    var style = Object.assign({}, {
      x: -width / 2,
      y: -height / 2,
      width: width,
      height: height,
      img: img
    }, cfg.style);
    return style;
  },
  updateShapeStyle: function updateShapeStyle(cfg, item) {
    var group = item.getContainer();
    var shapeClassName = this.itemType + "-shape";
    var shape = group.find(function (element) {
      return element.get('className') === shapeClassName;
    }) || item.getKeyShape();
    var shapeStyle = this.getShapeStyle(cfg);

    if (shape) {
      shape.attr(shapeStyle);
    }
  }
}, 'single-node');