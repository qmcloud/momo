/**
 * @fileOverview random layout
 * @author shiwu.wyy@antfin.com
 */
import { __extends } from "tslib";
import { BaseLayout } from './layout';
import { getDegree } from '../util/math';
import { clone } from '@antv/util';

function initHierarchy(nodes, edges, nodeMap, directed) {
  nodes.forEach(function (_, i) {
    nodes[i].children = [];
    nodes[i].parent = [];
  });

  if (directed) {
    edges.forEach(function (e) {
      var sourceIdx = 0;

      if (e.source) {
        sourceIdx = nodeMap[e.source];
      }

      var targetIdx = 0;

      if (e.target) {
        targetIdx = nodeMap[e.target];
      }

      nodes[sourceIdx].children.push(nodes[targetIdx].id);
      nodes[targetIdx].parent.push(nodes[sourceIdx].id);
    });
  } else {
    edges.forEach(function (e) {
      var sourceIdx = 0;

      if (e.source) {
        sourceIdx = nodeMap[e.source];
      }

      var targetIdx = 0;

      if (e.target) {
        targetIdx = nodeMap[e.target];
      }

      nodes[sourceIdx].children.push(nodes[targetIdx].id);
      nodes[targetIdx].children.push(nodes[sourceIdx].id);
    });
  }
}

function connect(a, b, edges) {
  var m = edges.length;

  for (var i = 0; i < m; i++) {
    if (a.id === edges[i].source && b.id === edges[i].target || b.id === edges[i].source && a.id === edges[i].target) {
      return true;
    }
  }

  return false;
}

function compareDegree(a, b) {
  if (a.degree < b.degree) {
    return -1;
  }

  if (a.degree > b.degree) {
    return 1;
  }

  return 0;
}
/**
 * 圆形布局
 */


