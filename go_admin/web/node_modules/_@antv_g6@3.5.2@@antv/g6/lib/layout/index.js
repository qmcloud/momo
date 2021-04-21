"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _each = _interopRequireDefault(require("@antv/util/lib/each"));

var _layout = _interopRequireDefault(require("./layout"));

var _circular = _interopRequireDefault(require("./circular"));

var _concentric = _interopRequireDefault(require("./concentric"));

var _dagre = _interopRequireDefault(require("./dagre"));

var _force = _interopRequireDefault(require("./force"));

var _g6force = _interopRequireDefault(require("./g6force"));

var _fruchterman = _interopRequireDefault(require("./fruchterman"));

var _grid = _interopRequireDefault(require("./grid"));

var _mds = _interopRequireDefault(require("./mds"));

var _radial = _interopRequireDefault(require("./radial/radial"));

var _random = _interopRequireDefault(require("./random"));

var _comboForce = _interopRequireDefault(require("./comboForce"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * @fileOverview layout entry file
 * @author shiwu.wyy@antfin.com
 */
var layouts = {
  circular: _circular.default,
  concentric: _concentric.default,
  dagre: _dagre.default,
  force: _force.default,
  g6force: _g6force.default,
  comboForce: _comboForce.default,
  fruchterman: _fruchterman.default,
  grid: _grid.default,
  mds: _mds.default,
  radial: _radial.default,
  random: _random.default
}; // 注册布局

(0, _each.default)(layouts, function (layout, type) {
  _layout.default.registerLayout(type, {}, layout);
});
var _default = _layout.default;
exports.default = _default;