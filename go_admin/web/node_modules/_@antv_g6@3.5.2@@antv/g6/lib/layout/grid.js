"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _tslib = require("tslib");

var _isString = _interopRequireDefault(require("@antv/util/lib/is-string"));

var _layout = require("./layout");

var _util = require("@antv/util");

var _math = require("../util/math");

var _base = require("../util/base");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * @fileOverview grid layout
 * @author shiwu.wyy@antfin.com
 * this algorithm refers to <cytoscape.js> - https://github.com/cytoscape/cytoscape.js/
 */

/**
 * 网格布局
 */
var GridLayout =
/** @class */
function (_super) {
  (0, _tslib.__extends)(GridLayout, _super);

  function GridLayout() {
    var _this = _super !== null && _super.apply(this, arguments) || this;
    /** 布局起始点 */


    _this.begin = [0, 0];
    /** prevents node overlap, may overflow boundingBox if not enough space */

    _this.preventOverlap = true;
    /** extra spacing around nodes when preventOverlap: true */

    _this.preventOverlapPadding = 10;
    /** uses all available space on false, uses minimal space on true */

    _this.condense = false;
    /** a sorting function to order the nodes; e.g. function(a, b){ return a.datapublic ('weight') - b.data('weight') } */

    _this.sortBy = 'degree';
    _this.nodeSize = 30;
    _this.nodes = [];
    _this.edges = [];
    /** 布局中心 */

    _this.center = [0, 0];
    _this.width = 300;
    _this.height = 300;
    _this.row = 0;
    _this.col = 0;
    _this.cellWidth = 0;
    _this.cellHeight = 0;
    _this.cellUsed = {};
    _this.id2manPos = {};
    return _this;
  }

  GridLayout.prototype.getDefaultCfg = function () {
    return {
      begin: [0, 0],
      preventOverlap: true,
      preventOverlapPadding: 10,
      condense: false,
      rows: undefined,
      cols: undefined,
      position: undefined,
      sortBy: 'degree',
      nodeSize: 30
    };
  };
  /**
   * 执行布局
   */


  GridLayout.prototype.execute = function () {
    var self = this;
    var nodes = self.nodes;
    var n = nodes.length;
    var center = self.center;

    if (n === 0) {
      return;
    }

    if (n === 1) {
      nodes[0].x = center[0];
      nodes[0].y = center[1];
      return;
    }

    var edges = self.edges;
    var layoutNodes = [];
    nodes.forEach(function (node) {
      layoutNodes.push(node);
    });
    var nodeIdxMap = {};
    layoutNodes.forEach(function (node, i) {
      nodeIdxMap[node.id] = i;
    });

    if (self.sortBy === 'degree' || !(0, _isString.default)(self.sortBy) || layoutNodes[0][self.sortBy] === undefined) {
      self.sortBy = 'degree';

      if ((0, _base.isNaN)(nodes[0].degree)) {
        var values_1 = (0, _math.getDegree)(layoutNodes.length, nodeIdxMap, edges);
        layoutNodes.forEach(function (node, i) {
          node.degree = values_1[i];
        });
      }
    } // sort nodes by value


    layoutNodes.sort(function (n1, n2) {
      return n2[self.sortBy] - n1[self.sortBy];
    });

    if (!self.width && typeof window !== 'undefined') {
      self.width = window.innerWidth;
    }

    if (!self.height && typeof window !== 'undefined') {
      self.height = window.innerHeight;
    }

    var oRows = self.rows;
    var oCols = self.cols != null ? self.cols : self.columns;
    self.cells = n; // if rows or columns were set in self, use those values

    if (oRows != null && oCols != null) {
      self.rows = oRows;
      self.cols = oCols;
    } else if (oRows != null && oCols == null) {
      self.rows = oRows;
      self.cols = Math.ceil(self.cells / self.rows);
    } else if (oRows == null && oCols != null) {
      self.cols = oCols;
      self.rows = Math.ceil(self.cells / self.cols);
    } else {
      // otherwise use the automatic values and adjust accordingly	      // otherwise use the automatic values and adjust accordingly
      // width/height * splits^2 = cells where splits is number of times to split width
      self.splits = Math.sqrt(self.cells * self.height / self.width);
      self.rows = Math.round(self.splits);
      self.cols = Math.round(self.width / self.height * self.splits);
    }

    if (self.cols * self.rows > self.cells) {
      // otherwise use the automatic values and adjust accordingly
      // if rounding was up, see if we can reduce rows or columns
      var sm = self.small();
      var lg = self.large(); // reducing the small side takes away the most cells, so try it first

      if ((sm - 1) * lg >= self.cells) {
        self.small(sm - 1);
      } else if ((lg - 1) * sm >= self.cells) {
        self.large(lg - 1);
      }
    } else {
      // if rounding was too low, add rows or columns
      while (self.cols * self.rows < self.cells) {
        var sm = self.small();
        var lg = self.large(); // try to add to larger side first (adds less in multiplication)

        if ((lg + 1) * sm >= self.cells) {
          self.large(lg + 1);
        } else {
          self.small(sm + 1);
        }
      }
    }

    self.cellWidth = self.width / self.cols;
    self.cellHeight = self.height / self.rows;

    if (self.condense) {
      self.cellWidth = 0;
      self.cellHeight = 0;
    }

    if (self.preventOverlap) {
      layoutNodes.forEach(function (node) {
        if (!node.x || !node.y) {
          // for bb
          node.x = 0;
          node.y = 0;
        }

        var nodew;
        var nodeh;

        if ((0, _util.isArray)(node.size)) {
          nodew = node.size[0];
          nodeh = node.size[1];
        } else if ((0, _util.isNumber)(node.size)) {
          nodew = node.size;
          nodeh = node.size;
        }

        if (nodew === undefined || nodeh === undefined) {
          if ((0, _util.isArray)(self.nodeSize)) {
            nodew = self.nodeSize[0];
            nodeh = self.nodeSize[1];
          } else if ((0, _util.isNumber)(self.nodeSize)) {
            nodew = self.nodeSize;
            nodeh = self.nodeSize;
          } else {
            nodew = 30;
            nodeh = 30;
          }
        }

        var p = self.preventOverlapPadding;
        var w = nodew + p;
        var h = nodeh + p;
        self.cellWidth = Math.max(self.cellWidth, w);
        self.cellHeight = Math.max(self.cellHeight, h);
      });
    }

    self.cellUsed = {}; // e.g. 'c-0-2' => true
    // to keep track of current cell position

    self.row = 0;
    self.col = 0; // get a cache of all the manual positions

    self.id2manPos = {};

    for (var i = 0; i < layoutNodes.length; i++) {
      var node = layoutNodes[i];
      var rcPos = void 0;

      if (self.position) {
        rcPos = self.position(node);
      }

      if (rcPos && (rcPos.row !== undefined || rcPos.col !== undefined)) {
        // must have at least row or col def'd
        var pos = {
          row: rcPos.row,
          col: rcPos.col
        };

        if (pos.col === undefined) {
          // find unused col
          pos.col = 0;

          while (self.used(pos.row, pos.col)) {
            pos.col++;
          }
        } else if (pos.row === undefined) {
          // find unused row
          pos.row = 0;

          while (self.used(pos.row, pos.col)) {
            pos.row++;
          }
        }

        self.id2manPos[node.id] = pos;
        self.use(pos.row, pos.col);
      }

      self.getPos(node);
    }
  };

  GridLayout.prototype.small = function (val) {
    var self = this;
    var res;
    var rows = self.rows || 5;
    var cols = self.cols || 5;

    if (val == null) {
      res = Math.min(rows, cols);
    } else {
      var min = Math.min(rows, cols);

      if (min === self.rows) {
        self.rows = val;
      } else {
        self.cols = val;
      }
    }

    return res;
  };

  GridLayout.prototype.large = function (val) {
    var self = this;
    var res;
    var rows = self.rows || 5;
    var cols = self.cols || 5;

    if (val == null) {
      res = Math.max(rows, cols);
    } else {
      var max = Math.max(rows, cols);

      if (max === self.rows) {
        self.rows = val;
      } else {
        self.cols = val;
      }
    }

    return res;
  };

  GridLayout.prototype.used = function (row, col) {
    var self = this;
    return self.cellUsed["c-" + row + "-" + col] || false;
  };

  GridLayout.prototype.use = function (row, col) {
    var self = this;
    self.cellUsed["c-" + row + "-" + col] = true;
  };

  GridLayout.prototype.moveToNextCell = function () {
    var self = this;
    var cols = self.cols || 5;
    self.col++;

    if (self.col >= cols) {
      self.col = 0;
      self.row++;
    }
  };

  GridLayout.prototype.getPos = function (node) {
    var self = this;
    var begin = self.begin;
    var cellWidth = self.cellWidth;
    var cellHeight = self.cellHeight;
    var x;
    var y; // see if we have a manual position set

    var rcPos = self.id2manPos[node.id];

    if (rcPos) {
      x = rcPos.col * cellWidth + cellWidth / 2 + begin[0];
      y = rcPos.row * cellHeight + cellHeight / 2 + begin[1];
    } else {
      // otherwise set automatically
      while (self.used(self.row, self.col)) {
        self.moveToNextCell();
      }

      x = self.col * cellWidth + cellWidth / 2 + begin[0];
      y = self.row * cellHeight + cellHeight / 2 + begin[1];
      self.use(self.row, self.col);
      self.moveToNextCell();
    }

    node.x = x;
    node.y = y;
  };

  return GridLayout;
}(_layout.BaseLayout);

var _default = GridLayout;
exports.default = _default;