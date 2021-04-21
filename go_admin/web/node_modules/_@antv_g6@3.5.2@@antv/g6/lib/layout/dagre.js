"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _tslib = require("tslib");

var _dagre = _interopRequireDefault(require("dagre"));

var _isArray = _interopRequireDefault(require("@antv/util/lib/is-array"));

var _layout = require("./layout");

var _util = require("@antv/util");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * @fileOverview random layout
 * @author shiwu.wyy@antfin.com
 */

/**
 * 层次布局
 */
var DagreLayout =
/** @class */
function (_super) {
  (0, _tslib.__extends)(DagreLayout, _super);

  function DagreLayout() {
    var _this = _super !== null && _super.apply(this, arguments) || this;
    /** layout 方向, 可选 TB, BT, LR, RL */


    _this.rankdir = 'TB';
    /** 节点水平间距(px) */

    _this.nodesep = 50;
    /** 每一层节点之间间距 */

    _this.ranksep = 50;
    /** 是否保留布局连线的控制点 */

    _this.controlPoints = false;
    return _this;
  }

  DagreLayout.prototype.getDefaultCfg = function () {
    return {
      rankdir: 'TB',
      align: undefined,
      nodeSize: undefined,
      nodesepFunc: undefined,
      ranksepFunc: undefined,
      nodesep: 50,
      ranksep: 50,
      controlPoints: false
    };
  };
  /**
   * 执行布局
   */


  DagreLayout.prototype.execute = function () {
    var self = this;
    var nodes = self.nodes,
        nodeSize = self.nodeSize,
        rankdir = self.rankdir;
    if (!nodes) return;
    var edges = self.edges || [];
    var g = new _dagre.default.graphlib.Graph();
    var nodeSizeFunc;

    if (!nodeSize) {
      nodeSizeFunc = function nodeSizeFunc(d) {
        if (d.size) {
          if ((0, _isArray.default)(d.size)) {
            return d.size;
          }

          return [d.size, d.size];
        }

        return [40, 40];
      };
    } else if ((0, _isArray.default)(nodeSize)) {
      nodeSizeFunc = function nodeSizeFunc() {
        return nodeSize;
      };
    } else {
      nodeSizeFunc = function nodeSizeFunc() {
        return [nodeSize, nodeSize];
      };
    }

    var horisep = getFunc(self.nodesepFunc, self.nodesep, 50);
    var vertisep = getFunc(self.ranksepFunc, self.ranksep, 50);

    if (rankdir === 'LR' || rankdir === 'RL') {
      horisep = getFunc(self.ranksepFunc, self.ranksep, 50);
      vertisep = getFunc(self.nodesepFunc, self.nodesep, 50);
    }

    g.setDefaultEdgeLabel(function () {
      return {};
    });
    g.setGraph(self);
    nodes.forEach(function (node) {
      var size = nodeSizeFunc(node);
      var verti = vertisep(node);
      var hori = horisep(node);
      var width = size[0] + 2 * hori;
      var height = size[1] + 2 * verti;
      g.setNode(node.id, {
        width: width,
        height: height
      });
    });
    edges.forEach(function (edge) {
      // dagrejs Wiki https://github.com/dagrejs/dagre/wiki#configuring-the-layout
      g.setEdge(edge.source, edge.target, {
        weight: edge.weight || 1
      });
    });

    _dagre.default.layout(g);

    var coord;
    g.nodes().forEach(function (node) {
      coord = g.node(node);
      var i = nodes.findIndex(function (it) {
        return it.id === node;
      });
      nodes[i].x = coord.x;
      nodes[i].y = coord.y;
    });
    g.edges().forEach(function (edge) {
      coord = g.edge(edge);
      var i = edges.findIndex(function (it) {
        return it.source === edge.v && it.target === edge.w;
      });

      if (self.controlPoints && edges[i].type !== 'loop' && edges[i].shape !== 'loop') {
        edges[i].controlPoints = coord.points.slice(1, coord.points.length - 1);
      }
    });
  };

  return DagreLayout;
}(_layout.BaseLayout);

var _default = DagreLayout;
exports.default = _default;

function getFunc(func, value, defaultValue) {
  var resultFunc;

  if (func) {
    resultFunc = func;
  } else if ((0, _util.isNumber)(value)) {
    resultFunc = function resultFunc() {
      return value;
    };
  } else {
    resultFunc = function resultFunc() {
      return defaultValue;
    };
  }

  return resultFunc;
}