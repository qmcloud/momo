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
      item: 'node',
      offset: 12,
      formatText: function formatText(model) {
        return model.label;
      }
    };
  },
  getEvents: function getEvents() {
    return {
      'node:mouseenter': 'onMouseEnter',
      'node:mouseleave': 'onMouseLeave',
      'node:mousemove': 'onMouseMove'
    };
  }
}, _tooltipBase.default);

exports.default = _default;