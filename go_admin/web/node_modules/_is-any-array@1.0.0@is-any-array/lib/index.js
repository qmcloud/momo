'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const toString = Object.prototype.toString;

function isAnyArray(object) {
  return toString.call(object).endsWith('Array]');
}

exports.default = isAnyArray;
