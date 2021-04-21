"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _default = {
  getDefaultCfg: function getDefaultCfg() {
    return {
      trigger: 'mouseenter',
      activeState: 'active',
      inactiveState: 'inactive',
      resetSelected: false,
      shouldUpdate: function shouldUpdate() {
        return true;
      }
    };
  },
  getEvents: function getEvents() {
    if (this.get('trigger') === 'mouseenter') {
      return {
        'node:mouseenter': 'setAllItemStates',
        'node:mouseleave': 'clearActiveState'
      };
    }

    return {
      'node:click': 'setAllItemStates',
      'canvas:click': 'clearAllItemStates'
    };
  },
  setAllItemStates: function setAllItemStates(e) {
    var item = e.item;
    var graph = this.get('graph');
    this.set('item', item);

    if (!this.shouldUpdate(e.item, {
      event: e,
      action: 'activate'
    })) {
      return;
    }

    var self = this;
    var activeState = this.get('activeState');
    var inactiveState = this.get('inactiveState');
    graph.getNodes().forEach(function (node) {
      var hasSelected = node.hasState('selected');

      if (self.resetSelected) {
        if (hasSelected) {
          graph.setItemState(node, 'selected', false);
        }
      }

      graph.setItemState(node, activeState, false);

      if (inactiveState) {
        graph.setItemState(node, inactiveState, true);
      }
    });
    graph.getEdges().forEach(function (edge) {
      graph.setItemState(edge, activeState, false);

      if (inactiveState) {
        graph.setItemState(edge, inactiveState, true);
      }
    });

    if (inactiveState) {
      graph.setItemState(item, inactiveState, false);
    }

    graph.setItemState(item, activeState, true);
    graph.getEdges().forEach(function (edge) {
      if (edge.getSource() === item) {
        var target = edge.getTarget();

        if (inactiveState) {
          graph.setItemState(target, inactiveState, false);
        }

        graph.setItemState(target, activeState, true);
        graph.setItemState(edge, inactiveState, false);
        graph.setItemState(edge, activeState, true);
        edge.toFront();
      } else if (edge.getTarget() === item) {
        var source = edge.getSource();

        if (inactiveState) {
          graph.setItemState(source, inactiveState, false);
        }

        graph.setItemState(source, activeState, true);
        graph.setItemState(edge, inactiveState, false);
        graph.setItemState(edge, activeState, true);
        edge.toFront();
      }
    });
    graph.emit('afteractivaterelations', {
      item: e.item,
      action: 'activate'
    });
  },
  clearActiveState: function clearActiveState(e) {
    var self = this;
    var graph = self.get('graph');

    if (!self.shouldUpdate(e.item, {
      event: e,
      action: 'deactivate'
    })) {
      return;
    }

    var activeState = this.get('activeState');
    var inactiveState = this.get('inactiveState');
    var autoPaint = graph.get('autoPaint');
    graph.setAutoPaint(false);
    graph.getNodes().forEach(function (node) {
      graph.clearItemStates(node, [activeState, inactiveState]);
    });
    graph.getEdges().forEach(function (edge) {
      graph.clearItemStates(edge, [activeState, inactiveState, 'deactivate']);
    });
    graph.paint();
    graph.setAutoPaint(autoPaint);
    graph.emit('afteractivaterelations', {
      item: e.item || self.get('item'),
      action: 'deactivate'
    });
  },
  clearAllItemStates: function clearAllItemStates(e) {
    var self = this;
    var graph = self.get('graph');

    if (!self.shouldUpdate(e.item, {
      event: e,
      action: 'deactivate'
    })) {
      return;
    }

    var activeState = this.get('activeState');
    var inactiveState = this.get('inactiveState');
    graph.getNodes().forEach(function (node) {
      graph.clearItemStates(node, [activeState, inactiveState]);
    });
    graph.getEdges().forEach(function (edge) {
      graph.clearItemStates(edge, [activeState, inactiveState, 'deactivate']);
    });
    graph.emit('afteractivaterelations', {
      item: e.item || self.get('item'),
      action: 'deactivate'
    });
  }
};
exports.default = _default;