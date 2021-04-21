/**
 * @fileOverview Combo force layout
 * @author shiwu.wyy@antfin.com
 */
import { __extends } from "tslib";
import { BaseLayout } from './layout';
import { isNumber, isArray, isFunction } from '@antv/util';
import { traverseTreeUp } from '../util/graphic';
import Global from '../global';
/**
 * force layout for graph with combos
 */

var ComboForce =
/** @class */
function (_super) {
  __extends(ComboForce, _super);

  function ComboForce() {
    var _this = _super !== null && _super.apply(this, arguments) || this;
    /** 布局中心 */


    _this.center = [0, 0];
    /** 停止迭代的最大迭代数 */

    _this.maxIteration = 100;
    /** 重力大小，影响图的紧凑程度 */

    _this.gravity = 10;
    /** 群组中心力大小 */

    _this.comboGravity = 10;
    /** 默认边长度 */

    _this.linkDistance = 10;
    /** 每次迭代位移的衰减相关参数 */

    _this.alpha = 1;
    _this.alphaMin = 0.001;
    _this.alphaDecay = 1 - Math.pow(_this.alphaMin, 1 / 300);
    _this.alphaTarget = 0;
    /** 节点运动速度衰减参数 */

    _this.velocityDecay = 0.6;
    /** 边引力大小 */

    _this.edgeStrength = 0.2;
    /** 节点引力大小 */

    _this.nodeStrength = 30;
    /** 是否开启防止重叠 */

    _this.preventOverlap = false;
    /** 是否开启节点之间的防止重叠 */

    _this.preventNodeOverlap = false;
    /** 是否开启 Combo 之间的防止重叠 */

    _this.preventComboOverlap = false;
    /** 防止重叠的碰撞力大小 */

    _this.collideStrength = undefined;
    /** 防止重叠的碰撞力大小 */

    _this.nodeCollideStrength = undefined;
    /** 防止重叠的碰撞力大小 */

    _this.comboCollideStrength = undefined;
    /** 优化计算斥力的速度，两节点间距超过 optimizeRangeFactor * width 则不再计算斥力和重叠斥力 */

    _this.optimizeRangeFactor = 1;
    /** 每次迭代的回调函数 */

    _this.onTick = function () {};
    /** 每次迭代的回调函数 */


    _this.onLayoutEnd = function () {};
    /** 根据边两端节点层级差距的调整引力系数的因子，取值范围 [0, 1]。层级差距越大，引力越小 */


    _this.depthAttractiveForceScale = 0.5;
    /** 根据边两端节点层级差距的调整斥力系数的因子，取值范围 [1, Infinity]。层级差距越大，斥力越大 */

    _this.depthRepulsiveForceScale = 2;
    /** 内部计算参数 */

    _this.nodes = [];
    _this.edges = [];
    _this.combos = [];
    _this.comboTrees = [];
    _this.width = 300;
    _this.height = 300;
    _this.bias = [];
    _this.nodeMap = {};
    _this.oriComboMap = {};
    _this.nodeIdxMap = {};
    _this.comboMap = {};
    _this.previousLayouted = false;
    return _this;
  }

  ComboForce.prototype.getDefaultCfg = function () {
    return {
      maxIteration: 100,
      center: [0, 0],
      gravity: 10,
      speed: 1,
      comboGravity: 30,
      preventOverlap: false,
      preventComboOverlap: true,
      preventNodeOverlap: true,
      nodeSpacing: undefined,
      collideStrength: undefined,
      nodeCollideStrength: 0.5,
      comboCollideStrength: 0.5,
      comboSpacing: 20,
      comboPadding: 10,
      edgeStrength: 0.2,
      nodeStrength: 30,
      linkDistance: 10
    };
  };
  /**
   * 执行布局
   */


  ComboForce.prototype.execute = function () {
    var self = this;
    var nodes = self.nodes;
    var center = self.center;
    self.comboTree = {
      id: 'comboTreeRoot',
      depth: -1,
      children: self.comboTrees
    };

    if (!nodes || nodes.length === 0) {
      return;
    }

    if (nodes.length === 1) {
      nodes[0].x = center[0];
      nodes[0].y = center[1];
      return;
    }

    self.initVals(); // layout

    self.run();
    self.onLayoutEnd();
  };

  ComboForce.prototype.run = function () {
    var self = this;
    var nodes = self.nodes;
    var maxIteration = self.previousLayouted ? self.maxIteration / 5 : self.maxIteration;

    if (!self.width && typeof window !== 'undefined') {
      self.width = window.innerWidth;
    }

    if (!self.height && typeof window !== 'undefined') {
      self.height = window.innerHeight;
    }

    var center = self.center;
    var velocityDecay = self.velocityDecay; // init the positions to make the nodes with same combo gather around the combo

    var comboMap = self.comboMap;
    if (!self.previousLayouted) self.initPos(comboMap);

    var _loop_1 = function _loop_1(i) {
      var displacements = [];
      nodes.forEach(function (_, j) {
        displacements[j] = {
          x: 0,
          y: 0
        };
      });
      self.applyCalculate(displacements); // gravity for combos

      self.applyComboCenterForce(displacements); // move

      nodes.forEach(function (n, j) {
        if (!isNumber(n.x) || !isNumber(n.y)) return;
        n.x += displacements[j].x * velocityDecay;
        n.y += displacements[j].y * velocityDecay;
      });
      self.alpha += (self.alphaTarget - self.alpha) * self.alphaDecay;
      self.onTick();
    }; // iterate


    for (var i = 0; i < maxIteration; i++) {
      _loop_1(i);
    } // move to center


    var meanCenter = [0, 0];
    nodes.forEach(function (n) {
      if (!isNumber(n.x) || !isNumber(n.y)) return;
      meanCenter[0] += n.x;
      meanCenter[1] += n.y;
    });
    meanCenter[0] /= nodes.length;
    meanCenter[1] /= nodes.length;
    var centerOffset = [center[0] - meanCenter[0], center[1] - meanCenter[1]];
    nodes.forEach(function (n, j) {
      if (!isNumber(n.x) || !isNumber(n.y)) return;
      n.x += centerOffset[0];
      n.y += centerOffset[1];
    }); // arrange the empty combo

    self.combos.forEach(function (combo) {
      var mapped = comboMap[combo.id];

      if (mapped && mapped.empty) {
        combo.x = mapped.cx || combo.x;
        combo.y = mapped.cy || combo.y;
      }
    });
    self.previousLayouted = true;
  };

  ComboForce.prototype.initVals = function () {
    var self = this;
    var edges = self.edges;
    var nodes = self.nodes;
    var combos = self.combos;
    var count = {};
    var nodeMap = {};
    var nodeIdxMap = {};
    nodes.forEach(function (node, i) {
      nodeMap[node.id] = node;
      nodeIdxMap[node.id] = i;
    });
    self.nodeMap = nodeMap;
    self.nodeIdxMap = nodeIdxMap;
    var oriComboMap = {};
    combos.forEach(function (combo) {
      oriComboMap[combo.id] = combo;
    });
    self.oriComboMap = oriComboMap;
    self.comboMap = self.getComboMap();
    var preventOverlap = self.preventOverlap;
    self.preventComboOverlap = self.preventComboOverlap || preventOverlap;
    self.preventNodeOverlap = self.preventNodeOverlap || preventOverlap;
    var collideStrength = self.collideStrength;

    if (collideStrength) {
      self.comboCollideStrength = collideStrength;
      self.nodeCollideStrength = collideStrength;
    } // get edge bias


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

    if (isNumber(nodeSpacing)) {
      nodeSpacingFunc = function nodeSpacingFunc() {
        return nodeSpacing;
      };
    } else if (isFunction(nodeSpacing)) {
      nodeSpacingFunc = nodeSpacing;
    } else {
      nodeSpacingFunc = function nodeSpacingFunc() {
        return 0;
      };
    }

    this.nodeSpacing = nodeSpacingFunc; // nodeSize to function

    if (!nodeSize) {
      nodeSizeFunc = function nodeSizeFunc(d) {
        if (d.size) {
          if (isArray(d.size)) {
            var res = d.size[0] > d.size[1] ? d.size[0] : d.size[1];
            return res / 2;
          }

          return d.size / 2;
        }

        return 10;
      };
    } else if (isFunction(nodeSize)) {
      nodeSizeFunc = function nodeSizeFunc(d) {
        return nodeSize(d);
      };
    } else if (isArray(nodeSize)) {
      var larger = nodeSize[0] > nodeSize[1] ? nodeSize[0] : nodeSize[1];
      var radius_1 = larger / 2;

      nodeSizeFunc = function nodeSizeFunc(d) {
        return radius_1;
      };
    } else {
      // number type
      var radius_2 = nodeSize / 2;

      nodeSizeFunc = function nodeSizeFunc(d) {
        return radius_2;
      };
    }

    this.nodeSize = nodeSizeFunc; // comboSpacing to function

    var comboSpacing = self.comboSpacing;
    var comboSpacingFunc;

    if (isNumber(comboSpacing)) {
      comboSpacingFunc = function comboSpacingFunc() {
        return comboSpacing;
      };
    } else if (isFunction(comboSpacing)) {
      comboSpacingFunc = comboSpacing;
    } else {
      // null type
      comboSpacingFunc = function comboSpacingFunc() {
        return 0;
      };
    }

    this.comboSpacing = comboSpacingFunc; // comboPadding to function

    var comboPadding = self.comboPadding;
    var comboPaddingFunc;

    if (isNumber(comboPadding)) {
      comboPaddingFunc = function comboPaddingFunc() {
        return comboPadding;
      };
    } else if (isArray(comboPadding)) {
      comboPaddingFunc = function comboPaddingFunc() {
        return Math.max.apply(null, comboPadding);
      };
    } else if (isFunction(comboPadding)) {
      comboPaddingFunc = comboPadding;
    } else {
      // null type
      comboPaddingFunc = function comboPaddingFunc() {
        return 0;
      };
    }

    this.comboPadding = comboPaddingFunc; // linkDistance to function

    var linkDistance = this.linkDistance;
    var linkDistanceFunc;

    if (!linkDistance) {
      linkDistance = 10;
    }

    if (isNumber(linkDistance)) {
      linkDistanceFunc = function linkDistanceFunc(d) {
        return linkDistance;
      };
    } else {
      linkDistanceFunc = linkDistance;
    }

    this.linkDistance = linkDistanceFunc; // linkStrength to function

    var edgeStrength = this.edgeStrength;
    var edgeStrengthFunc;

    if (!edgeStrength) {
      edgeStrength = 1;
    }

    if (isNumber(edgeStrength)) {
      edgeStrengthFunc = function edgeStrengthFunc(d) {
        return edgeStrength;
      };
    } else {
      edgeStrengthFunc = edgeStrength;
    }

    this.edgeStrength = edgeStrengthFunc; // nodeStrength to function

    var nodeStrength = this.nodeStrength;
    var nodeStrengthFunc;

    if (!nodeStrength) {
      nodeStrength = 30;
    }

    if (isNumber(nodeStrength)) {
      nodeStrengthFunc = function nodeStrengthFunc(d) {
        return nodeStrength;
      };
    } else {
      nodeStrengthFunc = nodeStrength;
    }

    this.nodeStrength = nodeStrengthFunc;
  };

  ComboForce.prototype.initPos = function (comboMap) {
    var self = this;
    var nodes = self.nodes;
    nodes.forEach(function (node) {
      if (node.comboId) {
        var combo = comboMap[node.comboId];
        node.x = combo.cx + Math.random() * 100;
        node.y = combo.cy + Math.random() * 100;
      } else {
        node.x = Math.random() * 100;
        node.y = Math.random() * 100;
      }
    });
  };

  ComboForce.prototype.getComboMap = function () {
    var self = this;
    var nodeMap = self.nodeMap;
    var nodeIdxMap = self.nodeIdxMap;
    var comboTrees = self.comboTrees;
    var oriComboMap = self.oriComboMap;
    var comboMap = {};
    comboTrees && comboTrees.forEach(function (ctree) {
      var treeChildren = [];
      traverseTreeUp(ctree, function (treeNode) {
        if (treeNode.itemType === 'node') return true; // skip it

        if (!oriComboMap[treeNode.id]) return true; // means it is hidden, skip it

        if (comboMap[treeNode.id] === undefined) {
          var combo = {
            name: treeNode.id,
            cx: 0,
            cy: 0,
            count: 0,
            depth: self.oriComboMap[treeNode.id].depth,
            children: []
          };
          comboMap[treeNode.id] = combo;
        }

        var children = treeNode.children;

        if (children) {
          children.forEach(function (child) {
            if (!comboMap[child.id] && !nodeMap[child.id]) return true; // means it is hidden

            treeChildren.push(child);
          });
        }

        var c = comboMap[treeNode.id];
        c.cx = 0;
        c.cy = 0; // In order to layout the empty combo, add a virtual node to it

        if (treeChildren.length === 0) {
          c.empty = true;
          var oriCombo = oriComboMap[treeNode.id];
          var idx = Object.keys(nodeMap).length;
          var virtualNodeId = treeNode.id + "-visual-child-" + idx;
          var vnode = {
            id: virtualNodeId,
            x: oriCombo.x,
            y: oriCombo.y,
            depth: c.depth + 1,
            itemType: 'node'
          };
          self.nodes.push(vnode);
          nodeMap[virtualNodeId] = vnode;
          nodeIdxMap[virtualNodeId] = idx;
          c.cx = oriCombo.x;
          c.cy = oriCombo.y;
          treeChildren.push(vnode);
        }

        treeChildren.forEach(function (child) {
          c.count++;

          if (child.itemType !== 'node') {
            var childCombo = comboMap[child.id];
            if (isNumber(childCombo.cx)) c.cx += childCombo.cx;
            if (isNumber(childCombo.cy)) c.cy += childCombo.cy;
            return;
          }

          var node = nodeMap[child.id]; // means the node is hidden, skip it

          if (!node) return;

          if (isNumber(node.x)) {
            c.cx += node.x;
          }

          if (isNumber(node.y)) {
            c.cy += node.y;
          }
        });
        c.cx /= c.count;
        c.cy /= c.count;
        c.children = treeChildren;
        return true;
      });
    });
    return comboMap;
  };

  ComboForce.prototype.applyComboCenterForce = function (displacements) {
    var self = this;
    var gravity = self.gravity;
    var comboGravity = self.comboGravity || gravity;
    var alpha = this.alpha;
    var comboTrees = self.comboTrees;
    var nodeIdxMap = self.nodeIdxMap;
    var nodeMap = self.nodeMap;
    var comboMap = self.comboMap;
    comboTrees && comboTrees.forEach(function (ctree) {
      traverseTreeUp(ctree, function (treeNode) {
        if (treeNode.itemType === 'node') return true; // skip it

        var combo = comboMap[treeNode.id]; // means the combo is hidden, skip it

        if (!combo) return true;
        var c = comboMap[treeNode.id]; // higher depth the combo, larger the gravity

        var gravityScale = (c.depth + 1) * 0.5; // apply combo center force for all the descend nodes in this combo
        // and update the center position and count for this combo

        var comboX = c.cx;
        var comboY = c.cy;
        c.cx = 0;
        c.cy = 0;
        c.children.forEach(function (child) {
          if (child.itemType !== 'node') {
            var childCombo = comboMap[child.id];
            if (childCombo && isNumber(childCombo.cx)) c.cx += childCombo.cx;
            if (childCombo && isNumber(childCombo.cy)) c.cy += childCombo.cy;
            return;
          }

          var node = nodeMap[child.id];
          var vecX = node.x - comboX || 0.005;
          var vecY = node.y - comboY || 0.005;
          var l = Math.sqrt(vecX * vecX + vecY * vecY);
          var childIdx = nodeIdxMap[node.id];
          var params = comboGravity * alpha / l * gravityScale;
          displacements[childIdx].x -= vecX * params;
          displacements[childIdx].y -= vecY * params;
          if (isNumber(node.x)) c.cx += node.x;
          if (isNumber(node.y)) c.cy += node.y;
        });
        c.cx /= c.count;
        c.cy /= c.count;
        return true;
      });
    });
  };

  ComboForce.prototype.applyCalculate = function (displacements) {
    var self = this;
    var comboMap = self.comboMap;
    var nodes = self.nodes; // store the vx, vy, and distance to reduce dulplicate calculation

    var vecMap = {};
    nodes.forEach(function (v, i) {
      nodes.forEach(function (u, j) {
        if (i < j) return;
        var vx = v.x - u.x || 0.005;
        var vy = v.y - u.y || 0.005;
        var vl2 = vx * vx + vy * vy;
        var vl = Math.sqrt(vl2);
        if (vl2 < 1) vl2 = vl;
        vecMap[v.id + "-" + u.id] = {
          vx: vx,
          vy: vy,
          vl2: vl2,
          vl: vl
        };
        vecMap[u.id + "-" + v.id] = {
          vx: -vx,
          vy: -vy,
          vl2: vl2,
          vl: vl
        };
      });
    }); // get the sizes of the combos

    self.updateComboSizes(comboMap);
    self.calRepulsive(displacements, vecMap);
    self.calAttractive(displacements, vecMap);
    var preventComboOverlap = self.preventComboOverlap;
    if (preventComboOverlap) self.comboNonOverlapping(displacements, comboMap);
  };
  /**
   * Update the sizes of the combos according to their children
   * Used for combos nonoverlap, but not re-render the combo shapes
   */


  ComboForce.prototype.updateComboSizes = function (comboMap) {
    var self = this;
    var comboTrees = self.comboTrees;
    var nodeMap = self.nodeMap;
    var nodeSize = self.nodeSize;
    var comboSpacing = self.comboSpacing;
    var comboPadding = self.comboPadding;
    comboTrees && comboTrees.forEach(function (ctree) {
      var treeChildren = [];
      traverseTreeUp(ctree, function (treeNode) {
        if (treeNode.itemType === 'node') return true; // skip it

        var c = comboMap[treeNode.id]; // means the combo is hidden, skip it

        if (!c) return;
        var children = treeNode.children;

        if (children) {
          children.forEach(function (child) {
            // means the combo is hidden.
            if (!comboMap[child.id] && !nodeMap[child.id]) return;
            treeChildren.push(child);
          });
        }

        c.minX = Infinity;
        c.minY = Infinity;
        c.maxX = -Infinity;
        c.maxY = -Infinity;
        treeChildren.forEach(function (child) {
          if (child.itemType !== 'node') return true; // skip it

          var node = nodeMap[child.id];
          if (!node) return true; // means it is hidden

          var r = nodeSize(node);
          var nodeMinX = node.x - r;
          var nodeMinY = node.y - r;
          var nodeMaxX = node.x + r;
          var nodeMaxY = node.y + r;
          if (c.minX > nodeMinX) c.minX = nodeMinX;
          if (c.minY > nodeMinY) c.minY = nodeMinY;
          if (c.maxX < nodeMaxX) c.maxX = nodeMaxX;
          if (c.maxY < nodeMaxY) c.maxY = nodeMaxY;
        });
        var minSize = self.oriComboMap[treeNode.id].size || Global.defaultCombo.size;
        if (isArray(minSize)) minSize = minSize[0];
        var maxLength = Math.max(c.maxX - c.minX, c.maxY - c.minY, minSize);
        c.r = maxLength / 2 + comboSpacing(c) / 2 + comboPadding(c);
        return true;
      });
    });
  };
  /**
   * prevent the overlappings among combos
   */


  ComboForce.prototype.comboNonOverlapping = function (displacements, comboMap) {
    var self = this;
    var comboTree = self.comboTree;
    var comboCollideStrength = self.comboCollideStrength;
    var nodeIdxMap = self.nodeIdxMap;
    var nodeMap = self.nodeMap;
    traverseTreeUp(comboTree, function (treeNode) {
      if (!comboMap[treeNode.id] && !nodeMap[treeNode.id] && treeNode.id !== 'comboTreeRoot') return; // means it is hidden

      var children = treeNode.children; // 同个子树下的子 combo 间两两对比

      if (children && children.length > 1) {
        children.forEach(function (v, i) {
          if (v.itemType === 'node') return; // skip it

          var cv = comboMap[v.id];
          if (!cv) return; // means it is hidden, skip it

          children.forEach(function (u, j) {
            if (i <= j) return;
            if (u.itemType === 'node') return; // skip it

            var cu = comboMap[u.id];
            if (!cu) return; // means it is hidden, skip it

            var vx = cv.cx - cu.cx || 0.005;
            var vy = cv.cy - cu.cy || 0.005;
            var l = vx * vx + vy * vy;
            var rv = cv.r;
            var ru = cu.r;
            var r = rv + ru;
            var ru2 = ru * ru;
            var rv2 = rv * rv; // overlapping

            if (l < r * r) {
              var vnodes = v.children;
              if (!vnodes || vnodes.length === 0) return; // skip it

              var unodes_1 = u.children;
              if (!unodes_1 || unodes_1.length === 0) return; // skip it

              var sqrtl = Math.sqrt(l);
              var ll = (r - sqrtl) / sqrtl * comboCollideStrength;
              var xl_1 = vx * ll;
              var yl_1 = vy * ll;
              var rratio_1 = ru2 / (rv2 + ru2);
              var irratio_1 = 1 - rratio_1; // 两兄弟 combo 的子节点上施加斥力

              vnodes.forEach(function (vn) {
                if (vn.itemType !== 'node') return; // skip it

                if (!nodeMap[vn.id]) return; // means it is hidden, skip it

                var vindex = nodeIdxMap[vn.id];
                unodes_1.forEach(function (un) {
                  if (un.itemType !== 'node') return;
                  if (!nodeMap[un.id]) return; // means it is hidden, skip it

                  var uindex = nodeIdxMap[un.id];
                  displacements[vindex].x += xl_1 * rratio_1;
                  displacements[vindex].y += yl_1 * rratio_1;
                  displacements[uindex].x -= xl_1 * irratio_1;
                  displacements[uindex].y -= yl_1 * irratio_1;
                });
              });
            }
          });
        });
      }

      return true;
    });
  };
  /**
   * Calculate the repulsive force between each node pair
   * @param displacements The array stores the displacements for nodes
   * @param vecMap The map stores vector between each node pair
   */


  ComboForce.prototype.calRepulsive = function (displacements, vecMap) {
    var self = this;
    var nodes = self.nodes;
    var max = self.width * self.optimizeRangeFactor;
    var nodeStrength = self.nodeStrength;
    ;
    var alpha = self.alpha;
    var nodeCollideStrength = self.nodeCollideStrength;
    var preventNodeOverlap = self.preventNodeOverlap;
    var nodeSizeFunc = self.nodeSize;
    var nodeSpacingFunc = self.nodeSpacing;
    var scale = self.depthRepulsiveForceScale;
    var center = self.center;
    nodes.forEach(function (v, i) {
      if (!v.x || !v.y) return; // center gravity

      if (center) {
        var gravity = self.gravity;
        var vecX = v.x - center[0] || 0.005;
        var vecY = v.y - center[1] || 0.005;
        var l = Math.sqrt(vecX * vecX + vecY * vecY);
        displacements[i].x -= vecX * gravity * alpha / l;
        displacements[i].y -= vecY * gravity * alpha / l;
      }

      nodes.forEach(function (u, j) {
        if (i === j) {
          return;
        }

        if (!u.x || !u.y) return;
        var _a = vecMap[v.id + "-" + u.id],
            vl2 = _a.vl2,
            vl = _a.vl;
        if (vl > max) return;
        var _b = vecMap[v.id + "-" + u.id],
            vx = _b.vx,
            vy = _b.vy;
        var depthDiff = Math.abs(u.depth - v.depth) + 1 || 1;
        if (u.comboId !== v.comboId) depthDiff++;
        var depthParam = depthDiff ? Math.pow(scale, depthDiff) : 1;
        var params = nodeStrength(u) * alpha / vl2 * depthParam;
        displacements[i].x += vx * params;
        displacements[i].y += vy * params; // prevent node overlappings

        if (i < j && preventNodeOverlap) {
          var ri = nodeSizeFunc(v) + nodeSpacingFunc(v);
          var rj = nodeSizeFunc(u) + nodeSpacingFunc(u);
          var r = ri + rj;

          if (vl2 < r * r) {
            var ll = (r - vl) / vl * nodeCollideStrength;
            var rj2 = rj * rj;
            var rratio = rj2 / (ri * ri + rj2);
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
  /**
   * Calculate the attractive force between the node pair with edge
   * @param displacements The array stores the displacements for nodes
   * @param vecMap The map stores vector between each node pair
   */


  ComboForce.prototype.calAttractive = function (displacements, vecMap) {
    var self = this;
    var edges = self.edges;
    var linkDistance = self.linkDistance;
    var alpha = self.alpha;
    var edgeStrength = self.edgeStrength;
    var bias = self.bias;
    var scale = self.depthAttractiveForceScale;
    edges.forEach(function (e, i) {
      if (!e.source || !e.target || e.source === e.target) return;
      var uIndex = self.nodeIdxMap[e.source];
      var vIndex = self.nodeIdxMap[e.target];
      var u = self.nodeMap[e.source];
      var v = self.nodeMap[e.target];
      var depthDiff = Math.abs(u.depth - v.depth);
      if (u.comboId === v.comboId) depthDiff / 2;
      var depthParam = depthDiff ? Math.pow(scale, depthDiff) : 1;

      if (u.comboId !== v.comboId && depthParam === 1) {
        depthParam = scale / 2;
      } else if (u.comboId === v.comboId) {
        depthParam = 2;
      }

      if (!isNumber(v.x) || !isNumber(u.x) || !isNumber(v.y) || !isNumber(u.y)) return;
      var _a = vecMap[e.target + "-" + e.source],
          vl = _a.vl,
          vx = _a.vx,
          vy = _a.vy;
      var l = (vl - linkDistance(e)) / vl * alpha * edgeStrength(e) * depthParam;
      var vecX = vx * l;
      var vecY = vy * l;
      var b = bias[i];
      displacements[vIndex].x -= vecX * b;
      displacements[vIndex].y -= vecY * b;
      displacements[uIndex].x += vecX * (1 - b);
      displacements[uIndex].y += vecY * (1 - b);
    });
  };

  return ComboForce;
}(BaseLayout);

export default ComboForce;