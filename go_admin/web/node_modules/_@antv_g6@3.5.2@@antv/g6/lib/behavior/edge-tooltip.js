"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _tooltipBase = _interopRequireDefault(require("./tooltip-base"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var _default = Object.assign({
  getDefaultCfg: function getDefaultCfg() {
    return {
      item: 'edge',
      offset: 12,
      formatText: function formatText(model) {
        return "source: " + model.source + " target: " + model.target;
      }
    };
  },
  getEvents: function getEvents() {
    return {
      'edge:mouseenter': 'onMouseEnter',
      'edge:mouseleave': 'onMouseLeave',
      'edge:mousemove': 'onMouseMove'
    };
  }
}, _tooltipBase.default);

exports.default = _default;