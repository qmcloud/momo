/*
 * @Author: Shiwu
 * @Description: 收起和展开 Combo
 */
var DEFAULT_TRIGGER = 'dblclick';
var ALLOW_EVENTS = ['click', 'dblclick'];
export default {
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

    return _a = {}, _a["" + trigger] = 'onComboClick', _a;
  },
  onComboClick: function onComboClick(evt) {
    var item = evt.item;
    var graph = this.graph;
    if (!item || item.getType() !== 'combo') return;
    var model = item.getModel();
    var comboId = model.id;

    if (!comboId) {
      return;
    }

    graph.collapseExpandCombo(comboId);
    if (graph.get('layout')) graph.layout();else graph.refreshPositions(); // graph.refreshPositions();
  }
};