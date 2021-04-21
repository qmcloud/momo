import { __assign } from "tslib";
/*
 * @Author: moyee
 * @Date: 2019-07-31 14:36:15
 * @LastEditors: moyee
 * @LastEditTime: 2019-08-23 11:13:43
 * @Description: 拖动群组
 */

import deepMix from '@antv/util/lib/deep-mix';
import Global from '../global';
export default {
  getDefaultCfg: function getDefaultCfg() {
    return {
      delegate: true,
      delegateStyle: {},
      delegateShapes: {},
      delegateShapeBBoxs: {}
    };
  },
  getEvents: function getEvents() {
    return {
      dragstart: 'onDragStart',
      drag: 'onDrag',
      dragend: 'onDragEnd',
      'canvas:mouseleave': 'onOutOfRange'
    };
  },
  onDragStart: function onDragStart(evt) {
    var graph = this.graph;
    var target = evt.target; // 获取拖动的group的ID，如果拖动的不是group，则直接return

    var groupId = target.get('groupId');

    if (!groupId) {
      return;
    }

    var customGroupControll = graph.get('customGroupControll');
    var customGroup = customGroupControll.customGroup;
    var currentGroup = customGroup[groupId].nodeGroup;
    this.targetGroup = currentGroup;
    this.mouseOrigin = {
      x: evt.x,
      y: evt.y
    }; // 获取groupId的父Group的ID

    var groups = graph.save().groups;
    var parentGroupId = null;

    for (var i = 0; i < groups.length; i++) {
      var group = groups[i];

      if (groupId === group.id) {
        parentGroupId = group.parentId;
        break;
      }
    }

    if (parentGroupId) {
      var parentGroup = customGroup[parentGroupId].nodeGroup;
      customGroupControll.setGroupStyle(parentGroup.get('keyShape'), 'hover');
    }
  },
  onDrag: function onDrag(evt) {
    if (!this.mouseOrigin) {
      return;
    }

    this.updateDelegate(evt);
  },
  onDragEnd: function onDragEnd(evt) {
    var graph = this.graph; // 删除delegate shape

    var groupId = evt.target.get('groupId');

    if (this.delegateShapes[groupId]) {
      this.delegateShapeBBox = this.delegateShapes[groupId].getBBox();
      this.delegateShapes[groupId].remove();
      delete this.delegateShapes[groupId];
    }

    if (!this.delegateShapeBBox) {
      return;
    } // 修改群组位置


    var customGroupControll = graph.get('customGroupControll');
    var delegateShapeBBoxs = this.delegateShapeBBoxs[groupId];
    customGroupControll.updateGroup(groupId, delegateShapeBBoxs, this.mouseOrigin);
    this.mouseOrigin = null;
    this.shapeOrigin = null;
    customGroupControll.resetNodePoint();
    this.delegateShapeBBox = null;
  },
  updateDelegate: function updateDelegate(evt) {
    var graph = this.graph;
    var groupId = evt.target.get('groupId');
    var item = this.targetGroup.get('keyShape');
    var delegateShape = this.delegateShapes[groupId];
    var groupBbox = item.getBBox();
    var delegateType = item.get('type');

    if (!delegateShape) {
      var delegateGroup = graph.get('delegateGroup');
      var bboxX = groupBbox.x,
          bboxY = groupBbox.y,
          width = groupBbox.width,
          height = groupBbox.height;

      var attrs = __assign({
        width: width,
        height: height
      }, deepMix({}, Global.delegateStyle, this.delegateStyle)); // 如果delegate是circle


      if (delegateType === 'circle') {
        var r = width > height ? width / 2 : height / 2;
        var cx = bboxX + r;
        var cy = bboxY + r;
        delegateShape = delegateGroup.addShape('circle', {
          attrs: __assign({
            x: cx,
            y: cy,
            r: r
          }, attrs),
          name: 'circle-delegate-shape'
        });
        this.shapeOrigin = {
          x: cx,
          y: cy
        };
      } else {
        delegateShape = delegateGroup.addShape('rect', {
          attrs: __assign({
            x: bboxX,
            y: bboxY
          }, attrs),
          name: 'rect-delegate-shape'
        });
        this.shapeOrigin = {
          x: bboxX,
          y: bboxY
        };
      } // delegateShape.set('capture', false);


      this.delegateShapes[groupId] = delegateShape;
      this.delegateShapeBBoxs[groupId] = delegateShape.getBBox();
    } else {
      var _a = this,
          mouseOrigin = _a.mouseOrigin,
          shapeOrigin = _a.shapeOrigin;

      var deltaX = evt.x - mouseOrigin.x;
      var deltaY = evt.y - mouseOrigin.y;
      var x = deltaX + shapeOrigin.x;
      var y = deltaY + shapeOrigin.y;
      delegateShape.attr({
        x: x,
        y: y
      });
      this.delegateShapeBBoxs[groupId] = delegateShape.getBBox();
    }
  },
  onOutOfRange: function onOutOfRange(e) {
    var _this = this;

    var canvasElement = this.graph.get('canvas').get('el');

    var listener = function listener(ev) {
      if (ev.target !== canvasElement) {
        _this.onDragEnd(e); // 终止时需要判断此时是否在监听画布外的 mouseup 事件，若有则解绑


        document.body.removeEventListener('mouseup', listener, true);
      }
    };

    if (this.mouseOrigin) {
      document.body.addEventListener('mouseup', listener, true);
    }
  }
};