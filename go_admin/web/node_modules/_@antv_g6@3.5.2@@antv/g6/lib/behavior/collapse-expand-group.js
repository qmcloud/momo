"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

/*
 * @Author: moyee
 * @Date: 2019-07-31 14:36:15
 * @LastEditors: moyee
 * @LastEditTime: 2019-08-22 18:43:24
 * @Description: 收起和展开群组
 */
var DEFAULT_TRIGGER = 'dblclick';
var ALLOW_EVENTS = ['click', 'dblclick'];
var _default = {
  getDefaultCfg: function getDefaultCfg() {
    return {
      trigger: DEFAULT_TRIGGER
    };
  },
  getEvents: function getEvents() {
    var _a;

    var trigger; // 检测输入是否合法

    if (ALLOW_EVENTS.includes(this.trigger)) {
      trigger = this.trigger;
    } else {
      trigger = DEFAULT_TRIGGER; // eslint-disable-next-line no-console

      console.warn("Behavior collapse-expand-group 的 trigger 参数不合法，请输入 'click' 或 'dblclick'");
    }

    return _a = {}, _a["" + trigger] = 'onGroupClick', _a;
  },
  onGroupClick: function onGroupClick(evt) {
    var target = evt.target;
    var graph = this.graph;
    var groupId = target.get('groupId');

    if (!groupId) {
      return;
    }

    var customGroupControll = graph.get('customGroupControll');
    customGroupControll.collapseExpandGroup(groupId);
  }
};
exports.default = _default;