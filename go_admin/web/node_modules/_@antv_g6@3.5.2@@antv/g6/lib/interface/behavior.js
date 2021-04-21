"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.G6GraphEvent = void 0;

var _tslib = require("tslib");

var _graphEvent = _interopRequireDefault(require("@antv/g-base/lib/event/graph-event"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var G6GraphEvent =
/** @class */
function (_super) {
  (0, _tslib.__extends)(G6GraphEvent, _super);

  function G6GraphEvent(type, event) {
    var _this = _super.call(this, type, event) || this;

    _this.item = event.item;
    _this.canvasX = event.canvasX;
    _this.canvasY = event.canvasY;
    _this.wheelDelta = event.wheelDelta;
    _this.detail = event.detail;
    return _this;
  }

  return G6GraphEvent;
}(_graphEvent.default);

exports.G6GraphEvent = G6GraphEvent;