import each from '@antv/util/lib/each'; // 自定义 Behavior 时候共有的方法

export default {
  getDefaultCfg: function getDefaultCfg() {
    return {};
  },

  /**
   * register event handler, behavior will auto bind events
   * for example:
   * return {
   *  click: 'onClick'
   * }
   */
  getEvents: function getEvents() {
    return {};
  },
  shouldBegin: function shouldBegin() {
    return true;
  },
  shouldUpdate: function shouldUpdate() {
    return true;
  },
  shouldEnd: function shouldEnd() {
    return true;
  },

  /**
   * auto bind events when register behavior
   * @param graph Graph instance
   */
  bind: function bind(graph) {
    var _this = this;

    var events = this.events;
    this.graph = graph;

    if (this.type === 'drag-canvas' || this.type === 'brush-select') {
      graph.get('canvas').set('draggable', true);
    }

    each(events, function (handler, event) {
      graph.on(event, handler);
    }); // To avoid the tabs switching makes the keydown related behaviors disable

    document.addEventListener('visibilitychange', function () {
      _this.keydown = false;
    });
  },
  unbind: function unbind(graph) {
    var events = this.events;

    if (this.type === 'drag-canvas' || this.type === 'brush-select') {
      graph.get('canvas').set('draggable', false);
    }

    each(events, function (handler, event) {
      graph.off(event, handler);
    });
  },
  get: function get(val) {
    return this[val];
  },
  set: function set(key, val) {
    this[key] = val;
    return this;
  }
};