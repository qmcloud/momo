var DELTA = 0.05;
export default {
  getDefaultCfg: function getDefaultCfg() {
    return {
      sensitivity: 2,
      minZoom: 0.2,
      maxZoom: 10,
      enableOptimize: false,
      optimizeZoom: 0.7
    };
  },
  getEvents: function getEvents() {
    return {
      wheel: 'onWheel'
    };
  },
  onWheel: function onWheel(e) {
    var graph = this.graph;
    e.preventDefault();

    if (!this.shouldUpdate.call(this, e)) {
      return;
    }

    var canvas = graph.get('canvas');
    var point = canvas.getPointByClient(e.clientX, e.clientY);
    var sensitivity = this.get('sensitivity');
    var ratio = graph.getZoom(); // 兼容IE、Firefox及Chrome

    if (e.wheelDelta < 0) {
      ratio = 1 - DELTA * sensitivity;
    } else {
      ratio = 1 + DELTA * sensitivity;
    }

    var zoom = ratio * graph.getZoom();

    if (zoom > this.get('maxZoom') || zoom < this.get('minZoom')) {
      return;
    }

    var enableOptimize = this.get('enableOptimize');

    if (enableOptimize) {
      var optimizeZoom = this.get('optimizeZoom');
      var currentZoom = graph.getZoom();

      if (currentZoom < optimizeZoom) {
        var nodes = graph.getNodes();
        var edges = graph.getEdges();
        nodes.map(function (node) {
          if (!node.destroyed) {
            var children = node.getContainer().get('children');
            children.map(function (shape) {
              if (!shape.destoryed && !shape.get('isKeyShape')) {
                shape.hide();
              }
            });
          }
        });
        edges.map(function (edge) {
          var children = edge.getContainer().get('children');
          children.map(function (shape) {
            if (!shape.get('isKeyShape')) {
              shape.hide();
            }
          });
        });
      } else {
        var nodes = graph.getNodes();
        var edges = graph.getEdges();
        nodes.map(function (node) {
          var children = node.getContainer().get('children');
          children.map(function (shape) {
            if (!shape.get('visible')) {
              shape.show();
            }
          });
        });
        edges.map(function (edge) {
          var children = edge.getContainer().get('children');
          children.map(function (shape) {
            if (!shape.get('visible')) {
              shape.show();
            }
          });
        });
      }
    }

    graph.zoom(ratio, {
      x: point.x,
      y: point.y
    });
    graph.emit('wheelzoom', e);
  }
};