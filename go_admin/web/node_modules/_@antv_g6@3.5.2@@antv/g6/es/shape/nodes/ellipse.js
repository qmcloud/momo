import { __assign, __rest } from "tslib";
import { mix } from '@antv/util';
import Global from '../../global';
import Shape from '../shape';
/**
 * 基本的椭圆，可以添加文本，默认文本居中
 */

Shape.registerNode('ellipse', {
  // 自定义节点时的配置
  options: {
    size: [120, 60],
    style: {
      x: 0,
      y: 0,
      stroke: Global.defaultShapeStrokeColor,
      fill: Global.defaultShapeFillColor,
      lineWidth: 1
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
      bottom: false,
      left: false,
      // circle的大小
      size: 3,
      lineWidth: 1,
      fill: '#72CC4A',
      stroke: '#72CC4A'
    },
    // 节点中icon配置
    icon: {
      // 是否显示icon，值为 false 则不渲染icon
      show: false,
      // icon的地址，字符串类型
      img: 'https://gw.alipayobjects.com/zos/basement_prod/012bcf4f-423b-4922-8c24-32a89f8c41ce.svg',
      width: 36,
      height: 36
    }
  },
  shapeType: 'ellipse',
  // 文本位置
  labelPosition: 'center',
  drawShape: function drawShape(cfg, group) {
    var defaultIcon = this.options.icon;
    var style = this.getShapeStyle(cfg);
    var icon = mix({}, defaultIcon, cfg.icon);
    var keyShape = group.addShape('ellipse', {
      attrs: style,
      className: 'ellipse-keyShape',
      name: 'ellipse-keyShape',
      draggable: true
    });
    var width = icon.width,
        height = icon.height,
        show = icon.show;

    if (show) {
      var image = group.addShape('image', {
        attrs: __assign({
          x: -width / 2,
          y: -height / 2
        }, icon),
        className: 'ellipse-icon',
        name: 'ellipse-icon',
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
    var linkPoints = mix({}, defaultLinkPoints, cfg.linkPoints);

    var top = linkPoints.top,
        left = linkPoints.left,
        right = linkPoints.right,
        bottom = linkPoints.bottom,
        markSize = linkPoints.size,
        markStyle = __rest(linkPoints, ["top", "left", "right", "bottom", "size"]);

    var size = this.getSize(cfg);
    var rx = size[0] / 2;
    var ry = size[1] / 2;

    if (left) {
      // left circle
      group.addShape('circle', {
        attrs: __assign(__assign({}, markStyle), {
          x: -rx,
          y: 0,
          r: markSize
        }),
        className: 'link-point-left',
        name: 'link-point-left',
        isAnchorPoint: true
      });
    }

    if (right) {
      // right circle
      group.addShape('circle', {
        attrs: __assign(__assign({}, markStyle), {
          x: rx,
          y: 0,
          r: markSize
        }),
        className: 'link-point-right',
        name: 'link-point-right',
        isAnchorPoint: true
      });
    }

    if (top) {
      // top circle
      group.addShape('circle', {
        attrs: __assign(__assign({}, markStyle), {
          x: 0,
          y: -ry,
          r: markSize
        }),
        className: 'link-point-top',
        name: 'link-point-top',
        isAnchorPoint: true
      });
    }

    if (bottom) {
      // bottom circle
      group.addShape('circle', {
        attrs: __assign(__assign({}, markStyle), {
          x: 0,
          y: ry,
          r: markSize
        }),
        className: 'link-point-bottom',
        name: 'link-point-bottom',
        isAnchorPoint: true
      });
    }
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

    var style = mix({}, defaultStyle, strokeStyle, cfg.style);
    var size = this.getSize(cfg);
    var rx = size[0] / 2;
    var ry = size[1] / 2;
    var styles = Object.assign({}, {
      x: 0,
      y: 0,
      rx: rx,
      ry: ry
    }, style);
    return styles;
  },
  update: function update(cfg, item) {
    var group = item.getContainer();
    var defaultStyle = this.options.style;
    var size = this.getSize(cfg);
    var strokeStyle = {
      stroke: cfg.color,
      rx: size[0] / 2,
      ry: size[1] / 2
    }; // 与 getShapeStyle 不同在于，update 时需要获取到当前的 style 进行融合。即新传入的配置项中没有涉及的属性，保留当前的配置。

    var keyShape = item.get('keyShape');
    var style = mix({}, defaultStyle, keyShape.attr(), strokeStyle);
    style = mix(style, cfg.style);
    this.updateShape(cfg, item, style, true);
    this.updateLinkPoints(cfg, group);
  }
}, 'single-node');