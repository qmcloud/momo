var min = Math.min,
    max = Math.max,
    abs = Math.abs;
var DEFAULT_TRIGGER = 'shift';
var ALLOW_EVENTS = ['drag', 'shift', 'ctrl', 'alt', 'control'];
export default {
  getDefaultCfg: function getDefaultCfg() {
    return {
      brushStyle: {
        fill: '#EEF6FF',
        fillOpacity: 0.4,
        stroke: '#DDEEFE',
        lineWidth: 1
      },
      onSelect: function onSelect() {},
      onDeselect: function onDeselect() {},
      selectedState: 'selected',
      trigger: DEFAULT_TRIGGER,
      includeEdges: true,
      selectedEdges: [],
      selectedNodes: []
    };
  },
  getEvents: function getEvents() {
    // 检测输入是否合法
    if (!(ALLOW_EVENTS.indexOf(this.trigger.toLowerCase()) > -1)) {
      this.trigger = DEFAULT_TRIGGER;
      console.warn("Behavior brush-select 的 trigger 参数不合法，请输入 'drag'、'shift'、'ctrl' 或 'alt'");
    }

    if (this.trigger === 'drag') {
      return {
        dragstart: 'onMouseDown',
        drag: 'onMouseMove',
        dragend: 'onMouseUp',
        'canvas:click': 'clearStates'
      };
    }

    return {
      dragstart: 'onMouseDown',
      drag: 'onMouseMove',
      dragend: 'onMouseUp',
      'canvas:click': 'clearStates',
      keyup: 'onKeyUp',
      keydown: 'onKeyDown'
    };
  },
  onMouseDown: function onMouseDown(e) {
    // 按在node上面拖动时候不应该是框选
    var item = e.item;
    var brush = this.brush;

    if (item) {
      return;
    }

    if (this.trigger !== 'drag' && !this.keydown) {
      return;
    }

    if (this.selectedNodes && this.selectedNodes.length !== 0) {
      this.clearStates();
    }

    if (!brush) {
      brush = this.createBrush();
    }

    this.originPoint = {
      x: e.canvasX,
      y: e.canvasY
    };
    brush.attr({
      width: 0,
      height: 0
    });
    brush.show();
    this.dragging = true;
  },
  onMouseMove: function onMouseMove(e) {
    if (!this.dragging) {
      return;
    }

    if (this.trigger !== 'drag' && !this.keydown) {
      return;
    }

    this.updateBrush(e);
  },
  onMouseUp: function onMouseUp(e) {
    var graph = this.graph; // TODO: 触发了 canvas:click 导致 clearStates

    if (!this.brush && !this.dragging) {
      return;
    }

    if (this.trigger !== 'drag' && !this.keydown) {
      return;
    }

    this.brush.remove(true); // remove and destroy

    this.brush = null;
    this.getSelectedNodes(e);
    this.dragging = false;
  },
  clearStates: function clearStates() {
    var _a = this,
        graph = _a.graph,
        selectedState = _a.selectedState;

    var nodes = graph.findAllByState('node', selectedState);
    var edges = graph.findAllByState('edge', selectedState);
    nodes.forEach(function (node) {
      return graph.setItemState(node, selectedState, false);
    });
    edges.forEach(function (edge) {
      return graph.setItemState(edge, selectedState, false);
    });
    this.selectedNodes = [];
    this.selectedEdges = [];

    if (this.onDeselect) {
      this.onDeselect(this.selectedNodes, this.selectedEdges);
    }

    graph.emit('nodeselectchange', {
      selectedItems: {
        nodes: [],
        edges: []
      },
      select: false
    });
  },
  getSelectedNodes: function getSelectedNodes(e) {
    var _this = this;

    var _a = this,
        graph = _a.graph,
        originPoint = _a.originPoint,
        shouldUpdate = _a.shouldUpdate;

    var state = this.selectedState;
    var p1 = {
      x: e.x,
      y: e.y
    };
    var p2 = graph.getPointByCanvas(originPoint.x, originPoint.y);
    var left = min(p1.x, p2.x);
    var right = max(p1.x, p2.x);
    var top = min(p1.y, p2.y);
    var bottom = max(p1.y, p2.y);
    var selectedNodes = [];
    var selectedIds = [];
    graph.getNodes().forEach(function (node) {
      var bbox = node.getBBox();

      if (bbox.centerX >= left && bbox.centerX <= right && bbox.centerY >= top && bbox.centerY <= bottom) {
        if (shouldUpdate(node, 'select')) {
          selectedNodes.push(node);
          var model = node.getModel();
          selectedIds.push(model.id);
          graph.setItemState(node, state, true);
        }
      }
    });
    var selectedEdges = [];

    if (this.includeEdges) {
      // 选中边，边的source和target都在选中的节点中时才选中
      selectedNodes.forEach(function (node) {
        var edges = node.getEdges();
        edges.forEach(function (edge) {
          var model = edge.getModel();
          var source = model.source,
              target = model.target;

          if (selectedIds.includes(source) && selectedIds.includes(target) && shouldUpdate(edge, 'select')) {
            selectedEdges.push(edge);
            graph.setItemState(edge, _this.selectedState, true);
          }
        });
      });
    }

    this.selectedEdges = selectedEdges;
    this.selectedNodes = selectedNodes;

    if (this.onSelect) {
      this.onSelect(selectedNodes, selectedEdges);
    }

    graph.emit('nodeselectchange', {
      selectedItems: {
        nodes: selectedNodes,
        edges: selectedEdges
      },
      select: true
    });
  },
  createBrush: function createBrush() {
    var self = this;
    var brush = self.graph.get('canvas').addShape('rect', {
      attrs: self.brushStyle,
      capture: false,
      name: 'brush-shape'
    });
    this.brush = brush;
    return brush;
  },
  updateBrush: function updateBrush(e) {
    var originPoint = this.originPoint;
    this.brush.attr({
      width: abs(e.canvasX - originPoint.x),
      height: abs(e.canvasY - originPoint.y),
      x: min(e.canvasX, originPoint.x),
      y: min(e.canvasY, originPoint.y)
    });
  },
  onKeyDown: function onKeyDown(e) {
    var code = e.key;

    if (!code) {
      return;
    } // 按住control键时，允许用户设置trigger为ctrl


    if (code.toLowerCase() === this.trigger.toLowerCase() || code.toLowerCase() === 'control') {
      this.keydown = true;
    } else {
      this.keydown = false;
    }
  },
  onKeyUp: function onKeyUp() {
    if (this.brush) {
      // 清除所有选中状态后，设置拖得动状态为false，并清除框选的brush
      this.brush.remove(true);
      this.brush = null;
      this.dragging = false;
    }

    this.keydown = false;
  }
};