import { __assign, __rest } from "tslib";
import { mix } from '@antv/util';
import Global from '../../global';
import Shape from '../shape'; // 菱形shape

Shape.registerNode('diamond', {
  // 自定义节点时的配置
  options: {
    size: [100, 100],
    style: {
      stroke: Global.defaultShapeStrokeColor,
      fill: Global.defaultShapeFillColor,
      lineWidth: Global.defaultNode.style.lineWidth
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
      width: 16,
      height: 16
    }
  },
  shapeType: 'circle',
  // 文本位置
  labelPosition: 'center',
  drawShape: function drawShape(cfg, group) {
    var defaultIcon = this.options.icon;
    var style = this.getShapeStyle(cfg);
    var icon = mix({}, defaultIcon, cfg.icon);
    var keyShape = group.addShape('path', {
      attrs: style,
      className: 'diamond-keyShape',
      name: 'diamond-keyShape',
      draggable: true
    });
    var w = icon.width,
        h = icon.height,
        show = icon.show;

    if (show) {
      var image = group.addShape('image', {
        attrs: __assign({
          x: -w / 2,
          y: -h / 2
        }, icon),
        className: 'diamond-icon',
        name: 'diamond-icon',
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
    var width = size[0];
    var height = size[1];

    if (left) {
      // left circle
      group.addShape('circle', {
        attrs: __assign(__assign({}, markStyle), {
          x: -width / 2,
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
          x: width / 2,
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
          y: -height / 2,
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
          y: height / 2,
          r: markSize
        }),
        className: 'link-point-bottom',
        name: 'link-point-bottom',
        isAnchorPoint: true
      });
    }
  },
  getPath: function getPath(cfg) {
    var size = this.getSize(cfg);
    var width = size[0];
    var height = size[1];
    var path = [['M', 0, -height / 2], ['L', width / 2, 0], ['L', 0, height / 2], ['L', -width / 2, 0], ['Z']];
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

    var style = mix({}, defaultStyle, strokeStyle, cfg.style);
    var path = this.getPath(cfg);

    var styles = __assign({
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
    var style = mix({}, defaultStyle, keyShape.attr(), strokeStyle);
    style = mix(style, cfg.style);
    this.updateShape(cfg, item, style, true);
    this.updateLinkPoints(cfg, group);
  }
}, 'single-node');