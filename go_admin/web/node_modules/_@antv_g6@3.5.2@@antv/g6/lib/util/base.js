"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.calculationItemsBBox = exports.isNaN = exports.isViewportChanged = exports.cloneEvent = exports.formatPadding = void 0;

var _isArray = _interopRequireDefault(require("@antv/util/lib/is-array"));

var _isNil = _interopRequireDefault(require("@antv/util/lib/is-nil"));

var _isNumber = _interopRequireDefault(require("@antv/util/lib/is-number"));

var _isString = _interopRequireDefault(require("@antv/util/lib/is-string"));

var _behavior = require("../interface/behavior");

var _lib = require("@antv/matrix-util/lib");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * turn padding into [top, right, bottom, right]
 * @param  {Number|Array} padding input padding
 * @return {array} output
 */
var formatPadding = function formatPadding(padding) {
  var top = 0;
  var left = 0;
  var right = 0;
  var bottom = 0;

  if ((0, _isNumber.default)(padding)) {
    top = left = right = bottom = padding;
  } else if ((0, _isString.default)(padding)) {
    var intPadding = parseInt(padding, 10);
    top = left = right = bottom = intPadding;
  } else if ((0, _isArray.default)(padding)) {
    top = padding[0];
    right = !(0, _isNil.default)(padding[1]) ? padding[1] : padding[0];
    bottom = !(0, _isNil.default)(padding[2]) ? padding[2] : padding[0];
    left = !(0, _isNil.default)(padding[3]) ? padding[3] : right;
  }

  return [top, right, bottom, left];
};
/**
 * clone event
 * @param e
 */


exports.formatPadding = formatPadding;

var cloneEvent = function cloneEvent(e) {
  var event = new _behavior.G6GraphEvent(e.type, e);
  event.clientX = e.clientX;
  event.clientY = e.clientY;
  event.x = e.x;
  event.y = e.y;
  event.target = e.target;
  event.currentTarget = e.currentTarget;
  event.bubbles = true;
  event.item = e.item;
  return event;
};
/**
 * 判断 viewport 是否改变，通过和单位矩阵对比
 * @param matrix Viewport 的 Matrix
 */


exports.cloneEvent = cloneEvent;

var isViewportChanged = function isViewportChanged(matrix) {
  // matrix 为 null， 则说明没有变化
  if (!matrix) {
    return false;
  }

  var MATRIX_LEN = 9;

  var ORIGIN_MATRIX = _lib.mat3.create();

  for (var i = 0; i < MATRIX_LEN; i++) {
    if (matrix[i] !== ORIGIN_MATRIX[i]) {
      return true;
    }
  }

  return false;
};

exports.isViewportChanged = isViewportChanged;

var isNaN = function isNaN(input) {
  return Number.isNaN(Number(input));
};
/**
 * 计算一组 Item 的 BBox
 * @param items 选中的一组Item，可以是 node 或 combo
 */


exports.isNaN = isNaN;

var calculationItemsBBox = function calculationItemsBBox(items) {
  var minx = Infinity;
  var maxx = -Infinity;
  var miny = Infinity;
  var maxy = -Infinity; // 获取已节点的所有最大最小x y值

  for (var i = 0; i < items.length; i++) {
    var element = items[i];
    var bbox = element.getBBox();
    var minX = bbox.minX,
        minY = bbox.minY,
        maxX = bbox.maxX,
        maxY = bbox.maxY;

    if (minX < minx) {
      minx = minX;
    }

    if (minY < miny) {
      miny = minY;
    }

    if (maxX > maxx) {
      maxx = maxX;
    }

    if (maxY > maxy) {
      maxy = maxY;
    }
  }

  var x = Math.floor(minx);
  var y = Math.floor(miny);
  var width = Math.ceil(maxx) - Math.floor(minx);
  var height = Math.ceil(maxy) - Math.floor(miny);
  return {
    x: x,
    y: y,
    width: width,
    height: height,
    minX: minx,
    minY: miny
  };
};

exports.calculationItemsBBox = calculationItemsBBox;