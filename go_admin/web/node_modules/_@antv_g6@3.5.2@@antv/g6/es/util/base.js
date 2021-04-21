import isArray from '@antv/util/lib/is-array';
import isNil from '@antv/util/lib/is-nil';
import isNumber from '@antv/util/lib/is-number';
import isString from '@antv/util/lib/is-string';
import { G6GraphEvent } from '../interface/behavior';
import { mat3 } from '@antv/matrix-util/lib';
/**
 * turn padding into [top, right, bottom, right]
 * @param  {Number|Array} padding input padding
 * @return {array} output
 */

export var formatPadding = function formatPadding(padding) {
  var top = 0;
  var left = 0;
  var right = 0;
  var bottom = 0;

  if (isNumber(padding)) {
    top = left = right = bottom = padding;
  } else if (isString(padding)) {
    var intPadding = parseInt(padding, 10);
    top = left = right = bottom = intPadding;
  } else if (isArray(padding)) {
    top = padding[0];
    right = !isNil(padding[1]) ? padding[1] : padding[0];
    bottom = !isNil(padding[2]) ? padding[2] : padding[0];
    left = !isNil(padding[3]) ? padding[3] : right;
  }

  return [top, right, bottom, left];
};
/**
 * clone event
 * @param e
 */

export var cloneEvent = function cloneEvent(e) {
  var event = new G6GraphEvent(e.type, e);
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

export var isViewportChanged = function isViewportChanged(matrix) {
  // matrix 为 null， 则说明没有变化
  if (!matrix) {
    return false;
  }

  var MATRIX_LEN = 9;
  var ORIGIN_MATRIX = mat3.create();

  for (var i = 0; i < MATRIX_LEN; i++) {
    if (matrix[i] !== ORIGIN_MATRIX[i]) {
      return true;
    }
  }

  return false;
};
export var isNaN = function isNaN(input) {
  return Number.isNaN(Number(input));
};
/**
 * 计算一组 Item 的 BBox
 * @param items 选中的一组Item，可以是 node 或 combo
 */

export var calculationItemsBBox = function calculationItemsBBox(items) {
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