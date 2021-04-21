"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _tslib = require("tslib");

var _layout = require("./layout");

var _util = require("@antv/util");

/**
 * @fileOverview fruchterman layout
 * @author shiwu.wyy@antfin.com
 */
var SPEED_DIVISOR = 800;
/**
 * fruchterman 布局
 */

var FruchtermanLayout =
/** @class */
function (_super) {
  (0, _tslib.__extends)(FruchtermanLayout, _super);

  function FruchtermanLayout() {
    var _this = _super !== null && _super.apply(this, arguments) || this;
    /** 布局中心 */


    _this.center = [0, 0];
    /** 停止迭代的最大迭代数 */

    _this.maxIteration = 1000;
    /** 重力大小，影响图的紧凑程度 */

    _this.gravity = 10;
    /** 速度 */

    _this.speed = 1;
    /** 是否产生聚类力 */

    _this.clustering = false;
    /** 聚类力大小 */

    _this.clusterGravity = 10;
    _this.nodes = [];
    _this.edges = [];
    _this.width = 300;
    _this.height = 300;
    _this.nodeMap = {};
    _this.nodeIdxMap = {};
    return _this;
  }

  FruchtermanLayout.prototype.getDefaultCfg = function () {
    return {
      maxIteration: 1000,
      center: [0, 0],
      gravity: 10,
      speed: 1,
      clustering: false,
      clusterGravity: 10
    };
  };
  /**
   * 执行布局
   */


  FruchtermanLayout.prototype.execute = function () {
    var _this = this;

    var self = this;
    var nodes = self.nodes;
    var center = self.center;

    if (!nodes || nodes.length === 0) {
      return;
    }

    if (nodes.length === 1) {
      nodes[0].x = center[0];
      nodes[0].y = center[1];
      return;
    }

    var nodeMap = {};
    var nodeIdxMap = {};
    nodes.forEach(function (node, i) {
      if (!(0, _util.isNumber)(node.x)) node.x = Math.random() * _this.width;
      if (!(0, _util.isNumber)(node.y)) node.y = Math.random() * _this.height;
      nodeMap[node.id] = node;
      nodeIdxMap[node.id] = i;
    });
    self.nodeMap = nodeMap;
    self.nodeIdxMap = nodeIdxMap; // layout

    self.run();
  };

  FruchtermanLayout.prototype.run = function () {
    var self = this;
    var nodes = self.nodes;
    var edges = self.edges;
    var maxIteration = self.maxIteration;

    if (!self.width && typeof window !== 'undefined') {
      self.width = window.innerWidth;
    }

    if (!self.height && typeof window !== 'undefined') {
      self.height = window.innerHeight;
    }

    var center = self.center;
    var maxDisplace = self.width / 10;
    var k = Math.sqrt(self.width * self.height / (nodes.length + 1));
    var gravity = self.gravity;
    var speed = self.speed;
    var clustering = self.clustering;
    var clusterMap = {};

    if (clustering) {
      nodes.forEach(function (n) {
        if (clusterMap[n.cluster] === undefined) {
          var cluster = {
            name: n.cluster,
            cx: 0,
            cy: 0,
            count: 0
          };
          clusterMap[n.cluster] = cluster;
        }

        var c = clusterMap[n.cluster];

        if ((0, _util.isNumber)(n.x)) {
          c.cx += n.x;
        }

        if ((0, _util.isNumber)(n.y)) {
          c.cy += n.y;
        }

        c.count++;
      });

      for (var key in clusterMap) {
        clusterMap[key].cx /= clusterMap[key].count;
        clusterMap[key].cy /= clusterMap[key].count;
      }
    }

    var _loop_1 = function _loop_1(i) {
      var displacements = [];
      nodes.forEach(function (_, j) {
        displacements[j] = {
          x: 0,
          y: 0
        };
      });
      self.applyCalculate(nodes, edges, displacements, k); // gravity for clusters

      if (clustering) {
        var clusterGravity_1 = self.clusterGravity || gravity;
        nodes.forEach(function (n, j) {
          if (!(0, _util.isNumber)(n.x) || !(0, _util.isNumber)(n.y)) return;
          var c = clusterMap[n.cluster];
          var distLength = Math.sqrt((n.x - c.cx) * (n.x - c.cx) + (n.y - c.cy) * (n.y - c.cy));
          var gravityForce = k * clusterGravity_1;
          displacements[j].x -= gravityForce * (n.x - c.cx) / distLength;
          displacements[j].y -= gravityForce * (n.y - c.cy) / distLength;
        });

        for (var key in clusterMap) {
          clusterMap[key].cx = 0;
          clusterMap[key].cy = 0;
          clusterMap[key].count = 0;
        }

        nodes.forEach(function (n) {
          var c = clusterMap[n.cluster];

          if ((0, _util.isNumber)(n.x)) {
            c.cx += n.x;
          }

          if ((0, _util.isNumber)(n.y)) {
            c.cy += n.y;
          }

          c.count++;
        });

        for (var key in clusterMap) {
          clusterMap[key].cx /= clusterMap[key].count;
          clusterMap[key].cy /= clusterMap[key].count;
        }
      } // gravity


      nodes.forEach(function (n, j) {
        if (!(0, _util.isNumber)(n.x) || !(0, _util.isNumber)(n.y)) return;
        var gravityForce = 0.01 * k * gravity;
        displacements[j].x -= gravityForce * (n.x - center[0]);
        displacements[j].y -= gravityForce * (n.y - center[1]);
      }); // move

      nodes.forEach(function (n, j) {
        if (!(0, _util.isNumber)(n.x) || !(0, _util.isNumber)(n.y)) return;
        var distLength = Math.sqrt(displacements[j].x * displacements[j].x + displacements[j].y * displacements[j].y);

        if (distLength > 0) {
          // && !n.isFixed()
          var limitedDist = Math.min(maxDisplace * (speed / SPEED_DIVISOR), distLength);
          n.x += displacements[j].x / distLength * limitedDist;
          n.y += displacements[j].y / distLength * limitedDist;
        }
      });
    };

    for (var i = 0; i < maxIteration; i++) {
      _loop_1(i);
    }
  };

  FruchtermanLayout.prototype.applyCalculate = function (nodes, edges, displacements, k) {
    var self = this;
    self.calRepulsive(nodes, displacements, k);
    self.calAttractive(edges, displacements, k);
  };

  FruchtermanLayout.prototype.calRepulsive = function (nodes, displacements, k) {
    nodes.forEach(function (v, i) {
      displacements[i] = {
        x: 0,
        y: 0
      };
      nodes.forEach(function (u, j) {
        if (i === j) {
          return;
        }

        if (!(0, _util.isNumber)(v.x) || !(0, _util.isNumber)(u.x) || !(0, _util.isNumber)(v.y) || !(0, _util.isNumber)(u.y)) return;
        var vecX = v.x - u.x;
        var vecY = v.y - u.y;
        var vecLengthSqr = vecX * vecX + vecY * vecY;

        if (vecLengthSqr === 0) {
          vecLengthSqr = 1;
          var sign = i > j ? 1 : -1;
          vecX = 0.01 * sign;
          vecY = 0.01 * sign;
        }

        var common = k * k / vecLengthSqr;
        displacements[i].x += vecX * common;
        displacements[i].y += vecY * common;
      });
    });
  };

  FruchtermanLayout.prototype.calAttractive = function (edges, displacements, k) {
    var _this = this;

    edges.forEach(function (e) {
      if (!e.source || !e.target) return;
      var uIndex = _this.nodeIdxMap[e.source];
      var vIndex = _this.nodeIdxMap[e.target];

      if (uIndex === vIndex) {
        return;
      }

      var u = _this.nodeMap[e.source];
      var v = _this.nodeMap[e.target];
      if (!(0, _util.isNumber)(v.x) || !(0, _util.isNumber)(u.x) || !(0, _util.isNumber)(v.y) || !(0, _util.isNumber)(u.y)) return;
      var vecX = v.x - u.x;
      var vecY = v.y - u.y;
      var vecLength = Math.sqrt(vecX * vecX + vecY * vecY);
      var common = vecLength * vecLength / k;
      displacements[vIndex].x -= vecX / vecLength * common;
      displacements[vIndex].y -= vecY / vecLength * common;
      displacements[uIndex].x += vecX / vecLength * common;
      displacements[uIndex].y += vecY / vecLength * common;
    });
  };

  return FruchtermanLayout;
}(_layout.BaseLayout);

var _default = FruchtermanLayout;
exports.default = _default;