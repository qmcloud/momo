"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _each = _interopRequireDefault(require("@antv/util/lib/each"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var DEFAULT_TRIGGER = 'shift';
var ALLOW_EVENTS = ['shift', 'ctrl', 'alt', 'control'];
var _default = {
  getDefaultCfg: function getDefaultCfg() {
    return {
      multiple: true,
      trigger: DEFAULT_TRIGGER,
      selectedState: 'selected'
    };
  },
  getEvents: function getEvents() {
    var self = this; // 检测输入是否合法

    if (!(ALLOW_EVENTS.indexOf(self.trigger.toLowerCase()) > -1)) {
      self.trigger = DEFAULT_TRIGGER; // eslint-disable-next-line no-console

      console.warn("Behavior brush-select 的 trigger 参数不合法，请输入 'drag'、'shift'、'ctrl' 或 'alt'");
    }

    if (!self.multiple) {
      return {
        'node:click': 'onClick',
        'combo:click': 'onClick',
        'canvas:click': 'onCanvasClick'
      };
    }

    return {
      'node:click': 'onClick',
      'combo:click': 'onClick',
      'canvas:click': 'onCanvasClick',
      keyup: 'onKeyUp',
      keydown: 'onKeyDown'
    };
  },
  onClick: function onClick(evt) {
    var _this = this;

    var item = evt.item;

    if (!item) {
      return;
    }

    var type = item.getType();

    var _a = this,
        graph = _a.graph,
        keydown = _a.keydown,
        multiple = _a.multiple,
        shouldUpdate = _a.shouldUpdate; // allow to select multiple nodes but did not press a key || do not allow the select multiple nodes


    if (!keydown || !multiple) {
      var selected = graph.findAllByState(type, this.selectedState);
      (0, _each.default)(selected, function (combo) {
        if (combo !== item) {
          graph.setItemState(combo, _this.selectedState, false);
        }
      });
    }

    if (item.hasState(this.selectedState)) {
      if (shouldUpdate.call(this, evt)) {
        graph.setItemState(item, this.selectedState, false);
      }

      var selectedNodes = graph.findAllByState('node', this.selectedState);
      var selectedCombos = graph.findAllByState('combo', this.selectedState);
      graph.emit('nodeselectchange', {
        target: item,
        selectedItems: {
          nodes: selectedNodes,
          combos: selectedCombos
        },
        select: false
      });
    } else {
      if (shouldUpdate.call(this, evt)) {
        graph.setItemState(item, this.selectedState, true);
      }

      var selectedNodes = graph.findAllByState('node', this.selectedState);
      var selectedCombos = graph.findAllByState('combo', this.selectedState);
      graph.emit('nodeselectchange', {
        target: item,
        selectedItems: {
          nodes: selectedNodes,
          combos: selectedCombos
        },
        select: true
      });
    }
  },
  onCanvasClick: function onCanvasClick() {
    var _this = this;

    var graph = this.graph;
    var selected = graph.findAllByState('node', this.selectedState);
    (0, _each.default)(selected, function (node) {
      graph.setItemState(node, _this.selectedState, false);
    });
    var selectedCombos = graph.findAllByState('combo', this.selectedState);
    (0, _each.default)(selectedCombos, function (combo) {
      graph.setItemState(combo, _this.selectedState, false);
    });
    graph.emit('nodeselectchange', {
      selectedItems: {
        nodes: [],
        edges: [],
        combos: []
      },
      select: false
    });
  },
  onKeyDown: function onKeyDown(e) {
    var code = e.key;

    if (!code) {
      return;
    }

    if (code.toLowerCase() === this.trigger.toLowerCase() || code.toLowerCase() === 'control') {
      this.keydown = true;
    } else {
      this.keydown = false;
    }
  },
  onKeyUp: function onKeyUp() {
    this.keydown = false;
  }
};
exports.default = _default;