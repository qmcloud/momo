import { __assign, __rest } from "tslib";
import deepMix from '@antv/util/lib/deep-mix';
import { mix, isString } from '@antv/util';
import Shape from '../shape';
import Global from '../../global';
Shape.registerNode('modelRect', {
  // 自定义节点时的配置
  options: {
    size: [185, 70],
    style: {
      radius: 5,
      stroke: '#69c0ff',
      fill: '#ffffff',
      lineWidth: Global.defaultNode.style.lineWidth,
      fillOpacity: 1
    },
    // 文本样式配置
    labelCfg: {
      style: {
        fill: '#595959',
        fontSize: 14
      },
      offset: 30
    },
    descriptionCfg: {
      style: {
        fontSize: 12,
        fill: '#bfbfbf'
      },
      paddingTop: 0
    },
    preRect: {
      show: true,
      width: 4,
      fill: '#40a9ff',
      radius: 2
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
    logoIcon: {
      // 是否显示icon，值为 false 则不渲染icon
      show: true,
      x: 0,
      y: 0,
      // icon的地址，字符串类型
      img: 'https://gw.alipayobjects.com/zos/basement_prod/4f81893c-1806-4de4-aff3-9a6b266bc8a2.svg',
      width: 16,
      height: 16,
      // 用于调整图标的左右位置
      offset: 0
    },
    // 节点中表示状态的icon配置
    stateIcon: {
      // 是否显示icon，值为 false 则不渲染icon
      show: true,
      x: 0,
      y: 0,
      // icon的地址，字符串类型
      img: 'https://gw.alipayobjects.com/zos/basement_prod/300a2523-67e0-4cbf-9d4a-67c077b40395.svg',
      width: 16,
      height: 16,
      // 用于调整图标的左右位置
      offset: -5
    },
    // 连接点，默认为左右
    // anchorPoints: [{ x: 0, y: 0.5 }, { x: 1, y: 0.5 }]
    anchorPoints: [[0, 0.5], [1, 0.5]]
  },
  shapeType: 'modelRect',
  drawShape: function drawShape(cfg, group) {
    var defaultPreRect = this.options.preRect;
    var style = this.getShapeStyle(cfg);
    var size = this.getSize(cfg);
    var width = size[0];
    var height = size[1];
    var keyShape = group.addShape('rect', {
      attrs: style,
      className: 'modelRect-keyShape',
      name: 'modelRect-keyShape',
      draggable: true
    });
    var preRect = mix({}, defaultPreRect, cfg.preRect);

    var preRectShow = preRect.show,
        preRectStyle = __rest(preRect, ["show"]);

    if (preRectShow) {
      group.addShape('rect', {
        attrs: __assign({
          x: -width / 2,
          y: -height / 2,
          height: height
        }, preRectStyle),
        className: 'pre-rect',
        name: 'pre-rect',
        draggable: true
      });
    }

    this.drawLogoIcon(cfg, group);
    this.drawStateIcon(cfg, group);
    this.drawLinkPoints(cfg, group);
    return keyShape;
  },

  /**
   * 绘制模型矩形左边的logo图标
   * @param {Object} cfg 数据配置项
   * @param {Group} group Group实例
   */
  drawLogoIcon: function drawLogoIcon(cfg, group) {
    var defaultLogoIcon = this.options.logoIcon;
    var logoIcon = mix({}, defaultLogoIcon, cfg.logoIcon);
    var size = this.getSize(cfg);
    var width = size[0];

    if (logoIcon.show) {
      var w = logoIcon.width,
          h = logoIcon.height,
          x = logoIcon.x,
          y = logoIcon.y,
          offset = logoIcon.offset,
          logoIconStyle = __rest(logoIcon, ["width", "height", "x", "y", "offset"]);

      group.addShape('image', {
        attrs: __assign(__assign({}, logoIconStyle), {
          x: x || -width / 2 + w + offset,
          y: y || -h / 2,
          width: w,
          height: h
        }),
        className: 'rect-logo-icon',
        name: 'rect-logo-icon',
        draggable: true
      });
    }
  },

  /**
   * 绘制模型矩形右边的状态图标
   * @param {Object} cfg 数据配置项
   * @param {Group} group Group实例
   */
  drawStateIcon: function drawStateIcon(cfg, group) {
    var defaultStateIcon = this.options.stateIcon;
    var stateIcon = mix({}, defaultStateIcon, cfg.stateIcon);
    var size = this.getSize(cfg);
    var width = size[0];

    if (stateIcon.show) {
      var w = stateIcon.width,
          h = stateIcon.height,
          x = stateIcon.x,
          y = stateIcon.y,
          offset = stateIcon.offset,
          iconStyle = __rest(stateIcon, ["width", "height", "x", "y", "offset"]);

      var image = group.addShape('image', {
        attrs: __assign(__assign({}, iconStyle), {
          x: x || width / 2 - w + offset,
          y: y || -h / 2,
          width: w,
          height: h
        }),
        className: 'rect-state-icon',
        name: 'rect-state-icon',
        draggable: true
      });
    }
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
  drawLabel: function drawLabel(cfg, group) {
    var _a = this.options,
        defaultLabelCfg = _a.labelCfg,
        defaultLogoIcon = _a.logoIcon,
        defaultDescritionCfg = _a.descriptionCfg;
    var logoIcon = mix({}, defaultLogoIcon, cfg.logoIcon);
    var labelCfg = deepMix({}, defaultLabelCfg, cfg.labelCfg);
    var descriptionCfg = deepMix({}, defaultDescritionCfg, cfg.descriptionCfg);
    var size = this.getSize(cfg);
    var width = size[0];
    var label = null;
    var show = logoIcon.show,
        w = logoIcon.width;
    var offsetX = -width / 2 + labelCfg.offset;

    if (show) {
      offsetX = -width / 2 + w + labelCfg.offset;
    }

    var fontStyle = labelCfg.style;
    var descriptionStyle = descriptionCfg.style,
        descriptionPaddingTop = descriptionCfg.paddingTop;

    if (isString(cfg.description)) {
      label = group.addShape('text', {
        attrs: __assign(__assign({}, fontStyle), {
          x: offsetX,
          y: -5,
          text: cfg.label
        }),
        className: 'text-shape',
        name: 'text-shape',
        draggable: true
      });
      group.addShape('text', {
        attrs: __assign(__assign({}, descriptionStyle), {
          x: offsetX,
          y: 17 + descriptionPaddingTop,
          text: cfg.description
        }),
        className: 'rect-description',
        name: 'rect-description',
        draggable: true
      });
    } else {
      label = group.addShape('text', {
        attrs: __assign(__assign({}, fontStyle), {
          x: offsetX,
          y: 7,
          text: cfg.label
        }),
        draggable: true
      });
    }

    return label;
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
    var width = style.width || size[0];
    var height = style.height || size[1];
    var styles = Object.assign({}, {
      x: -width / 2,
      y: -height / 2,
      width: width,
      height: height
    }, style);
    return styles;
  },
  update: function update(cfg, item) {
    var _a = this.options,
        defaultStyle = _a.style,
        defaultLabelCfg = _a.labelCfg,
        defaultDescritionCfg = _a.descriptionCfg;
    var style = mix({}, defaultStyle, cfg.style);
    var size = this.getSize(cfg);
    var width = size[0];
    var height = size[1];
    var keyShape = item.get('keyShape');
    keyShape.attr(__assign(__assign({}, style), {
      x: -width / 2,
      y: -height / 2,
      width: width,
      height: height
    }));
    var group = item.getContainer();
    var labelCfg = deepMix({}, defaultLabelCfg, cfg.labelCfg);
    var logoIconShape = group.find(function (element) {
      return element.get('className') === 'rect-logo-icon';
    });
    var currentLogoIconAttr = logoIconShape ? logoIconShape.attr() : {};
    var logoIcon = mix({}, currentLogoIconAttr, cfg.logoIcon);
    var w = logoIcon.width;

    if (w === undefined) {
      w = this.options.logoIcon.width;
    }

    var show = cfg.logoIcon ? cfg.logoIcon.show : undefined;
    var offset = labelCfg.offset;
    var offsetX = -width / 2 + w + offset;

    if (!show && show !== undefined) {
      offsetX = -width / 2 + offset;
    }

    var label = group.find(function (element) {
      return element.get('className') === 'node-label';
    });
    var description = group.find(function (element) {
      return element.get('className') === 'rect-description';
    });

    if (cfg.label) {
      if (!label) {
        group.addShape('text', {
          attrs: __assign(__assign({}, labelCfg.style), {
            x: offsetX,
            y: cfg.description ? -5 : 7,
            text: cfg.label
          }),
          className: 'node-label',
          name: 'node-label',
          draggable: true
        });
      } else {
        var cfgStyle = cfg.labelCfg ? cfg.labelCfg.style : {};
        var labelStyle = mix({}, label.attr(), cfgStyle);
        if (cfg.label) labelStyle.text = cfg.label;
        labelStyle.x = offsetX;
        if (isString(cfg.description)) labelStyle.y = -5;

        if (description) {
          description.resetMatrix();
          description.attr({
            x: offsetX
          });
        }

        label.resetMatrix();
        label.attr(labelStyle);
      }
    }

    if (isString(cfg.description)) {
      var descriptionCfg = deepMix({}, defaultDescritionCfg, cfg.descriptionCfg);
      var paddingTop = descriptionCfg.paddingTop;

      if (!description) {
        group.addShape('text', {
          attrs: __assign(__assign({}, descriptionCfg.style), {
            x: offsetX,
            y: 17 + paddingTop,
            text: cfg.description
          }),
          className: 'rect-description',
          name: 'rect-description',
          draggable: true
        });
      } else {
        var cfgStyle = cfg.descriptionCfg ? cfg.descriptionCfg.style : {};
        var descriptionStyle = mix({}, description.attr(), cfgStyle);
        if (isString(cfg.description)) descriptionStyle.text = cfg.description;
        descriptionStyle.x = offsetX;
        description.resetMatrix();
        description.attr(__assign(__assign({}, descriptionStyle), {
          y: 17 + paddingTop
        }));
      }
    }

    var preRectShape = group.find(function (element) {
      return element.get('className') === 'pre-rect';
    });

    if (preRectShape) {
      var preRect = mix({}, preRectShape.attr(), cfg.preRect);
      preRectShape.attr(__assign(__assign({}, preRect), {
        x: -width / 2,
        y: -height / 2,
        height: height
      }));
    }

    if (logoIconShape) {
      if (!show && show !== undefined) {
        logoIconShape.remove();
      } else {
        var logoW = logoIcon.width,
            h = logoIcon.height,
            x = logoIcon.x,
            y = logoIcon.y,
            logoOffset = logoIcon.offset,
            logoIconStyle = __rest(logoIcon, ["width", "height", "x", "y", "offset"]);

        logoIconShape.attr(__assign(__assign({}, logoIconStyle), {
          x: x || -width / 2 + logoW + logoOffset,
          y: y || -h / 2,
          width: logoW,
          height: h
        }));
      }
    } else if (show) {
      this.drawLogoIcon(cfg, group);
    }

    var stateIconShape = group.find(function (element) {
      return element.get('className') === 'rect-state-icon';
    });
    var currentStateIconAttr = stateIconShape ? stateIconShape.attr() : {};
    var stateIcon = mix({}, currentStateIconAttr, cfg.stateIcon);

    if (stateIconShape) {
      if (!stateIcon.show && stateIcon.show !== undefined) {
        stateIconShape.remove();
      }

      var stateW = stateIcon.width,
          h = stateIcon.height,
          x = stateIcon.x,
          y = stateIcon.y,
          stateOffset = stateIcon.offset,
          stateIconStyle = __rest(stateIcon, ["width", "height", "x", "y", "offset"]);

      stateIconShape.attr(__assign(__assign({}, stateIconStyle), {
        x: x || width / 2 - stateW + stateOffset,
        y: y || -h / 2,
        width: stateW,
        height: h
      }));
    } else if (stateIcon.show) {
      this.drawStateIcon(cfg, group);
    }

    this.updateLinkPoints(cfg, group);
  }
}, 'single-node');