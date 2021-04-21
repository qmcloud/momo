"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _each = _interopRequireDefault(require("@antv/util/lib/each"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// 自定义 Behavior 时候共有的方法
var _default = {
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

    (0, _each.default)(events, function (handler, event) {
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

    (0, _each.default)(events, function (handler, event) {
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
exports.default = _default;