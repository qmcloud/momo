"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.LAYOUT_MESSAGE = void 0;

/**
 * @fileoverview constants for layout
 * @author changzhe.zb@antfin.com
 */

/** layout message type */
var LAYOUT_MESSAGE = {
  // run layout
  RUN: 'LAYOUT_RUN',
  // layout ended with success
  END: 'LAYOUT_END',
  // layout error
  ERROR: 'LAYOUT_ERROR',
  // layout tick, used in force directed layout
  TICK: 'LAYOUT_TICK'
};
exports.LAYOUT_MESSAGE = LAYOUT_MESSAGE;