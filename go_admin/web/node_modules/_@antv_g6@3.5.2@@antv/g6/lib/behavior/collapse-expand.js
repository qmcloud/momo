"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var DEFAULT_TRIGGER = 'click';
var ALLOW_EVENTS = ['click', 'dblclick'];
var _default = {
  getDefaultCfg: function getDefaultCfg() {
    return {
      /**
       * 发生收缩/扩展变化时的回调
       */
      trigger: DEFAULT_TRIGGER,
      onChange: function onChange() {}
    };
  },
  getEvents: function getEvents() {
    var _a;

    var trigger; // 检测输入是否合法

    if (ALLOW_EVENTS.includes(this.trigger)) {
      trigger = this.trigger;
    } else {
      trigger = DEFAULT_TRIGGER; // eslint-disable-next-line no-console

      console.warn("Behavior collapse-expand 的 trigger 参数不合法，请输入 'click' 或 'dblclick'");
    }

    return _a = {}, _a["node:" + trigger] = 'onNodeClick', _a;
  },
  onNodeClick: function onNodeClick(e) {
    var item = e.item; // 如果节点进行过更新，model 会进行 merge，直接改 model 就不能改布局，所以需要去改源数据

    var sourceData = this.graph.findDataById(item.get('id'));

    if (!sourceData) {
      return;
    }

    var children = sourceData.children; // 叶子节点的收缩和展开没有意义

    if (!children || children.length === 0) {
      return;
    }

    var collapsed = !sourceData.collapsed;

    if (!this.shouldBegin(e, collapsed)) {
      return;
    }

    sourceData.collapsed = collapsed;
    item.getModel().collapsed = collapsed;
    this.graph.emit('itemcollapsed', {
      item: e.item,
      collapsed: collapsed
    });

    if (!this.shouldUpdate(e, collapsed)) {
      return;
    }

    try {
      this.onChange(item, collapsed);
    } catch (err) {
      // eslint-disable-next-line no-console
      console.warn('G6 自 3.0.4 版本支持直接从 item.getModel() 获取源数据(临时通知，将在3.2.0版本中清除)', err);
    }

    this.graph.layout();
  }
};
exports.default = _default;