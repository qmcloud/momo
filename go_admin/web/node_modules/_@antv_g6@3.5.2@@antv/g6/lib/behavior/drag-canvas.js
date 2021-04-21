"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _base = require("../util/base");

var abs = Math.abs;
var DRAG_OFFSET = 10;
var ALLOW_EVENTS = ['shift', 'ctrl', 'alt', 'control'];
var _default = {
  getDefaultCfg: function getDefaultCfg() {
    return {
      direction: 'both'
    };
  },
  getEvents: function getEvents() {
    return {
      dragstart: 'onMouseDown',
      drag: 'onMouseMove',
      dragend: 'onMouseUp',
      'canvas:click': 'onMouseUp',
      keyup: 'onKeyUp',
      focus: 'onKeyUp',
      keydown: 'onKeyDown'
    };
  },
  updateViewport: function updateViewport(e) {
    var origin = this.origin;
    var clientX = +e.clientX;
    var clientY = +e.clientY;

    if ((0, _base.isNaN)(clientX) || (0, _base.isNaN)(clientY)) {
      return;
    }

    var dx = clientX - origin.x;
    var dy = clientY - origin.y;

    if (this.get('direction') === 'x') {
      dy = 0;
    } else if (this.get('direction') === 'y') {
      dx = 0;
    }

    this.origin = {
      x: clientX,
      y: clientY
    };
    var width = this.graph.get('width');
    var height = this.graph.get('height');
    var graphCanvasBBox = this.graph.get('canvas').getCanvasBBox();

    if (graphCanvasBBox.minX + dx > width || graphCanvasBBox.maxX + dx < 0) {
      dx = 0;
    }

    if (graphCanvasBBox.minY + dy > height || graphCanvasBBox.maxY + dy < 0) {
      dy = 0;
    }

    this.graph.translate(dx, dy);
    this.graph.paint();
  },
  onMouseDown: function onMouseDown(e) {
    var self = this;

    if (self.keydown || e.shape) {
      return;
    }

    self.origin = {
      x: e.clientX,
      y: e.clientY
    };
    self.dragging = false;
  },
  onMouseMove: function onMouseMove(e) {
    var graph = this.graph;

    if (this.keydown || e.shape) {
      return;
    }

    e = (0, _base.cloneEvent)(e);

    if (!this.origin) {
      return;
    }

    if (!this.dragging) {
      if (abs(this.origin.x - e.clientX) + abs(this.origin.y - e.clientY) < DRAG_OFFSET) {
        return;
      }

      if (this.shouldBegin.call(this, e)) {
        e.type = 'dragstart';
        graph.emit('canvas:dragstart', e);
        this.dragging = true;
      }
    } else {
      e.type = 'drag';
      graph.emit('canvas:drag', e);
    }

    if (this.shouldUpdate.call(this, e)) {
      this.updateViewport(e);
    }
  },
  onMouseUp: function onMouseUp(e) {
    var graph = this.graph;

    if (this.keydown || e.shape) {
      return;
    }

    if (!this.dragging) {
      this.origin = null;
      return;
    }

    e = (0, _base.cloneEvent)(e);

    if (this.shouldEnd.call(this, e)) {
      this.updateViewport(e);
    }

    e.type = 'dragend';
    graph.emit('canvas:dragend', e);
    this.endDrag();
  },
  endDrag: function endDrag() {
    this.origin = null;
    this.dragging = false;
    this.dragbegin = false;
  },
  onKeyDown: function onKeyDown(e) {
    var self = this;
    var code = e.key;

    if (!code) {
      return;
    }

    if (ALLOW_EVENTS.indexOf(code.toLowerCase()) > -1) {
      self.keydown = true;
    } else {
      self.keydown = false;
    }
  },
  onKeyUp: function onKeyUp() {
    this.keydown = false;
  }
};
exports.default = _default;