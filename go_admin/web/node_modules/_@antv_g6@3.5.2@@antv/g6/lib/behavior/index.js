"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _each = _interopRequireDefault(require("@antv/util/lib/each"));

var _behavior = _interopRequireDefault(require("./behavior"));

var _dragCanvas = _interopRequireDefault(require("./drag-canvas"));

var _dragNode = _interopRequireDefault(require("./drag-node"));

var _activateRelations = _interopRequireDefault(require("./activate-relations"));

var _brushSelect = _interopRequireDefault(require("./brush-select"));

var _clickSelect = _interopRequireDefault(require("./click-select"));

var _zoomCanvas = _interopRequireDefault(require("./zoom-canvas"));

var _tooltip = _interopRequireDefault(require("./tooltip"));

var _edgeTooltip = _interopRequireDefault(require("./edge-tooltip"));

var _dragGroup = _interopRequireDefault(require("./drag-group"));

var _dragNodeWithGroup = _interopRequireDefault(require("./drag-node-with-group"));

var _collapseExpandGroup = _interopRequireDefault(require("./collapse-expand-group"));

var _collapseExpand = _interopRequireDefault(require("./collapse-expand"));

var _dragCombo = _interopRequireDefault(require("./drag-combo"));

var _collapseExpandCombo = _interopRequireDefault(require("./collapse-expand-combo"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var behaviors = {
  'drag-canvas': _dragCanvas.default,
  'zoom-canvas': _zoomCanvas.default,
  'drag-node': _dragNode.default,
  'activate-relations': _activateRelations.default,
  'brush-select': _brushSelect.default,
  'click-select': _clickSelect.default,
  tooltip: _tooltip.default,
  'edge-tooltip': _edgeTooltip.default,
  'drag-group': _dragGroup.default,
  'drag-node-with-group': _dragNodeWithGroup.default,
  'collapse-expand-group': _collapseExpandGroup.default,
  'collapse-expand': _collapseExpand.default,
  'drag-combo': _dragCombo.default,
  'collapse-expand-combo': _collapseExpandCombo.default
};
(0, _each.default)(behaviors, function (behavior, type) {
  _behavior.default.registerBehavior(type, behavior);
});
var _default = _behavior.default;
exports.default = _default;