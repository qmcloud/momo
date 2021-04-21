import { __assign, __rest } from "tslib";
import deepMix from '@antv/util/lib/deep-mix';
import isString from '@antv/util/lib/is-string';
import { traverseTree } from '../../util/graphic';
var treeGroup = null;

var CustomGroup =
/** @class */
function () {
  function CustomGroup(graph) {
    this.graph = graph;
    var groupStyle = graph.get('groupStyle');
    this.styles = deepMix({}, CustomGroup.getDefaultCfg(), groupStyle); // 创建的群组集合

    this.customGroup = {};
    this.delegateInGroup = {};
    this.nodePoint = [];
    this.destroyed = false;
  }

  CustomGroup.getDefaultCfg = function () {
    return {
      default: {
        lineWidth: 1,
        stroke: '#A3B1BF',
        // lineDash: [ 5, 5 ],
        strokeOpacity: 0.9,
        fill: '#F3F9FF',
        fillOpacity: 0.8,
        opacity: 0.8,
        disCoefficient: 0.6,
        minDis: 30,
        maxDis: 30
      },
      hover: {
        stroke: '#faad14',
        fill: '#ffe58f',
        fillOpacity: 0.3,
        opacity: 0.3,
        lineWidth: 3
      },
      // 收起状态样式
      collapse: {
        r: 30,
        width: 80,
        height: 40,
        // lineDash: [ 5, 5 ],
        stroke: '#A3B1BF',
        lineWidth: 3,
        fill: '#F3F9FF',
        offsetX: -15,
        offsetY: 5
      },
      icon: 'https://gw.alipayobjects.com/zos/rmsportal/MXXetJAxlqrbisIuZxDO.svg',
      operatorBtn: {
        collapse: {
          img: 'https://gw.alipayobjects.com/zos/rmsportal/uZVdwjJGqDooqKLKtvGA.svg',
          width: 16,
          height: 16
        },
        expand: {
          width: 16,
          height: 16,
          img: 'https://gw.alipayobjects.com/zos/rmsportal/MXXetJAxlqrbisIuZxDO.svg'
        }
      },
      visible: false
    };
  };
  /**
   * 生成群组
   * @param {string} groupId 群组ID
   * @param {array} nodes 群组中的节点集合
   * @param {string} type 群组类型，默认为circle，支持rect
   * @param {number} zIndex 群组层级，默认为0
   * @param {boolean} updateDataModel 是否更新节点数据，默认为false，只有当手动创建group时才为true
   * @param {object} title 分组标题配置
   * @memberof ItemGroup
   * @return {object} null
   */


  CustomGroup.prototype.create = function (groupId, nodes, type, zIndex, updateDataModel, title) {
    if (type === void 0) {
      type = 'circle';
    }

    if (zIndex === void 0) {
      zIndex = 0;
    }

    if (updateDataModel === void 0) {
      updateDataModel = false;
    }

    if (title === void 0) {
      title = {};
    }

    var graph = this.graph;
    var customGroup = graph.get('customGroup');
    var hasGroupIds = customGroup.get('children').map(function (data) {
      return data.get('id');
    });

    if (hasGroupIds.indexOf(groupId) > -1) {
      // eslint-disable-next-line no-console
      console.warn("\u5DF2\u7ECF\u5B58\u5728ID\u4E3A " + groupId + " \u7684\u5206\u7EC4\uFF0C\u8BF7\u91CD\u65B0\u8BBE\u7F6E\u5206\u7EC4ID\uFF01");
      return;
    }

    var nodeGroup = customGroup.addGroup({
      id: groupId,
      zIndex: zIndex
    });
    var defaultStyle = this.styles.default; // 计算群组左上角左边、宽度、高度及x轴方向上的最大值

    var _a = this.calculationGroupPosition(nodes),
        x = _a.x,
        y = _a.y,
        width = _a.width,
        height = _a.height,
        maxX = _a.maxX;

    var paddingValue = this.getGroupPadding(groupId);
    var groupBBox = graph.get('groupBBoxs');
    groupBBox[groupId] = {
      x: x,
      y: y,
      width: width,
      height: height,
      maxX: maxX
    }; // 根据groupId获取group数据，判断是否需要添加title

    var groupTitle = null; // 只有手动创建group时执行以下逻辑

    if (updateDataModel) {
      var groups = graph.get('groups'); // 如果是手动创建group，则原始数据中是没有groupId信息的，需要将groupId添加到node中

      nodes.forEach(function (nodeId) {
        var node = graph.findById(nodeId);
        var model = node.getModel();

        if (!model.groupId) {
          model.groupId = groupId;
        }
      }); // 如果是手动创建 group，则将 group 也添加到 groups 中

      if (!groups.find(function (data) {
        return data.id === groupId;
      })) {
        groups.push({
          id: groupId,
          title: title
        });
        graph.set({
          groups: groups
        });
      }
    }

    var groupData = graph.get('groups').filter(function (data) {
      return data.id === groupId;
    });

    if (groupData && groupData.length > 0) {
      groupTitle = groupData[0].title;
    } // group title 坐标


    var titleX = 0;
    var titleY = 0; // step 1：绘制群组外框

    var keyShape = null;

    if (type === 'circle') {
      var r = width > height ? width / 2 : height / 2;
      var cx = (width + 2 * x) / 2;
      var cy = (height + 2 * y) / 2;
      var lastR = r + paddingValue;
      keyShape = nodeGroup.addShape('circle', {
        attrs: __assign(__assign({}, defaultStyle), {
          x: cx,
          y: cy,
          r: lastR
        }),
        draggable: true,
        capture: true,
        zIndex: zIndex,
        groupId: groupId,
        name: 'circle-group-shape'
      });
      titleX = cx;
      titleY = cy - lastR; // 更新群组及属性样式

      this.setDeletageGroupByStyle(groupId, nodeGroup, {
        width: width,
        height: height,
        x: cx,
        y: cy,
        r: lastR
      });
    } else {
      var rectPadding = paddingValue * defaultStyle.disCoefficient;
      keyShape = nodeGroup.addShape('rect', {
        attrs: __assign(__assign({}, defaultStyle), {
          x: x - rectPadding,
          y: y - rectPadding,
          width: width + rectPadding * 2,
          height: height + rectPadding * 2
        }),
        draggable: true,
        capture: true,
        zIndex: zIndex,
        groupId: groupId,
        name: 'rect-group-shape'
      });
      titleX = x - rectPadding + 15;
      titleY = y - rectPadding + 15; // 更新群组及属性样式

      this.setDeletageGroupByStyle(groupId, nodeGroup, {
        x: x - rectPadding,
        y: y - rectPadding,
        width: width + rectPadding,
        height: height + rectPadding,
        btnOffset: maxX - 3
      });
    } // 添加group标题


    if (groupTitle) {
      var _b = groupTitle.offsetX,
          offsetX = _b === void 0 ? 0 : _b,
          _c = groupTitle.offsetY,
          offsetY = _c === void 0 ? 0 : _c,
          _d = groupTitle.text,
          text = _d === void 0 ? groupTitle : _d,
          titleStyle = __rest(groupTitle, ["offsetX", "offsetY", "text"]);

      var textShape = nodeGroup.addShape('text', {
        attrs: __assign({
          text: text,
          stroke: '#444',
          x: titleX + offsetX,
          y: titleY + offsetY
        }, titleStyle),
        className: 'group-title',
        name: 'group-title-shape'
      });
      textShape.set('capture', false);
    }

    nodeGroup.set('keyShape', keyShape); // 设置graph中groupNodes的值

    graph.get('groupNodes')[groupId] = nodes;
  };
  /**
   * 修改Group样式
   * @param {Item} keyShape 群组的keyShape
   * @param {Object | String} style 样式
   */


  CustomGroup.prototype.setGroupStyle = function (keyShape, style) {
    if (!keyShape || keyShape.get('destroyed')) {
      return;
    }

    var styles = {};
    var _a = this.styles,
        hoverStyle = _a.hover,
        defaultStyle = _a.default;

    if (isString(style)) {
      if (style === 'default') {
        styles = deepMix({}, defaultStyle);
      } else if (style === 'hover') {
        styles = deepMix({}, hoverStyle);
      }
    } else {
      styles = deepMix({}, defaultStyle, style);
    }

    Object.keys(styles).forEach(function (s) {
      keyShape.attr(s, styles[s]);
    });
  };
  /**
   * 根据GroupID计算群组位置，包括左上角左边及宽度和高度
   *
   * @param {object} nodes 符合条件的node集合：选中的node或具有同一个groupID的node
   * @param {object} position delegate的坐标位置
   * @return {object} 根据节点计算出来的包围盒坐标
   * @memberof ItemGroup
   */


  CustomGroup.prototype.calculationGroupPosition = function (nodes, position) {
    if (position === void 0) {
      position = {
        x: 100,
        y: 100
      };
    } // hxy 可新增无节点group，适用于图编辑场景


    if (nodes.length === 0) {
      // 防止空group 无法计算大小
      return {
        x: position.x,
        y: position.y,
        width: 100,
        height: 100
      };
    }

    var graph = this.graph;
    var minx = Infinity;
    var maxx = -Infinity;
    var miny = Infinity;
    var maxy = -Infinity; // 获取已节点的所有最大最小x y值

    nodes.forEach(function (id) {
      var element = isString(id) ? graph.findById(id) : id;
      var bbox = element.getBBox();
      var minX = bbox.minX,
          minY = bbox.minY,
          maxX = bbox.maxX,
          maxY = bbox.maxY;

      if (minX < minx) {
        minx = minX;
      }

      if (minY < miny) {
        miny = minY;
      }

      if (maxX > maxx) {
        maxx = maxX;
      }

      if (maxY > maxy) {
        maxy = maxY;
      }
    });
    var x = Math.floor(minx);
    var y = Math.floor(miny);
    var width = Math.ceil(maxx) - x;
    var height = Math.ceil(maxy) - y;
    return {
      x: x,
      y: y,
      width: width,
      height: height,
      maxX: Math.ceil(maxx)
    };
  };
  /**
  * 扁平的数据格式转成树形
  * @param {array} data 扁平结构的数据
  * @param {string} value 树状结构的唯一标识
  * @param {string} parentId 父节点的键值
  * @return {array} 转成的树形结构数据
  */


  CustomGroup.prototype.flatToTree = function (data, value, parentId) {
    if (value === void 0) {
      value = 'id';
    }

    if (parentId === void 0) {
      parentId = 'parentId';
    }

    var children = 'children';
    var valueMap = [];
    var tree = [];
    data.forEach(function (v) {
      valueMap[v[value]] = v;
    });
    data.forEach(function (v) {
      var parent = valueMap[v[parentId]];

      if (parent) {
        !parent[children] && (parent[children] = []);
        parent[children].push(v);
      } else {
        tree.push(v);
      }
    });
    return tree;
  };
  /**
   * 当group中含有group时，获取padding值
   * @param {string} groupId 节点分组ID
   * @return {number} 在x和y方向上的偏移值
   */


  CustomGroup.prototype.getGroupPadding = function (groupId) {
    var graph = this.graph;
    var defaultStyle = this.styles.default; // 检测操作的群组中是否包括子群组

    var groups = graph.get('groups'); // 计算每个 groupId 包含的组的数量

    var currentGroups = groups.filter(function (g) {
      return g.parentId === groupId;
    });
    var count = 1;

    if (currentGroups.length > 0) {
      if (!treeGroup) {
        treeGroup = this.flatToTree(groups);
      }

      traverseTree(treeGroup[0], function (param) {
        if (param.parentId === groupId && param.children) {
          count += param.children.length;
          return true;
        }
      });
    }

    var big = groups.filter(function (g) {
      return g.id === groupId && !g.parentId;
    });

    if (big.length > 0) {
      count += 1;
    }

    var hasSubGroup = !!(groups.filter(function (g) {
      return g.parentId === groupId;
    }).length > 0);
    var paddingValue = hasSubGroup ? defaultStyle.maxDis + (count > 1 ? count / 2 : 1) * 30 : defaultStyle.minDis;
    return paddingValue;
  };
  /**
   * 设置群组对象及属性值
   *
   * @param {string} groupId 群组ID
   * @param {Group} deletage 群组元素
   * @param {object} property 属性值，里面包括width、height和maxX
   * @memberof ItemGroup
   */


  CustomGroup.prototype.setDeletageGroupByStyle = function (groupId, deletage, property) {
    var width = property.width,
        height = property.height,
        x = property.x,
        y = property.y,
        r = property.r,
        btnOffset = property.btnOffset;
    var customGroupStyle = this.customGroup[groupId];

    if (!customGroupStyle) {
      // 首次赋值
      this.customGroup[groupId] = {
        nodeGroup: deletage,
        groupStyle: {
          width: width,
          height: height,
          x: x,
          y: y,
          r: r,
          btnOffset: btnOffset
        }
      };
    } else {
      // 更新时候merge配置项
      var groupStyle = customGroupStyle.groupStyle;
      var styles = deepMix({}, groupStyle, property);
      this.customGroup[groupId] = {
        nodeGroup: deletage,
        groupStyle: styles
      };
    }
  };
  /**
   * 根据群组ID获取群组及属性对象
   *
   * @param {string} groupId 群组ID
   * @return {Item} 群组
   * @memberof ItemGroup
   */


  CustomGroup.prototype.getDeletageGroupById = function (groupId) {
    return this.customGroup[groupId];
  };
  /**
   * 收起和展开群组
   * @param {string} groupId 群组ID
   */


  CustomGroup.prototype.collapseExpandGroup = function (groupId) {
    var customGroup = this.getDeletageGroupById(groupId);
    var nodeGroup = customGroup.nodeGroup;
    var hasHidden = nodeGroup.get('hasHidden'); // 该群组已经处于收起状态，需要展开

    if (hasHidden) {
      nodeGroup.set('hasHidden', false);
      this.expandGroup(groupId);
    } else {
      nodeGroup.set('hasHidden', true);
      this.collapseGroup(groupId);
    }
  };
  /**
   * 将临时节点递归地设置到groupId及父节点上
   * @param {string} groupId 群组ID
   * @param {string} tmpNodeId 临时节点ID
   */


  CustomGroup.prototype.setGroupTmpNode = function (groupId, tmpNodeId) {
    var graph = this.graph;
    var graphNodes = graph.get('groupNodes');
    var groups = graph.get('groups');

    if (graphNodes[groupId].indexOf(tmpNodeId) < 0) {
      graphNodes[groupId].push(tmpNodeId);
    } // 获取groupId的父群组


    var parentGroup = groups.filter(function (g) {
      return g.id === groupId;
    });
    var parentId = null;

    if (parentGroup.length > 0) {
      // eslint-disable-next-line prefer-destructuring
      parentId = parentGroup[0].parentId;
    } // 如果存在父群组，则把临时元素也添加到父群组中


    if (parentId) {
      this.setGroupTmpNode(parentId, tmpNodeId);
    }
  };
  /**
   * 收起群组，隐藏群组中的节点及边，群组外部相邻的边都连接到群组上
   *
   * @param {string} id 群组ID
   * @memberof ItemGroup
   */


  CustomGroup.prototype.collapseGroup = function (id) {
    var _this = this;

    var customGroup = this.getDeletageGroupById(id);
    var nodeGroup = customGroup.nodeGroup; // 收起群组后的默认样式

    var collapse = this.styles.collapse;
    var graph = this.graph;
    var groupType = graph.get('groupType');
    var nodesInGroup = graph.get('groupNodes')[id]; // 更新Group的大小

    var keyShape = nodeGroup.get('keyShape');

    var r = collapse.r,
        width = collapse.width,
        height = collapse.height,
        offsetX = collapse.offsetX,
        offsetY = collapse.offsetY,
        otherStyle = __rest(collapse, ["r", "width", "height", "offsetX", "offsetY"]);

    Object.keys(otherStyle).forEach(function (style) {
      keyShape.attr(style, otherStyle[style]);
    });
    var options = {
      groupId: id,
      id: id + "-custom-node",
      x: keyShape.attr('x'),
      y: keyShape.attr('y'),
      style: {
        r: r
      },
      type: 'circle'
    };
    var titleShape = nodeGroup.find(function (element) {
      return element.get('className') === 'group-title';
    }); // 收起群组时候动画

    if (groupType === 'circle') {
      keyShape.animate({
        r: r
      }, {
        duration: 500,
        easing: 'easeCubic'
      });

      if (titleShape) {
        titleShape.attr({
          x: keyShape.attr('x') + offsetX,
          y: keyShape.attr('y') + offsetY
        });
      }
    } else if (groupType === 'rect') {
      keyShape.animate({
        width: width,
        height: height
      }, {
        duration: 500,
        easing: 'easeCubic'
      });

      if (titleShape) {
        titleShape.attr({
          x: keyShape.attr('x') + 10,
          y: keyShape.attr('y') + height / 2 + 5
        });
      }

      options = {
        groupId: id,
        id: id + "-custom-node",
        x: keyShape.attr('x') + width / 2,
        y: keyShape.attr('y') + height / 2,
        size: [width, height],
        type: 'rect'
      };
    }

    var edges = graph.getEdges(); // 获取所有source在群组外，target在群组内的边

    var sourceOutTargetInEdges = edges.filter(function (edge) {
      var model = edge.getModel();
      return !nodesInGroup.includes(model.source) && nodesInGroup.includes(model.target);
    }); // 获取所有source在群组外，target在群组内的边

    var sourceInTargetOutEdges = edges.filter(function (edge) {
      var model = edge.getModel();
      return nodesInGroup.includes(model.source) && !nodesInGroup.includes(model.target);
    }); // 获取群组中节点之间的所有边

    var edgeAllInGroup = edges.filter(function (edge) {
      var model = edge.getModel();
      return nodesInGroup.includes(model.source) && nodesInGroup.includes(model.target);
    }); // 隐藏群组中的所有节点

    nodesInGroup.forEach(function (nodeId) {
      var node = graph.findById(nodeId);
      var model = node.getModel();
      var groupId = model.groupId;

      if (groupId && groupId !== id) {
        // 存在群组，则隐藏
        var currentGroup = _this.getDeletageGroupById(groupId);

        var currentNodeGroup = currentGroup.nodeGroup;
        currentNodeGroup.hide();
      }

      node.hide();
    });
    edgeAllInGroup.forEach(function (edge) {
      var source = edge.getSource();
      var target = edge.getTarget();

      if (source.isVisible() && target.isVisible()) {
        edge.show();
      } else {
        edge.hide();
      }
    }); // 群组中存在source和target其中有一个在群组内，一个在群组外的情况

    if (sourceOutTargetInEdges.length > 0 || sourceInTargetOutEdges.length > 0) {
      var delegateNode = graph.add('node', options);
      delegateNode.set('capture', false);
      delegateNode.hide();
      this.delegateInGroup[id] = {
        delegateNode: delegateNode
      }; // 将临时添加的节点加入到群组中，以便拖动节点时候线跟着拖动

      this.setGroupTmpNode(id, id + "-custom-node");
      this.updateEdgeInGroupLinks(id, sourceOutTargetInEdges, sourceInTargetOutEdges);
    }
  };
  /**
   * 收起群组时生成临时的节点，用于连接群组外的节点
   *
   * @param {string} groupId 群组ID
   * @param {array} sourceOutTargetInEdges 出度的边
   * @param {array} sourceInTargetOutEdges 入度的边
   * @memberof ItemGroup
   */


  CustomGroup.prototype.updateEdgeInGroupLinks = function (groupId, sourceOutTargetInEdges, sourceInTargetOutEdges) {
    var graph = this.graph; // 更新source在外的节点

    var edgesOuts = {};
    sourceOutTargetInEdges.map(function (edge) {
      var model = edge.getModel();
      var id = edge.get('id');
      var target = model.target;
      edgesOuts[id] = target;
      graph.updateItem(edge, {
        target: groupId + "-custom-node"
      });
      return true;
    }); // 更新target在外的节点

    var edgesIn = {};
    sourceInTargetOutEdges.map(function (edge) {
      var model = edge.getModel();
      var id = edge.get('id');
      var source = model.source;
      edgesIn[id] = source;
      graph.updateItem(edge, {
        source: groupId + "-custom-node"
      });
      return true;
    }); // 缓存群组groupId下的edge和临时生成的node节点

    this.delegateInGroup[groupId] = deepMix({
      sourceOutTargetInEdges: sourceOutTargetInEdges,
      sourceInTargetOutEdges: sourceInTargetOutEdges,
      edgesOuts: edgesOuts,
      edgesIn: edgesIn
    }, this.delegateInGroup[groupId]);
  };
  /**
   * 展开群组，恢复群组中的节点及边
   *
   * @param {string} id 群组ID
   * @memberof ItemGroup
   */


  CustomGroup.prototype.expandGroup = function (id) {
    var _this = this;

    var graph = this.graph;
    var groupType = graph.get('groupType'); // 显示之前隐藏的节点和群组

    var nodesInGroup = graph.get('groupNodes')[id];
    var noCustomNodes = nodesInGroup.filter(function (node) {
      return node.indexOf('custom-node') === -1;
    });

    var _a = this.calculationGroupPosition(noCustomNodes),
        width = _a.width,
        height = _a.height;

    var nodeGroup = this.getDeletageGroupById(id).nodeGroup;
    var keyShape = nodeGroup.get('keyShape');
    var _b = this.styles,
        defaultStyle = _b.default,
        collapse = _b.collapse;
    Object.keys(defaultStyle).forEach(function (style) {
      keyShape.attr(style, defaultStyle[style]);
    });
    var titleShape = nodeGroup.find(function (element) {
      return element.get('className') === 'group-title';
    }); // 检测操作的群组中是否包括子群组

    var paddingValue = this.getGroupPadding(id);

    if (groupType === 'circle') {
      var r = width > height ? width / 2 : height / 2;
      keyShape.animate({
        r: r + paddingValue
      }, {
        duration: 500,
        easing: 'easeCubic'
      });
    } else if (groupType === 'rect') {
      var w = collapse.width,
          h = collapse.height;
      keyShape.animate({
        width: w + width + paddingValue * defaultStyle.disCoefficient * 2,
        height: h + height + paddingValue * defaultStyle.disCoefficient * 2
      }, {
        duration: 500,
        easing: 'easeCubic'
      });
    }

    if (titleShape) {
      // 根据groupId获取group数据，判断是否需要添加title
      var groupTitle = null;
      var groupData = graph.get('groups').filter(function (data) {
        return data.id === id;
      });

      if (groupData && groupData.length > 0) {
        groupTitle = groupData[0].title;
      }

      var _c = groupTitle.offsetX,
          offsetX = _c === void 0 ? 0 : _c,
          _d = groupTitle.offsetY,
          offsetY = _d === void 0 ? 0 : _d;

      if (groupType === 'circle') {
        titleShape.animate({
          x: keyShape.attr('x') + offsetX,
          y: keyShape.attr('y') - keyShape.attr('r') + offsetY
        }, {
          duration: 600,
          easing: 'easeCubic'
        });
      } else if (groupType === 'rect') {
        titleShape.animate({
          x: keyShape.attr('x') + (15 + offsetX),
          y: keyShape.attr('y') + (15 + offsetY)
        }, {
          duration: 600,
          easing: 'easeCubic'
        });
      }
    } // 群组动画一会后再显示节点和边


    setTimeout(function () {
      nodesInGroup.forEach(function (nodeId) {
        var node = graph.findById(nodeId);
        var model = node.getModel();
        var groupId = model.groupId;

        if (groupId && groupId !== id) {
          // 存在群组，则显示
          var currentGroup = _this.getDeletageGroupById(groupId);

          var currentNodeGroup = currentGroup.nodeGroup;
          currentNodeGroup.show();
          var hasHidden = currentNodeGroup.get('hasHidden');

          if (!hasHidden) {
            node.show();
          }
        } else {
          node.show();
        }
      });
      var edges = graph.getEdges(); // 获取群组中节点之间的所有边

      var edgeAllInGroup = edges.filter(function (edge) {
        var model = edge.getModel();
        return nodesInGroup.includes(model.source) || nodesInGroup.includes(model.target);
      });
      edgeAllInGroup.forEach(function (edge) {
        var source = edge.getSource();
        var target = edge.getTarget();

        if (source.isVisible() && target.isVisible()) {
          edge.show();
        }
      });
    }, 300);
    var delegates = this.delegateInGroup[id];

    if (delegates) {
      var sourceOutTargetInEdges = delegates.sourceOutTargetInEdges,
          sourceInTargetOutEdges = delegates.sourceInTargetOutEdges,
          edgesOuts_1 = delegates.edgesOuts,
          edgesIn_1 = delegates.edgesIn,
          delegateNode = delegates.delegateNode; // 恢复source在外的节点

      sourceOutTargetInEdges.map(function (edge) {
        var edgeId = edge.get('id');
        var sourceOuts = edgesOuts_1[edgeId];
        graph.updateItem(edge, {
          target: sourceOuts
        });
        return true;
      }); // 恢复target在外的节点

      sourceInTargetOutEdges.map(function (edge) {
        var edgeId = edge.get('id');
        var sourceIn = edgesIn_1[edgeId];
        graph.updateItem(edge, {
          source: sourceIn
        });
        return true;
      }); // 删除群组中的临时节点ID

      var tmpNodeModel = delegateNode.getModel();
      this.deleteTmpNode(id, tmpNodeModel.id);
      graph.remove(delegateNode);
      delete this.delegateInGroup[id];
    }
  };

  CustomGroup.prototype.deleteTmpNode = function (groupId, tmpNodeId) {
    var graph = this.graph;
    var groups = graph.get('groups');
    var nodesInGroup = graph.get('groupNodes')[groupId];
    var index = nodesInGroup.indexOf(tmpNodeId);
    nodesInGroup.splice(index, 1); // 获取groupId的父群组

    var parentGroup = groups.filter(function (g) {
      return g.id === groupId;
    });
    var parentId = null;

    if (parentGroup.length > 0) {
      // eslint-disable-next-line prefer-destructuring
      parentId = parentGroup[0].parentId;
    } // 如果存在父群组，则把临时元素也添加到父群组中


    if (parentId) {
      this.deleteTmpNode(parentId, tmpNodeId);
    }
  };
  /**
   * 删除节点分组
   * @param {string} groupId 节点分组ID
   * @memberof ItemGroup
   */


  CustomGroup.prototype.remove = function (groupId) {
    var graph = this.graph;
    var customGroup = this.getDeletageGroupById(groupId);

    if (!customGroup) {
      // eslint-disable-next-line no-console
      //console.warn(`请确认输入的groupId ${groupId} 是否有误！`);
      return;
    }

    var nodeGroup = customGroup.nodeGroup;
    var groupNodes = graph.get('groupNodes');
    var nodes = groupNodes[groupId]; // 删除原群组中node中的groupID

    nodes.forEach(function (nodeId) {
      var node = graph.findById(nodeId);
      var model = node.getModel();
      var gId = model.groupId;

      if (!gId) {
        return;
      }

      if (groupId === gId) {
        delete model.groupId; // 使用没有groupID的数据更新节点

        graph.updateItem(node, model);
      }
    });
    nodeGroup.destroy(); // 删除customGroup中groupId的数据

    delete this.customGroup[groupId]; // 删除groups数据中的groupId

    var groups = graph.get('groups');

    if (groups.length > 0) {
      var filterGroup = groups.filter(function (group) {
        return group.id !== groupId;
      });
      graph.set('groups', filterGroup);
    }

    var parentGroupId = null;
    var parentGroupData = null;

    for (var i = 0; i < groups.length; i++) {
      var group = groups[i];

      if (groupId === group.id) {
        parentGroupId = group.parentId;
        parentGroupData = group;
        break;
      }
    }

    if (parentGroupData) {
      delete parentGroupData.parentId;
    } // 删除groupNodes中的groupId数据


    delete groupNodes[groupId];

    if (parentGroupId) {
      groupNodes[parentGroupId] = groupNodes[parentGroupId].filter(function (node) {
        return !nodes.includes(node);
      });
    }
  };
  /**
   * 更新节点分组位置及里面的节点和边的位置
   * @param {string} groupId 节点分组ID
   * @param {object} position delegate的坐标位置
   */


  CustomGroup.prototype.updateGroup = function (groupId, position, originPosition) {
    var graph = this.graph;
    var groupType = graph.get('groupType'); // 更新群组里面节点和线的位置

    this.updateItemInGroup(groupId, position, originPosition); // 判断是否拖动出了parent group外面，如果拖出了parent Group外面，则更新数据，去掉group关联
    // 获取groupId的父Group的ID

    var groups = graph.save().groups;
    var parentGroupId = null;
    var parentGroupData = null;

    for (var i = 0; i < groups.length; i++) {
      var group = groups[i];

      if (groupId === group.id) {
        parentGroupId = group.parentId;
        parentGroupData = group;
        break;
      }
    }

    if (parentGroupId) {
      var parentGroup = this.getDeletageGroupById(parentGroupId).nodeGroup; // const parentGroup = customGroup[parentGroupId].nodeGroup;

      var parentKeyShape = parentGroup.get('keyShape');
      this.setGroupStyle(parentKeyShape, 'default');
      var parentGroupBBox = parentKeyShape.getBBox();
      var minX = parentGroupBBox.minX,
          minY = parentGroupBBox.minY,
          maxX = parentGroupBBox.maxX,
          maxY = parentGroupBBox.maxY; // 检查是否拖出了父Group

      var currentGroup = this.getDeletageGroupById(groupId).nodeGroup; // const currentGroup = customGroup[groupId].nodeGroup;

      var currentKeyShape = currentGroup.get('keyShape');
      var currentKeyShapeBBox = currentKeyShape.getBBox();
      var x = currentKeyShapeBBox.x,
          y = currentKeyShapeBBox.y;

      if (!(x < maxX && x > minX && y < maxY && y > minY)) {
        // 拖出了parent group，则取消parent group ID
        delete parentGroupData.parentId; // 同时删除groupID中的节点

        var groupNodes = graph.get('groupNodes');
        var currentGroupNodes_1 = groupNodes[groupId];
        var parentGroupNodes = groupNodes[parentGroupId];
        groupNodes[parentGroupId] = parentGroupNodes.filter(function (node) {
          return currentGroupNodes_1.indexOf(node) === -1;
        });

        var _a = this.calculationGroupPosition(groupNodes[parentGroupId]),
            x1 = _a.x,
            y1 = _a.y,
            width = _a.width,
            height = _a.height; // x: x1, y: y1,
        // const { x: x1, y: y1 } = originPosition;


        var paddingValue = this.getGroupPadding(parentGroupId);
        var groupTitleShape = parentGroup.find(function (element) {
          return element.get('className') === 'group-title';
        });
        var titleX = 0;
        var titleY = 0;

        if (groupType === 'circle') {
          var r = width > height ? width / 2 : height / 2;
          var cx = x1;
          var cy = y1;
          parentKeyShape.attr({
            r: r + paddingValue,
            x: cx,
            y: cy
          });
          titleX = cx;
          titleY = cy - parentKeyShape.attr('r');
        } else if (groupType === 'rect') {
          var defaultStyle = this.styles.default;
          var rectPadding = paddingValue * defaultStyle.disCoefficient;
          parentKeyShape.attr({
            x: x1 - rectPadding,
            y: y1 - rectPadding
          });
          titleX = x1 - rectPadding + 15;
          titleY = y1 - rectPadding + 15;
        }

        if (groupTitleShape) {
          var titleConfig = parentGroupData.title;
          var offsetX = 0;
          var offsetY = 0;

          if (titleConfig) {
            offsetX = titleConfig.offsetX; // eslint-disable-line prefer-destructuring

            offsetY = titleConfig.offsetY; // eslint-disable-line prefer-destructuring
          }

          groupTitleShape.attr({
            x: titleX + offsetX,
            y: titleY + offsetY
          });
        }
      }
    }
  };
  /**
   * 更新节点分组中节点和边的位置
   * @param {string} groupId 节点分组ID
   * @param {object} position delegate的坐标位置
   */
  // eslint-disable-next-line @typescript-eslint/no-unused-vars


  CustomGroup.prototype.updateItemInGroup = function (groupId, position, originPosition) {
    var _this = this;

    var graph = this.graph;
    var groupType = graph.get('groupType');
    var groupNodes = graph.get('groupNodes'); // step 1：先修改groupId中的节点位置

    var nodeInGroup = groupNodes[groupId];
    var nodeGroup = this.getDeletageGroupById(groupId).nodeGroup;
    var originBBox = nodeGroup.getBBox();
    var otherGroupId = [];
    nodeInGroup.forEach(function (nodeId, index) {
      var node = graph.findById(nodeId);
      var model = node.getModel();
      var nodeGroupId = model.groupId;

      if (nodeGroupId && !otherGroupId.includes(nodeGroupId)) {
        otherGroupId.push(nodeGroupId);
      }

      if (!_this.nodePoint[index]) {
        _this.nodePoint[index] = {
          x: model.x,
          y: model.y
        };
      } // 群组拖动后节点的位置：deletateShape的最终位置-群组起始位置+节点位置


      var x = position.x - originBBox.x + _this.nodePoint[index].x;
      var y = position.y - originBBox.y + _this.nodePoint[index].y;
      _this.nodePoint[index] = {
        x: x,
        y: y
      };
      graph.updateItem(node, {
        x: x,
        y: y
      });
    }); // step 2：修改父group中其他节点的位置
    // otherGroupId中是否包括当前groupId，如果不包括，则添加进去

    if (!otherGroupId.includes(groupId)) {
      otherGroupId.push(groupId);
    } // 更新完群组位置后，重新设置群组起始位置


    otherGroupId.forEach(function (id) {
      // 更新群组位置
      var othergroup = _this.getDeletageGroupById(id).nodeGroup;

      var groupKeyShape = othergroup.get('keyShape');
      var noCustomNodes = groupNodes[id].filter(function (node) {
        return node.indexOf('custom-node') === -1;
      });

      var _a = _this.calculationGroupPosition(noCustomNodes, position),
          x = _a.x,
          y = _a.y,
          width = _a.width,
          height = _a.height;

      var titleX = 0;
      var titleY = 0;

      if (groupType === 'circle') {
        var cx = (width + 2 * x) / 2;
        var cy = (height + 2 * y) / 2;
        groupKeyShape.attr({
          x: cx,
          y: cy
        });
        titleX = cx;
        titleY = cy - groupKeyShape.attr('r');
      } else if (groupType === 'rect') {
        // 节点分组状态
        var hasHidden = othergroup.get('hasHidden');

        var paddingValue = _this.getGroupPadding(id);

        var keyshapePosition = {};
        var defaultStyle = _this.styles.default;
        var rectPadding = paddingValue * defaultStyle.disCoefficient;
        titleX = x - rectPadding + 15;
        titleY = y - rectPadding + 15;

        if (hasHidden) {
          // 无标题，或节点分组是展开的情况
          keyshapePosition = {
            x: x - rectPadding,
            y: y - rectPadding
          };
          titleY = titleY + 10;
        } else {
          keyshapePosition = {
            x: x - rectPadding,
            y: y - rectPadding,
            width: width + rectPadding * 2,
            height: height + rectPadding * 2
          };
        }

        groupKeyShape.attr(keyshapePosition);
      } // 如果存在标题，则更新标题位置


      _this.updateGroupTitle(othergroup, id, titleX, titleY);
    });
  };
  /**
   * 更新节点分组的 Title
   * @param {Group} group 当前 Group 实例
   * @param {string} groupId 分组ID
   * @param {number} x x坐标
   * @param {number} y y坐标
   */


  CustomGroup.prototype.updateGroupTitle = function (group, groupId, x, y) {
    var graph = this.graph;
    var groupTitleShape = group.find(function (element) {
      return element.get('className') === 'group-title';
    });

    if (groupTitleShape) {
      var titleConfig = null;
      var groupData = graph.get('groups').filter(function (data) {
        return data.id === groupId;
      });

      if (groupData && groupData.length > 0) {
        titleConfig = groupData[0].title;
      }

      var offsetX = 0;
      var offsetY = 0;

      if (titleConfig) {
        offsetX = titleConfig.offsetX || 0;
        offsetY = titleConfig.offsetY || 0;
      }

      groupTitleShape.attr({
        x: x + offsetX,
        y: y + offsetY
      });
    }
  };
  /**
   * 拖动节点时候动态改变节点分组大小
   * @param {Event} evt 事件句柄
   * @param {Group} currentGroup 当前操作的群组
   * @param {Item} keyShape 当前操作的keyShape
   * @description 节点拖入拖出后动态改变群组大小
   */


  CustomGroup.prototype.dynamicChangeGroupSize = function (evt, currentGroup, keyShape) {
    var item = evt.item;
    var model = item.getModel(); // 节点所在的GroupId

    var groupId = model.groupId;
    var graph = this.graph;
    var groupType = graph.get('groupType');
    var groupNodes = graph.get('groupNodes');
    var nodes = groupNodes[groupId]; // 拖出节点后，根据最新的节点数量，重新计算群组大小
    // 如果只有一个节点，拖出后，则删除该组

    if (nodes.length === 0) {
      // step 1: 从groupNodes中删除
      delete groupNodes[groupId]; // step 2: 从groups数据中删除

      var groupsData = graph.get('groups');
      graph.set('groups', groupsData.filter(function (gdata) {
        return gdata.id !== groupId;
      })); // step 3: 删除原来的群组

      currentGroup.remove();
    } else {
      var _a = this.calculationGroupPosition(nodes),
          x = _a.x,
          y = _a.y,
          width = _a.width,
          height = _a.height; // 检测操作的群组中是否包括子群组


      var paddingValue = this.getGroupPadding(groupId);
      var titleX = 0;
      var titleY = 0;

      if (groupType === 'circle') {
        var r = width > height ? width / 2 : height / 2;
        var cx = (width + 2 * x) / 2;
        var cy = (height + 2 * y) / 2;
        keyShape.attr({
          r: r + paddingValue,
          x: cx,
          y: cy
        });
        titleX = cx;
        titleY = cy - keyShape.attr('r');
      } else if (groupType === 'rect') {
        var defaultStyle = this.styles.default;
        var rectPadding = paddingValue * defaultStyle.disCoefficient;
        keyShape.attr({
          x: x - rectPadding,
          y: y - rectPadding,
          width: width + rectPadding * 2,
          height: height + rectPadding * 2
        });
        titleX = x - rectPadding + 15;
        titleY = y - rectPadding + 15;
      } // 如果存在标题，则更新标题位置


      this.updateGroupTitle(currentGroup, groupId, titleX, titleY);
    }

    this.setGroupStyle(keyShape, 'default');
  };

  CustomGroup.prototype.resetNodePoint = function () {
    this.nodePoint.length = 0;
  };

  CustomGroup.prototype.destroy = function () {
    this.graph = null;
    this.styles = {};
    this.customGroup = {};
    this.delegateInGroup = {};
    this.resetNodePoint();
    this.destroyed = true;
  };

  return CustomGroup;
}();

export default CustomGroup;