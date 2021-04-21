"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _modifyCss = _interopRequireDefault(require("@antv/dom-util/lib/modify-css"));

var _createDom = _interopRequireDefault(require("@antv/dom-util/lib/create-dom"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var _default = {
  onMouseEnter: function onMouseEnter(e) {
    var item = e.item;

    if (!this.shouldBegin(e)) {
      return;
    }

    this.currentTarget = item;
    this.showTooltip(e);
    this.graph.emit('tooltipchange', {
      item: e.item,
      action: 'show'
    });
  },
  onMouseMove: function onMouseMove(e) {
    if (!this.shouldUpdate(e)) {
      this.hideTooltip();
      return;
    }

    if (!this.currentTarget || e.item !== this.currentTarget) {
      return;
    }

    this.updatePosition(e);
  },
  onMouseLeave: function onMouseLeave(e) {
    if (!this.shouldEnd(e)) {
      return;
    }

    this.hideTooltip();
    this.graph.emit('tooltipchange', {
      item: this.currentTarget,
      action: 'hide'
    });
    this.currentTarget = null;
  },
  showTooltip: function showTooltip(e) {
    var container = this.container;

    if (!e.item) {
      return;
    }

    if (!container) {
      container = this.createTooltip(this.graph.get('canvas'));
      this.container = container;
    }

    var text = this.formatText(e.item.get('model'), e);
    container.innerHTML = text;
    this.updatePosition(e);
    (0, _modifyCss.default)(this.container, {
      visibility: 'visible'
    });
  },
  hideTooltip: function hideTooltip() {
    (0, _modifyCss.default)(this.container, {
      visibility: 'hidden'
    });
  },
  updatePosition: function updatePosition(e) {
    var _a = this,
        width = _a.width,
        height = _a.height,
        container = _a.container;

    var x = e.canvasX;
    var y = e.canvasY;
    var bbox = container.getBoundingClientRect();

    if (x > width / 2) {
      x -= bbox.width;
    } else {
      x += this.offset;
    }

    if (y > height / 2) {
      y -= bbox.height;
    } else {
      y += this.offset;
    }

    var left = x + "px";
    var top = y + "px";
    (0, _modifyCss.default)(this.container, {
      left: left,
      top: top,
      visibility: 'visible'
    });
  },
  createTooltip: function createTooltip(canvas) {
    var el = canvas.get('el');
    el.style.position = 'relative';
    var container = (0, _createDom.default)("<div class=\"g6-tooltip g6-" + this.item + "-tooltip\"></div>");
    el.parentNode.appendChild(container);
    (0, _modifyCss.default)(container, {
      position: 'absolute',
      visibility: 'visible'
    });
    this.width = canvas.get('width');
    this.height = canvas.get('height');
    this.container = container;
    return container;
  }
};
exports.default = _default;