var CircularLayout =
/** @class */
function (_super) {
  __extends(CircularLayout, _super);

  function CircularLayout() {
    var _this = _super !== null && _super.apply(this, arguments) || this;
    /** 布局中心 */


    _this.center = [0, 0];
    /** 固定半径，若设置了 radius，则 startRadius 与 endRadius 不起效 */

    _this.radius = null;
    /** 起始半径 */

    _this.startRadius = null;
    /** 终止半径 */

    _this.endRadius = null;
    /** 起始角度 */

    _this.startAngle = 0;
    /** 终止角度 */

    _this.endAngle = 2 * Math.PI;
    /** 是否顺时针 */

    _this.clockwise = true;
    /** 节点在环上分成段数（几个段将均匀分布），在 endRadius - startRadius != 0 时生效 */

    _this.divisions = 1;
    /** 节点在环上排序的依据，可选: 'topology', 'degree', 'null' */

    _this.ordering = null;
    /** how many 2*pi from first to last nodes */

    _this.angleRatio = 1;
    _this.nodes = [];
    _this.edges = [];
    _this.nodeMap = {};
    _this.degrees = [];
    _this.width = 300;
    _this.height = 300;
    return _this;
  }

  CircularLayout.prototype.getDefaultCfg = function () {
    return {
      center: [0, 0],
      radius: null,
      startRadius: null,
      endRadius: null,
      startAngle: 0,
      endAngle: 2 * Math.PI,
      clockwise: true,
      divisions: 1,
      ordering: null,
      angleRatio: 1
    };
  };
  /**
   * 执行布局
   */


  CircularLayout.prototype.execute = function () {
    var self = this;
    var nodes = self.nodes;
    var edges = self.edges;
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

    var radius = self.radius;
    var startRadius = self.startRadius;
    var endRadius = self.endRadius;
    var divisions = self.divisions;
    var startAngle = self.startAngle;
    var endAngle = self.endAngle;
    var angleStep = (endAngle - startAngle) / n; // layout

    var nodeMap = {};
    nodes.forEach(function (node, i) {
      nodeMap[node.id] = i;
    });
    self.nodeMap = nodeMap;
    var degrees = getDegree(nodes.length, nodeMap, edges);
    self.degrees = degrees;

    if (!self.width && typeof window !== 'undefined') {
      self.width = window.innerWidth;
    }

    if (!self.height && typeof window !== 'undefined') {
      self.height = window.innerHeight;
    }

    if (!radius && !startRadius && !endRadius) {
      radius = self.height > self.width ? self.width / 2 : self.height / 2;
    } else if (!startRadius && endRadius) {
      startRadius = endRadius;
    } else if (startRadius && !endRadius) {
      endRadius = startRadius;
    }

    var angleRatio = self.angleRatio;
    var astep = angleStep * angleRatio;
    self.astep = astep;
    var ordering = self.ordering;
    var layoutNodes = [];

    if (ordering === 'topology') {
      // layout according to the topology
      layoutNodes = self.topologyOrdering();
    } else if (ordering === 'topology-directed') {
      // layout according to the topology
      layoutNodes = self.topologyOrdering(true);
    } else if (ordering === 'degree') {
      // layout according to the descent order of degrees
      layoutNodes = self.degreeOrdering();
    } else {
      // layout according to the original order in the data.nodes
      layoutNodes = nodes;
    }

    var clockwise = self.clockwise;
    var divN = Math.ceil(n / divisions); // node number in each division

    for (var i = 0; i < n; ++i) {
      var r = radius;

      if (!r && startRadius !== null && endRadius !== null) {
        r = startRadius + i * (endRadius - startRadius) / (n - 1);
      }

      if (!r) {
        r = 10 + i * 100 / (n - 1);
      }

      var angle = startAngle + i % divN * astep + 2 * Math.PI / divisions * Math.floor(i / divN);

      if (!clockwise) {
        angle = endAngle - i % divN * astep - 2 * Math.PI / divisions * Math.floor(i / divN);
      }

      layoutNodes[i].x = center[0] + Math.cos(angle) * r;
      layoutNodes[i].y = center[1] + Math.sin(angle) * r;
      layoutNodes[i].weight = degrees[i];
    }
  };
  /**
   * 根据节点的拓扑结构排序
   * @return {array} orderedNodes 排序后的结果
   */


  CircularLayout.prototype.topologyOrdering = function (directed) {
    if (directed === void 0) {
      directed = false;
    }

    var self = this;
    var degrees = self.degrees;
    var edges = self.edges;
    var nodes = self.nodes;
    var cnodes = clone(nodes);
    var nodeMap = self.nodeMap;
    var orderedCNodes = [cnodes[0]];
    var resNodes = [nodes[0]];
    var pickFlags = [];
    var n = nodes.length;
    pickFlags[0] = true;
    initHierarchy(cnodes, edges, nodeMap, directed);
    var k = 0;
    cnodes.forEach(function (cnode, i) {
      if (i !== 0) {
        if ((i === n - 1 || degrees[i] !== degrees[i + 1] || connect(orderedCNodes[k], cnode, edges)) && pickFlags[i] !== true) {
          orderedCNodes.push(cnode);
          resNodes.push(nodes[nodeMap[cnode.id]]);
          pickFlags[i] = true;
          k++;
        } else {
          var children = orderedCNodes[k].children;
          var foundChild = false;

          for (var j = 0; j < children.length; j++) {
            var childIdx = nodeMap[children[j]];

            if (degrees[childIdx] === degrees[i] && pickFlags[childIdx] !== true) {
              orderedCNodes.push(cnodes[childIdx]);
              resNodes.push(nodes[nodeMap[cnodes[childIdx].id]]);
              pickFlags[childIdx] = true;
              foundChild = true;
              break;
            }
          }

          var ii = 0;

          while (!foundChild) {
            if (!pickFlags[ii]) {
              orderedCNodes.push(cnodes[ii]);
              resNodes.push(nodes[nodeMap[cnodes[ii].id]]);
              pickFlags[ii] = true;
              foundChild = true;
            }

            ii++;

            if (ii === n) {
              break;
            }
          }
        }
      }
    });
    return resNodes;
  };
  /**
   * 根据节点度数大小排序
   * @return {array} orderedNodes 排序后的结果
   */


  CircularLayout.prototype.degreeOrdering = function () {
    var self = this;
    var nodes = self.nodes;
    var orderedNodes = [];
    var degrees = self.degrees;
    nodes.forEach(function (node, i) {
      node.degree = degrees[i];
      orderedNodes.push(node);
    });
    orderedNodes.sort(compareDegree);
    return orderedNodes;
  };

  return CircularLayout;
}(BaseLayout);

export default CircularLayout;