import { __assign } from "tslib";
/*
 * @Author: moyee
 * @Date: 2019-06-27 18:12:06
 * @LastEditors: moyee
 * @LastEditTime: 2019-08-23 13:54:53
 * @Description: 有group的情况下，拖动节点的Behavior
 */

import deepMix from '@antv/util/lib/deep-mix';
import Global from '../global';
var body = document.body;
export default {
  getDefaultCfg: function getDefaultCfg() {
    return {
      updateEdge: true,
      delegate: true,
      delegateStyle: {},
      maxMultiple: 1.1,
      minMultiple: 1
    };
  },
  getEvents: function getEvents() {
    return {
      'node:dragstart': 'onDragStart',
      'node:drag': 'onDrag',
      'node:dragend': 'onDragEnd',
      'canvas:mouseleave': 'onOutOfRange',
      dragover: 'onDragOver',
      // FIXME: does not response
      dragleave: 'onDragLeave'
    };
  },
  onDragOver: function onDragOver(evt) {
    var graph = this.graph;
    var target = evt.target;
    var groupId = target.get('groupId');

    if (groupId && this.origin) {
      var customGroupControll = graph.get('customGroupControll');
      var customGroup = customGroupControll.getDeletageGroupById(groupId);

      if (customGroup) {
        var currentGroup = customGroup.nodeGroup;
        var keyShape = currentGroup.get('keyShape');
        this.inGroupId = groupId;
        customGroupControll.setGroupStyle(keyShape, 'hover');
      }
    }
  },

  /**
   * 拖动节点移除Group时的事件
   * @param {Event} evt 事件句柄
   */
  onDragLeave: function onDragLeave(evt) {
    var graph = this.graph;
    var target = evt.target;
    var groupId = target.get('groupId');

    if (groupId && this.origin) {
      var customGroupControll = graph.get('customGroupControll');
      var customGroup = customGroupControll.getDeletageGroupById(groupId);

      if (customGroup) {
        var currentGroup = customGroup.nodeGroup;
        var keyShape = currentGroup.get('keyShape');
        customGroupControll.setGroupStyle(keyShape, 'default');
      }
    }

    if (!groupId) {
      this.inGroupId = null;
    }
  },
  onDragStart: function onDragStart(e) {
    var graph = this.graph;

    if (!this.shouldBegin.call(this, e)) {
      return;
    }

    var item = e.item;
    this.target = item; // 拖动节点时，如果在Group中，则Group高亮

    var model = item.getModel();
    var groupId = model.groupId;

    if (groupId) {
      var customGroupControll = graph.get('customGroupControll');
      var customGroup = customGroupControll.getDeletageGroupById(groupId);

      if (customGroup) {
        var currentGroup = customGroup.nodeGroup;
        var keyShape = currentGroup.get('keyShape');
        customGroupControll.setGroupStyle(keyShape, 'hover'); // 初始拖动时候，如果是在当前群组中拖动，则赋值为当前groupId

        this.inGroupId = groupId;
      }
    }

    this.origin = {
      x: e.x,
      y: e.y
    };
    this.point = {};
    this.originPoint = {};
  },
  onDrag: function onDrag(e) {
    if (!this.origin) {
      return;
    }

    if (!this.get('shouldUpdate').call(this, e)) {
      return;
    }

    this.update(this.target, e, true);
    var item = e.item;
    var graph = this.graph;
    var model = item.getModel();
    var groupId = model.groupId;

    if (groupId) {
      var customGroupControll = graph.get('customGroupControll');
      var customGroup = customGroupControll.getDeletageGroupById(groupId);

      if (customGroup) {
        var currentGroup = customGroup.nodeGroup;
        var keyShape = currentGroup.get('keyShape'); // 当前

        if (this.inGroupId !== groupId) {
          customGroupControll.setGroupStyle(keyShape, 'default');
        } else {
          customGroupControll.setGroupStyle(keyShape, 'hover');
        }
      }
    }
  },
  onDragEnd: function onDragEnd(e) {
    if (!this.origin || !this.shouldEnd.call(this, e)) {
      return;
    }

    if (this.shape) {
      this.shape.remove();
      this.shape = null;
    }

    if (this.target) {
      var delegateShape = this.target.get('delegateShape');

      if (delegateShape) {
        delegateShape.remove();
        this.target.set('delegateShape', null);
      }
    }

    if (this.target) {
      this.update(this.target, e);
    }

    this.point = {};
    this.origin = null;
    this.originPoint = {};
    this.target = null;
    this.setCurrentGroupStyle(e);
  },
  setCurrentGroupStyle: function setCurrentGroupStyle(evt) {
    var graph = this.graph;
    var item = evt.item;
    var model = item.getModel(); // 节点所在的GroupId

    var groupId = model.groupId,
        id = model.id;
    var customGroupControll = graph.get('customGroupControll');
    var customGroup = customGroupControll.customGroup;
    var groupNodes = graph.get('groupNodes');

    if (this.inGroupId && groupId) {
      var currentGroup = customGroup[groupId].nodeGroup;

      if (!currentGroup) {
        return;
      }

      var keyShape = currentGroup.get('keyShape');
      var itemBBox = item.getBBox();
      var currentGroupBBox = keyShape.getBBox();
      var centerX = itemBBox.centerX,
          centerY = itemBBox.centerY;
      var minX = currentGroupBBox.minX,
          minY = currentGroupBBox.minY,
          maxX = currentGroupBBox.maxX,
          maxY = currentGroupBBox.maxY; // 在自己的group中拖动，判断是否拖出了自己的group
      // this.inGroupId !== groupId，则说明拖出了原来的group，拖到了其他group上面，
      // 则删除item中的groupId字段，同时删除group中的nodeID

      if (!(centerX < maxX * this.maxMultiple && centerX > minX * this.minMultiple && centerY < maxY * this.maxMultiple && centerY > minY * this.minMultiple) || this.inGroupId !== groupId) {
        // 拖出了group，则删除item中的groupId字段，同时删除group中的nodeID
        var currentGroupNodes = groupNodes[groupId];
        groupNodes[groupId] = currentGroupNodes.filter(function (node) {
          return node !== id;
        });
        customGroupControll.dynamicChangeGroupSize(evt, currentGroup, keyShape); // 同时删除groupID中的节点

        delete model.groupId;
      } // 拖动到其他的group上面


      if (this.inGroupId !== groupId) {
        // 拖动新的group后，更新groupNodes及model中的groupId
        var nodeInGroup = customGroup[this.inGroupId].nodeGroup;

        if (!nodeInGroup) {
          return;
        }

        var targetKeyShape = nodeInGroup.get('keyShape'); // 将该节点添加到inGroupId中

        if (groupNodes[this.inGroupId].indexOf(id) === -1) {
          groupNodes[this.inGroupId].push(id);
        } // 更新节点的groupId为拖动上去的group Id


        model.groupId = this.inGroupId; // 拖入节点后，根据最新的节点数量，重新计算群组大小

        customGroupControll.dynamicChangeGroupSize(evt, nodeInGroup, targetKeyShape);
      }

      customGroupControll.setGroupStyle(keyShape, 'default');
    } else if (this.inGroupId && !groupId) {
      // 将节点拖动到群组中
      var nodeInGroup = customGroup[this.inGroupId].nodeGroup;

      if (!nodeInGroup) {
        return;
      }

      var keyShape = nodeInGroup.get('keyShape'); // 将该节点添加到inGroupId中

      if (groupNodes[this.inGroupId].indexOf(id) === -1) {
        groupNodes[this.inGroupId].push(id);
      } // 更新节点的groupId为拖动上去的group Id


      model.groupId = this.inGroupId; // 拖入节点后，根据最新的节点数量，重新计算群组大小

      customGroupControll.dynamicChangeGroupSize(evt, nodeInGroup, keyShape);
    } else if (!this.inGroupId && groupId) {
      // 拖出到群组之外了，则删除数据中的groupId
      Object.keys(groupNodes).forEach(function (gnode) {
        var currentGroupNodes = groupNodes[gnode];
        groupNodes[gnode] = currentGroupNodes.filter(function (node) {
          return node !== id;
        });
      });
      var currentGroup = customGroup[groupId].nodeGroup;

      if (!currentGroup) {
        return;
      }

      var keyShape = currentGroup.get('keyShape');
      customGroupControll.dynamicChangeGroupSize(evt, currentGroup, keyShape);
      delete model.groupId;
    }

    this.inGroupId = null;
  },
  // 若在拖拽时，鼠标移出画布区域，此时放开鼠标无法终止 drag 行为。在画布外监听 mouseup 事件，放开则终止
  onOutOfRange: function onOutOfRange(e) {
    var self = this;
    var canvasElement = self.graph.get('canvas').get('el');

    function listener(ev) {
      if (ev.target !== canvasElement) {
        e.item = self.target;
        self.onDragEnd(e); // 终止时需要判断此时是否在监听画布外的 mouseup 事件，若有则解绑

        document.body.removeEventListener('mouseup', listener, true);
      }
    }

    if (self.origin) {
      body.addEventListener('mouseup', listener, true);
    }
  },
  update: function update(item, e, force) {
    var origin = this.origin;
    var model = item.get('model');
    var nodeId = item.get('id');

    if (!this.point[nodeId]) {
      this.point[nodeId] = {
        x: model.x,
        y: model.y
      };
    }

    var x = e.x - origin.x + this.point[nodeId].x;
    var y = e.y - origin.y + this.point[nodeId].y; // 拖动单个未选中元素

    if (force) {
      this.updateDelegate(e, x, y);
      return;
    }

    var pos = {
      x: x,
      y: y
    };

    if (this.get('updateEdge')) {
      this.graph.updateItem(item, pos);
    } else {
      item.updatePosition(pos);
      this.graph.paint();
    }
  },

  /**
   * 更新拖动元素时的delegate
   * @param {Event} e 事件句柄
   * @param {number} x 拖动单个元素时候的x坐标
   * @param {number} y 拖动单个元素时候的y坐标
   */
  updateDelegate: function updateDelegate(e, x, y) {
    var graph = this.graph;
    var item = e.item;
    var groupType = graph.get('groupType');
    var bbox = item.get('keyShape').getBBox();

    if (!this.shape) {
      var parent_1 = graph.get('delegateGroup');
      var attrs = deepMix({}, Global.delegateStyle, this.delegateStyle);

      if (this.target) {
        this.shape = parent_1.addShape('rect', {
          attrs: __assign({
            width: bbox.width,
            height: bbox.height,
            x: x - bbox.width / 2,
            y: y - bbox.height / 2
          }, attrs),
          name: 'delegate-shape'
        });
        this.target.set('delegateShape', this.shape);
      }

      this.shape.set('capture', false);
    }

    if (this.target) {
      if (groupType === 'circle') {
        this.shape.attr({
          x: x - bbox.width / 2,
          y: y - bbox.height / 2
        });
      } else if (groupType === 'rect') {
        this.shape.attr({
          x: x,
          y: y
        });
      }
    }
  }
};