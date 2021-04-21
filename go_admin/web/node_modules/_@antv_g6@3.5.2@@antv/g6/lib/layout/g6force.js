"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _tslib = require("tslib");

var _layout = require("./layout");

var _util = require("@antv/util");

/**
 * @fileOverview G6's force layout, supports clustering
 * @author shiwu.wyy@antfin.com
 */

/**
 * G6's force layout
 */
var G6Force =
/** @class */
function (_super) {
  (0, _tslib.__extends)(G6Force, _super);

  function G6Force() {
    var _this = _super !== null && _super.apply(this, arguments) || this;
    /** 布局中心 */


    _this.center = [0, 0];
    /** 停止迭代的最大迭代数 */

    _this.maxIteration = 500;
    /** 重力大小，影响图的紧凑程度 */

    _this.gravity = 10;
    /** 是否产生聚类力 */

    _this.clustering = false;
    /** 聚类力大小 */

    _this.clusterGravity = 10;
    /** 默认边长度 */

    _this.linkDistance = 50;
    /** 每次迭代位移的衰减相关参数 */

    _this.alpha = 1;
    _this.alphaMin = 0.001;
    _this.alphaDecay = 1 - Math.pow(_this.alphaMin, 1 / 300);
    _this.alphaTarget = 0;
    /** 节点运动速度衰减参数 */

    _this.velocityDecay = 0.6;
    /** 边引力大小 */

    _this.linkStrength = 1;
    /** 节点引力大小 */

    _this.nodeStrength = 30;
    /** 是否开启防止重叠 */

    _this.preventOverlap = false;
    /** 防止重叠的碰撞力大小 */

    _this.collideStrength = 1;
    /** 优化计算斥力的速度，两节点间距超过 optimizeRangeFactor * width 则不再计算斥力和重叠斥力 */

    _this.optimizeRangeFactor = 1;
    /** 每次迭代的回调函数 */

    _this.tick = function () {};
    /** 内部计算参数 */


    _this.nodes = [];
    _this.edges = [];
    _this.width = 300;
    _this.height = 300;
    _this.bias = [];
    _this.nodeMap = {};
    _this.nodeIdxMap = {};
    return _this;
  }

  G6Force.prototype.getDefaultCfg = function () {
    return {
      maxIteration: 1000,
      center: [0, 0],
      gravity: 10,
      speed: 1,
      clustering: false,
      clusterGravity: 10,
      preventOverlap: false,
      nodeSpacing: undefined,
      collideStrength: 10
    };
  };
  /**
   * 执行布局
   */


  G6Force.prototype.execute = function () {
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
      nodeMap[node.id] = node;
      nodeIdxMap[node.id] = i;
    });
    self.nodeMap = nodeMap;
    self.nodeIdxMap = nodeIdxMap; // layout

    self.run();
  };

  G6Force.prototype.run = function () {
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
    var velocityDecay = self.velocityDecay;
    var clustering = self.clustering;
    var clusterMap;
    self.initVals();

    if (clustering) {
      clusterMap = self.getClusterMap();
    }

    var _loop_1 = function _loop_1(i) {
      var displacements = [];
      nodes.forEach(function (_, j) {
        displacements[j] = {
          x: 0,
          y: 0
        };
      });
      self.applyCalculate(nodes, edges, displacements); // gravity for clusters

      if (clustering) {
        self.applyClusterForce(clusterMap, displacements);
      } // move


      nodes.forEach(function (n, j) {
        if (!(0, _util.isNumber)(n.x) || !(0, _util.isNumber)(n.y)) return;
        n.x += displacements[j].x * velocityDecay;
        n.y += displacements[j].y * velocityDecay;
      });
      this_1.alpha += (this_1.alphaTarget - this_1.alpha) * this_1.alphaDecay;
      self.tick();
    };

    var this_1 = this; // iterate

    for (var i = 0; i < maxIteration; i++) {
      _loop_1(i);
    } // move to center


    var meanCenter = [0, 0];
    nodes.forEach(function (n) {
      if (!(0, _util.isNumber)(n.x) || !(0, _util.isNumber)(n.y)) return;
      meanCenter[0] += n.x;
      meanCenter[1] += n.y;
    });
    meanCenter[0] /= nodes.length;
    meanCenter[1] /= nodes.length;
    var centerOffset = [center[0] - meanCenter[0], center[1] - meanCenter[1]];
    nodes.forEach(function (n, j) {
      if (!(0, _util.isNumber)(n.x) || !(0, _util.isNumber)(n.y)) return;
      n.x += centerOffset[0];
      n.y += centerOffset[1];
    });
  };

  G6Force.prototype.initVals = function () {
    var self = this;
    var edges = self.edges;
    var count = {}; // get edge bias

    for (var i = 0; i < edges.length; ++i) {
      if (count[edges[i].source]) count[edges[i].source]++;else count[edges[i].source] = 1;
      if (count[edges[i].target]) count[edges[i].target]++;else count[edges[i].target] = 1;
    }

    var bias = [];

    for (var i = 0; i < edges.length; ++i) {
      bias[i] = count[edges[i].source] / (count[edges[i].source] + count[edges[i].target]);
    }

    this.bias = bias;
    var nodeSize = self.nodeSize;
    var nodeSpacing = self.nodeSpacing;
    var nodeSizeFunc;
    var nodeSpacingFunc; // nodeSpacing to function

    if ((0, _util.isNumber)(nodeSpacing)) {
      nodeSpacingFunc = function nodeSpacingFunc() {
        return nodeSpacing;
      };
    } else if ((0, _util.isFunction)(nodeSpacing)) {
      nodeSpacingFunc = nodeSpacing;
    } else {
      nodeSpacingFunc = function nodeSpacingFunc() {
        return 0;
      };
    } // nodeSize to function


    if (!nodeSize) {
      nodeSizeFunc = function nodeSizeFunc(d) {
        if (d.size) {
          if ((0, _util.isArray)(d.size)) {
            var res = d.size[0] > d.size[1] ? d.size[0] : d.size[1];
            return res / 2 + nodeSpacingFunc(d);
          }

          return d.size / 2 + nodeSpacingFunc(d);
        }

        return 10 + nodeSpacingFunc(d);
      };
    } else if ((0, _util.isFunction)(nodeSize)) {
      nodeSizeFunc = function nodeSizeFunc(d) {
        var size = nodeSize(d);
        return size + nodeSpacingFunc(d);
      };
    } else if ((0, _util.isArray)(nodeSize)) {
      var larger = nodeSize[0] > nodeSize[1] ? nodeSize[0] : nodeSize[1];
      var radius_1 = larger / 2;

      nodeSizeFunc = function nodeSizeFunc(d) {
        return radius_1 + nodeSpacingFunc(d);
      };
    } else if ((0, _util.isNumber)(nodeSize)) {
      var radius_2 = nodeSize / 2;

      nodeSizeFunc = function nodeSizeFunc(d) {
        return radius_2 + nodeSpacingFunc(d);
      };
    } else {
      nodeSizeFunc = function nodeSizeFunc() {
        return 10;
      };
    }

    this.nodeSize = nodeSizeFunc; // linkDistance to function

    var linkDistance = this.linkDistance;
    var linkDistanceFunc;

    if (linkDistance) {
      linkDistance = 50;
    }

    if ((0, _util.isNumber)(linkDistance)) {
      linkDistanceFunc = function linkDistanceFunc(d) {
        return linkDistance;
      };
    }

    this.linkDistance = linkDistanceFunc; // linkStrength to function

    var linkStrength = this.linkStrength;
    var linkStrengthFunc;

    if (!linkStrength) {
      linkStrength = 1;
    }

    if ((0, _util.isNumber)(linkStrength)) {
      linkStrengthFunc = function linkStrengthFunc(d) {
        return linkStrength;
      };
    }

    this.linkStrength = linkStrengthFunc; // nodeStrength to function

    var nodeStrength = this.nodeStrength;
    var nodeStrengthFunc;

    if (!nodeStrength) {
      nodeStrength = 30;
    }

    if ((0, _util.isNumber)(nodeStrength)) {
      nodeStrengthFunc = function nodeStrengthFunc(d) {
        return nodeStrength;
      };
    }

    this.nodeStrength = nodeStrengthFunc;
  };

  G6Force.prototype.getClusterMap = function () {
    var self = this;
    var nodes = self.nodes;
    var clusterMap = {};
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

    return clusterMap;
  };

  G6Force.prototype.applyClusterForce = function (clusterMap, displacements) {
    var self = this;
    var gravity = self.gravity;
    var nodes = self.nodes;
    var clusterGravity = self.clusterGravity || gravity;
    var alpha = this.alpha;
    nodes.forEach(function (n, j) {
      if (!(0, _util.isNumber)(n.x) || !(0, _util.isNumber)(n.y)) return;
      var c = clusterMap[n.cluster];
      var vecX = n.x - c.cx;
      var vecY = n.y - c.cy;
      var l = Math.sqrt(vecX * vecX + vecY * vecY);
      displacements[j].x -= vecX * clusterGravity * alpha / l;
      displacements[j].y -= vecY * clusterGravity * alpha / l;
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
  };

  G6Force.prototype.applyCalculate = function (nodes, edges, displacements) {
    var self = this; // store the vx, vy, and distance to reduce dulplicate calculation

    var vecMap = {};
    nodes.forEach(function (v, i) {
      displacements[i] = {
        x: 0,
        y: 0
      };
      nodes.forEach(function (u, j) {
        if (i < j) return;
        var vx = v.x - u.x;
        var vy = v.y - u.y;
        var vl = vx * vx + vy * vy;
        if (vl < 1) vl = Math.sqrt(vl);

        if (vx === 0) {
          vx = Math.random() * 0.01;
          vl += vx * vx;
        }

        if (vy === 0) {
          vy = Math.random() * 0.01;
          vl += vy * vy;
        }

        vecMap[v.id + "-" + u.id] = {
          vx: vx,
          vy: vy,
          vl: vl
        };
        vecMap[u.id + "-" + v.id] = {
          vx: -vx,
          vy: -vy,
          vl: vl
        };
      });
    });
    self.calRepulsive(nodes, displacements, vecMap);
    self.calAttractive(edges, displacements, vecMap);
  };

  G6Force.prototype.calRepulsive = function (nodes, displacements, vecMap) {
    var max = this.width * this.optimizeRangeFactor * this.width * this.optimizeRangeFactor;
    var nodeStrength = this.nodeStrength;
    ;
    var alpha = this.alpha;
    var collideStrength = this.collideStrength;
    var preventOverlap = this.preventOverlap;
    var nodeSizeFunc = this.nodeSize;
    nodes.forEach(function (v, i) {
      nodes.forEach(function (u, j) {
        if (i === j) {
          return;
        }

        if (!(0, _util.isNumber)(v.x) || !(0, _util.isNumber)(u.x) || !(0, _util.isNumber)(v.y) || !(0, _util.isNumber)(u.y)) return;
        var _a = vecMap[v.id + "-" + u.id],
            vl = _a.vl,
            vx = _a.vx,
            vy = _a.vy;
        if (vl > max) return;
        displacements[i].x += vx * nodeStrength(u) * alpha / vl;
        displacements[i].y += vy * nodeStrength(u) * alpha / vl; // collide strength

        if (preventOverlap && i < j) {
          var ri = nodeSizeFunc(v);
          var rj = nodeSizeFunc(u);
          var r = ri + rj;

          if (vl < r * r) {
            var sqrtl = Math.sqrt(vl);
            var ll = (r - sqrtl) / sqrtl * collideStrength;
            var rratio = rj * rj / (ri * ri + rj * rj);
            var xl = vx * ll;
            var yl = vy * ll;
            displacements[i].x += xl * rratio;
            displacements[i].y += yl * rratio;
            rratio = 1 - rratio;
            displacements[j].x -= xl * rratio;
            displacements[j].y -= yl * rratio;
          }
        }
      });
    });
  };

  G6Force.prototype.calAttractive = function (edges, displacements, vecMap) {
    var _this = this;

    var linkDistance = this.linkDistance;
    var alpha = this.alpha;
    var linkStrength = this.linkStrength;
    var bias = this.bias;
    edges.forEach(function (e, i) {
      if (!e.source || !e.target) return;
      var uIndex = _this.nodeIdxMap[e.source];
      var vIndex = _this.nodeIdxMap[e.target];

      if (uIndex === vIndex) {
        return;
      }

      var u = _this.nodeMap[e.source];
      var v = _this.nodeMap[e.target];
      if (!(0, _util.isNumber)(v.x) || !(0, _util.isNumber)(u.x) || !(0, _util.isNumber)(v.y) || !(0, _util.isNumber)(u.y)) return;
      var _a = vecMap[e.target + "-" + e.source],
          vl = _a.vl,
          vx = _a.vx,
          vy = _a.vy;
      var l = (vl - linkDistance(e)) / vl * alpha * linkStrength(e);
      var vecX = vx * l;
      var vecY = vy * l;
      var b = bias[i];
      displacements[vIndex].x -= vecX * b;
      displacements[vIndex].y -= vecY * b;
      displacements[uIndex].x += vecX * (1 - b);
      displacements[uIndex].y += vecY * (1 - b);
    });
  };

  return G6Force;
}(_layout.BaseLayout);

var _default = G6Force;
exports.default = _default;