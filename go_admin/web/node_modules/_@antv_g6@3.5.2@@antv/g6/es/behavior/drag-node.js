import { __assign } from "tslib";
import deepMix from '@antv/util/lib/deep-mix';
import Global from '../global';
export default {
  getDefaultCfg: function getDefaultCfg() {
    return {
      updateEdge: true,
      delegateStyle: {},
      // 是否开启delegate
      enableDelegate: false,
      // 拖动节点过程中是否只改变 Combo 的大小，而不改变其结构
      onlyChangeComboSize: false,
      // 拖动过程中目标 combo 状态样式
      comboActiveState: '',
      selectedState: 'selected'
    };
  },
  getEvents: function getEvents() {
    return {
      'node:dragstart': 'onDragStart',
      'node:drag': 'onDrag',
      'node:dragend': 'onDragEnd',
      'combo:dragenter': 'onDragEnter',
      'combo:dragleave': 'onDragLeave',
      'combo:drop': 'onDropCombo'
    };
  },
  validationCombo: function validationCombo(item) {
    if (!this.origin) {
      return;
    }

    if (!item) {
      return;
    }

    var type = item.getType();

    if (type !== 'combo') {
      return;
    }
  },

  /**
   * 开始拖动节点
   * @param evt
   */
  onDragStart: function onDragStart(evt) {
    var _this = this;

    if (!this.shouldBegin.call(this, evt)) {
      return;
    }

    var item = evt.item;

    if (!item || item.hasLocked()) {
      return;
    } // 拖动时，设置拖动元素的 capture 为false，则不拾取拖动的元素


    var group = item.getContainer();
    group.set('capture', false); // 如果拖动的target 是linkPoints / anchorPoints 则不允许拖动

    var target = evt.target;

    if (target) {
      var isAnchorPoint = target.get('isAnchorPoint');

      if (isAnchorPoint) {
        return;
      }
    }

    var graph = this.graph;
    this.targets = []; // 将节点拖入到指定的 Combo

    this.targetCombo = null; // 获取所有选中的元素

    var nodes = graph.findAllByState('node', this.selectedState);
    var currentNodeId = item.get('id'); // 当前拖动的节点是否是选中的节点

    var dragNodes = nodes.filter(function (node) {
      var nodeId = node.get('id');
      return currentNodeId === nodeId;
    }); // 只拖动当前节点

    if (dragNodes.length === 0) {
      this.targets.push(item);
    } else if (nodes.length > 1) {
      // 拖动多个节点
      nodes.forEach(function (node) {
        var locked = node.hasLocked();

        if (!locked) {
          _this.targets.push(node);
        }
      });
    } else {
      this.targets.push(item);
    }

    this.origin = {
      x: evt.x,
      y: evt.y
    };
    this.point = {};
    this.originPoint = {};
  },

  /**
   * 持续拖动节点
   * @param evt
   */
  onDrag: function onDrag(evt) {
    var _this = this;

    if (!this.origin) {
      return;
    }

    if (!this.shouldUpdate(this, evt)) {
      return;
    }

    if (this.get('enableDelegate')) {
      this.updateDelegate(evt);
    } else {
      this.targets.map(function (target) {
        _this.update(target, evt);
      });
    }
  },

  /**
   * 拖动结束，设置拖动元素capture为true，更新元素位置，如果是拖动涉及到 combo，则更新 combo 结构
   * @param evt
   */
  onDragEnd: function onDragEnd(evt) {
    var _this = this;

    if (!this.origin || !this.shouldEnd.call(this, evt)) {
      return;
    } // 拖动结束后，设置拖动元素 group 的 capture 为 true，允许拾取拖动元素


    var item = evt.item;
    var group = item.getContainer();
    group.set('capture', true);

    if (this.delegateRect) {
      this.delegateRect.remove();
      this.delegateRect = null;
    } // 当开启 delegate 时，拖动结束后需要更新所有已选中节点的位置


    if (this.get('enableDelegate')) {
      this.targets.map(function (node) {
        return _this.update(node, evt);
      });
    }

    var graph = this.graph; // 拖动结束后是动态改变 Combo 大小还是将节点从 Combo 中删除

    if (this.onlyChangeComboSize) {
      // 拖动节点结束后，动态改变 Combo 的大小
      graph.updateCombos();
    } else {
      // 拖放到了最外面，如果targets中有 combo，则删除掉
      if (!this.targetCombo) {
        this.targets.map(function (node) {
          // 拖动的节点有 comboId，即是从其他 combo 中拖出时才处理
          var model = node.getModel();

          if (model.comboId) {
            graph.updateComboTree(node);
          }
        });
      } else {
        var targetComboModel_1 = this.targetCombo.getModel();
        this.targets.map(function (node) {
          var nodeModel = node.getModel();

          if (nodeModel.comboId !== targetComboModel_1.id) {
            graph.updateComboTree(node, targetComboModel_1.id);
          }
        });
      }
    }

    this.point = {};
    this.origin = null;
    this.originPoint = {};
    this.targets.length = 0;
    this.targetCombo = null;
  },

  /**
   * 拖动过程中将节点放置到 combo 上
   * @param evt
   */
  onDropCombo: function onDropCombo(evt) {
    var item = evt.item;
    this.validationCombo(item);
    var graph = this.graph;

    if (this.comboActiveState) {
      graph.setItemState(item, this.comboActiveState, false);
    }

    this.targetCombo = item;
  },

  /**
   * 将节点拖入到 Combo 中
   * @param evt
   */
  onDragEnter: function onDragEnter(evt) {
    var item = evt.item;
    this.validationCombo(item);
    var graph = this.graph;

    if (this.comboActiveState) {
      graph.setItemState(item, this.comboActiveState, true);
    }
  },

  /**
   * 将节点从 Combo 中拖出
   * @param evt
   */
  onDragLeave: function onDragLeave(evt) {
    var item = evt.item;
    this.validationCombo(item);
    var graph = this.graph;

    if (this.comboActiveState) {
      graph.setItemState(item, this.comboActiveState, false);
    }
  },

  /**
   * 更新节点
   * @param item 拖动的节点实例
   * @param evt
   */
  update: function update(item, evt) {
    var origin = this.origin;
    var model = item.get('model');
    var nodeId = item.get('id');

    if (!this.point[nodeId]) {
      this.point[nodeId] = {
        x: model.x,
        y: model.y
      };
    }

    var x = evt.x - origin.x + this.point[nodeId].x;
    var y = evt.y - origin.y + this.point[nodeId].y;
    var pos = {
      x: x,
      y: y
    };

    if (this.get('updateEdge')) {
      this.graph.updateItem(item, pos);
    } else {
      item.updatePosition(pos);
    }
  },

  /**
   * 更新拖动元素时的delegate
   * @param {Event} e 事件句柄
   * @param {number} x 拖动单个元素时候的x坐标
   * @param {number} y 拖动单个元素时候的y坐标
   */
  updateDelegate: function updateDelegate(e) {
    if (!this.delegateRect) {
      // 拖动多个
      var parent_1 = this.graph.get('group');
      var attrs = deepMix({}, Global.delegateStyle, this.delegateStyle);

      var _a = this.calculationGroupPosition(e),
          cx = _a.x,
          cy = _a.y,
          width = _a.width,
          height = _a.height,
          minX = _a.minX,
          minY = _a.minY;

      this.originPoint = {
        x: cx,
        y: cy,
        width: width,
        height: height,
        minX: minX,
        minY: minY
      }; // model上的x, y是相对于图形中心的，delegateShape是g实例，x,y是绝对坐标

      this.delegateRect = parent_1.addShape('rect', {
        attrs: __assign({
          width: width,
          height: height,
          x: cx,
          y: cy
        }, attrs),
        name: 'rect-delegate-shape'
      });
      this.delegateRect.set('capture', false);
    } else {
      var clientX = e.x - this.origin.x + this.originPoint.minX;
      var clientY = e.y - this.origin.y + this.originPoint.minY;
      this.delegateRect.attr({
        x: clientX,
        y: clientY
      });
    }
  },

  /**
   * 计算delegate位置，包括左上角左边及宽度和高度
   * @memberof ItemGroup
   * @return {object} 计算出来的delegate坐标信息及宽高
   */
  calculationGroupPosition: function calculationGroupPosition(evt) {
    var graph = this.graph;
    var nodes = graph.findAllByState('node', this.selectedState);

    if (nodes.length === 0) {
      nodes.push(evt.item);
    }

    var minx = Infinity;
    var maxx = -Infinity;
    var miny = Infinity;
    var maxy = -Infinity; // 获取已节点的所有最大最小x y值

    for (var i = 0; i < nodes.length; i++) {
      var element = nodes[i];
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
    }

    var x = Math.floor(minx);
    var y = Math.floor(miny);
    var width = Math.ceil(maxx) - Math.floor(minx);
    var height = Math.ceil(maxy) - Math.floor(miny);
    return {
      x: x,
      y: y,
      width: width,
      height: height,
      minX: minx,
      minY: miny
    };
  }
};