"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var MathUtil = _interopRequireWildcard(require("./math"));

var GraphicUtil = _interopRequireWildcard(require("./graphic"));

var PathUtil = _interopRequireWildcard(require("./path"));

var BaseUtil = _interopRequireWildcard(require("./base"));

var _lib = require("@antv/matrix-util/lib");

var _mix = _interopRequireDefault(require("@antv/util/lib/mix"));

var _deepMix = _interopRequireDefault(require("@antv/util/lib/deep-mix"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _getRequireWildcardCache() { if (typeof WeakMap !== "function") return null; var cache = new WeakMap(); _getRequireWildcardCache = function _getRequireWildcardCache() { return cache; }; return cache; }

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { default: obj }; } var cache = _getRequireWildcardCache(); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj.default = obj; if (cache) { cache.set(obj, newObj); } return newObj; }

var Base = {
  mat3: _lib.mat3,
  mix: _mix.default,
  deepMix: _deepMix.default,
  transform: _lib.transform
};
var Util = Object.assign({}, Base, BaseUtil, GraphicUtil, PathUtil, MathUtil);
var _default = Util;
exports.default = _default;