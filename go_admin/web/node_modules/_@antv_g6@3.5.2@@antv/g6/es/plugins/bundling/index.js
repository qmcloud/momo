import { __extends } from "tslib";
import Base from '../base';

function getEucliDis(pointA, pointB, eps) {
  var vx = pointA.x - pointB.x;
  var vy = pointA.y - pointB.y;

  if (!eps || Math.abs(vx) > eps || Math.abs(vy) > eps) {
    return Math.sqrt(vx * vx + vy * vy);
  }

  return eps;
}

function getDotProduct(ei, ej) {
  return ei.x * ej.x + ei.y * ej.y;
}

function projectPointToEdge(p, e) {
  var k = (e.source.y - e.target.y) / (e.source.x - e.target.x);
  var x = (k * k * e.source.x + k * (p.y - e.source.y) + p.x) / (k * k + 1);
  var y = k * (x - e.source.x) + e.source.y;
  return {
    x: x,
    y: y
  };
}

var Bundling =
/** @class */
function (_super) {
  __extends(Bundling, _super);

  function Bundling(cfg) {
    return _super.call(this, cfg) || this;
  }

  Bundling.prototype.getDefaultCfgs = function () {
    return {
      edgeBundles: [],
      edgePoints: [],
      K: 0.1,
      lambda: 0.1,
      divisions: 1,
      divRate: 2,
      cycles: 6,
      iterations: 90,
      iterRate: 0.6666667,
      bundleThreshold: 0.6,
      eps: 1e-6,
      onLayoutEnd: function onLayoutEnd() {},
      onTick: function onTick() {}
    };
  };

  Bundling.prototype.init = function () {
    var graph = this.get('graph');
    var onTick = this.get('onTick');

    var tick = function tick() {
      if (onTick) {
        onTick();
      }

      graph.refreshPositions();
    };

    this.set('tick', tick);
  };

  Bundling.prototype.bundling = function (data) {
    var self = this;
    self.set('data', data); // 如果正在布局，忽略布局请求

    if (self.isTicking()) {
      return;
    }

    var edges = data.edges || [];
    var nodes = data.nodes || [];
    var nodeIdMap = {};
    var error = false;
    nodes.forEach(function (node) {
      if (node.x === null || !node.y === null || node.x === undefined || !node.y === undefined) {
        error = true;
      }

      nodeIdMap[node.id] = node;
    });
    if (error) throw new Error('please layout the graph or assign x and y for nodes first');
    self.set('nodeIdMap', nodeIdMap); // subdivide each edges

    var divisions = self.get('divisions');
    var divRate = self.get('divRate');
    var edgePoints = self.divideEdges(divisions);
    self.set('edgePoints', edgePoints); // compute the bundles

    var edgeBundles = self.getEdgeBundles();
    self.set('edgeBundles', edgeBundles); // iterations

    var C = self.get('cycles');
    var iterations = self.get('iterations');
    var iterRate = self.get('iterRate');
    var lambda = self.get('lambda');

    for (var i = 0; i < C; i++) {
      var _loop_1 = function _loop_1(j) {
        var forces = [];
        edges.forEach(function (e, k) {
          if (e.source === e.target) return;
          var source = nodeIdMap[e.source];
          var target = nodeIdMap[e.target];
          forces[k] = self.getEdgeForces({
            source: source,
            target: target
          }, k, divisions, lambda);

          for (var p = 0; p < divisions + 1; p++) {
            edgePoints[k][p].x += forces[k][p].x;
            edgePoints[k][p].y += forces[k][p].y;
          }
        });
      };

      for (var j = 0; j < iterations; j++) {
        _loop_1(j);
      } // parameters for nex cycle


      lambda = lambda / 2;
      divisions *= divRate;
      iterations *= iterRate;
      edgePoints = self.divideEdges(divisions);
      self.set('edgePoints', edgePoints);
    } // change the edges according to edgePoints


    edges.forEach(function (e, i) {
      if (e.source === e.target) return;
      e.shape = 'polyline';
      e.type = 'polyline';
      e.controlPoints = edgePoints[i].slice(1, edgePoints[i].length - 1);
    });
    var graph = self.get('graph');
    graph.refresh();
  };

  Bundling.prototype.updateBundling = function (cfg) {
    var self = this;
    var data = cfg.data;

    if (data) {
      self.set('data', data);
    }

    if (self.get('ticking')) {
      self.set('ticking', false);
    }

    Object.keys(cfg).forEach(function (key) {
      self.set(key, cfg[key]);
    });

    if (cfg.onTick) {
      var graph_1 = this.get('graph');
      self.set('tick', function () {
        cfg.onTick();
        graph_1.refresh();
      });
    }

    self.bundling(data);
  };

  Bundling.prototype.divideEdges = function (divisions) {
    var self = this;
    var edges = self.get('data').edges;
    var nodeIdMap = self.get('nodeIdMap');
    var edgePoints = self.get('edgePoints');
    if (!edgePoints || edgePoints === undefined) edgePoints = [];
    edges.forEach(function (edge, i) {
      if (!edgePoints[i] || edgePoints[i] === undefined) {
        edgePoints[i] = [];
      }

      var source = nodeIdMap[edge.source];
      var target = nodeIdMap[edge.target];

      if (divisions === 1) {
        edgePoints[i].push({
          x: source.x,
          y: source.y
        }); // source

        edgePoints[i].push({
          x: 0.5 * (source.x + target.x),
          y: 0.5 * (source.y + target.y)
        }); // mid

        edgePoints[i].push({
          x: target.x,
          y: target.y
        }); // target
      } else {
        var edgeLength = 0;

        if (!edgePoints[i] || edgePoints[i] === []) {
          // it is a straight line
          edgeLength = getEucliDis({
            x: source.x,
            y: source.y
          }, {
            x: target.x,
            y: target.y
          });
        } else {
          edgeLength = self.getEdgeLength(edgePoints[i]);
        }

        var divisionLength_1 = edgeLength / (divisions + 1);
        var currentDivisonLength_1 = divisionLength_1;
        var newEdgePoints_1 = [{
          x: source.x,
          y: source.y
        }]; // source

        edgePoints[i].forEach(function (ep, j) {
          if (j === 0) return;
          var oriDivisionLength = getEucliDis(ep, edgePoints[i][j - 1]);

          while (oriDivisionLength > currentDivisonLength_1) {
            var ratio = currentDivisonLength_1 / oriDivisionLength;
            var edgePoint = {
              x: edgePoints[i][j - 1].x,
              y: edgePoints[i][j - 1].y
            };
            edgePoint.x += ratio * (ep.x - edgePoints[i][j - 1].x);
            edgePoint.y += ratio * (ep.y - edgePoints[i][j - 1].y);
            newEdgePoints_1.push(edgePoint);
            oriDivisionLength -= currentDivisonLength_1;
            currentDivisonLength_1 = divisionLength_1;
          }

          currentDivisonLength_1 -= oriDivisionLength;
        });
        newEdgePoints_1.push({
          x: target.x,
          y: target.y
        }); // target

        edgePoints[i] = newEdgePoints_1;
      }
    });
    return edgePoints;
  };
  /**
   * 计算边的长度
   * @param points
   */


  Bundling.prototype.getEdgeLength = function (points) {
    var length = 0;
    points.forEach(function (p, i) {
      if (i === 0) return;
      length += getEucliDis(p, points[i - 1]);
    });
    return length;
  };

  Bundling.prototype.getEdgeBundles = function () {
    var self = this;
    var data = self.get('data');
    var edges = data.edges || [];
    var bundleThreshold = self.get('bundleThreshold');
    var nodeIdMap = self.get('nodeIdMap');
    var edgeBundles = self.get('edgeBundles');
    if (!edgeBundles) edgeBundles = [];
    edges.forEach(function (e, i) {
      if (!edgeBundles[i] || edgeBundles[i] === undefined) {
        edgeBundles[i] = [];
      }
    });
    edges.forEach(function (ei, i) {
      var iSource = nodeIdMap[ei.source];
      var iTarget = nodeIdMap[ei.target];
      edges.forEach(function (ej, j) {
        if (j <= i) return;
        var jSource = nodeIdMap[ej.source];
        var jTarget = nodeIdMap[ej.target];
        var score = self.getBundleScore({
          source: iSource,
          target: iTarget
        }, {
          source: jSource,
          target: jTarget
        });

        if (score >= bundleThreshold) {
          edgeBundles[i].push(j);
          edgeBundles[j].push(i);
        }
      });
    });
    return edgeBundles;
  };

  Bundling.prototype.getBundleScore = function (ei, ej) {
    var self = this;
    ei.vx = ei.target.x - ei.source.x;
    ei.vy = ei.target.y - ei.source.y;
    ej.vx = ej.target.x - ej.source.x;
    ej.vy = ej.target.y - ej.source.y;
    ei.length = getEucliDis({
      x: ei.source.x,
      y: ei.source.y
    }, {
      x: ei.target.x,
      y: ei.target.y
    });
    ej.length = getEucliDis({
      x: ej.source.x,
      y: ej.source.y
    }, {
      x: ej.target.x,
      y: ej.target.y
    }); // angle score

    var aScore = self.getAngleScore(ei, ej); // scale score

    var sScore = self.getScaleScore(ei, ej); // position score

    var pScore = self.getPositionScore(ei, ej); // visibility socre

    var vScore = self.getVisibilityScore(ei, ej);
    return aScore * sScore * pScore * vScore;
  };

  Bundling.prototype.getAngleScore = function (ei, ej) {
    var dotProduct = getDotProduct({
      x: ei.vx,
      y: ei.vy
    }, {
      x: ej.vx,
      y: ej.vy
    });
    return dotProduct / (ei.length * ej.length);
  };

  Bundling.prototype.getScaleScore = function (ei, ej) {
    var aLength = (ei.length + ej.length) / 2;
    var score = 2 / (aLength / Math.min(ei.length, ej.length) + Math.max(ei.length, ej.length) / aLength);
    return score;
  };

  Bundling.prototype.getPositionScore = function (ei, ej) {
    var aLength = (ei.length + ej.length) / 2;
    var iMid = {
      x: (ei.source.x + ei.target.x) / 2,
      y: (ei.source.y + ei.target.y) / 2
    };
    var jMid = {
      x: (ej.source.x + ej.target.x) / 2,
      y: (ej.source.y + ej.target.y) / 2
    };
    var distance = getEucliDis(iMid, jMid);
    return aLength / (aLength + distance);
  };

  Bundling.prototype.getVisibilityScore = function (ei, ej) {
    var vij = this.getEdgeVisibility(ei, ej);
    var vji = this.getEdgeVisibility(ej, ei);
    return vij < vji ? vij : vji;
  };

  Bundling.prototype.getEdgeVisibility = function (ei, ej) {
    var ps = projectPointToEdge(ej.source, ei);
    var pt = projectPointToEdge(ej.target, ei);
    var pMid = {
      x: (ps.x + pt.x) / 2,
      y: (ps.y + pt.y) / 2
    };
    var iMid = {
      x: (ei.source.x + ei.target.x) / 2,
      y: (ei.source.y + ei.target.y) / 2
    };
    return Math.max(0, 1 - 2 * getEucliDis(pMid, iMid) / getEucliDis(ps, pt));
  };

  Bundling.prototype.getEdgeForces = function (e, eidx, divisions, lambda) {
    var self = this;
    var edgePoints = self.get('edgePoints');
    var K = self.get('K');
    var kp = K / (getEucliDis(e.source, e.target) * (divisions + 1));
    var edgePointForces = [{
      x: 0,
      y: 0
    }];

    for (var i = 1; i < divisions; i++) {
      var force = {
        x: 0,
        y: 0
      };
      var spring = self.getSpringForce({
        pre: edgePoints[eidx][i - 1],
        cur: edgePoints[eidx][i],
        next: edgePoints[eidx][i + 1]
      }, kp);
      var electrostatic = self.getElectrostaticForce(i, eidx);
      force.x = lambda * (spring.x + electrostatic.x);
      force.y = lambda * (spring.y + electrostatic.y);
      edgePointForces.push(force);
    }

    edgePointForces.push({
      x: 0,
      y: 0
    });
    return edgePointForces;
  };

  Bundling.prototype.getSpringForce = function (divisions, kp) {
    var x = divisions.pre.x + divisions.next.x - 2 * divisions.cur.x;
    var y = divisions.pre.y + divisions.next.y - 2 * divisions.cur.y;
    x *= kp;
    y *= kp;
    return {
      x: x,
      y: y
    };
  };

  Bundling.prototype.getElectrostaticForce = function (pidx, eidx) {
    var self = this;
    var eps = self.get('eps');
    var edgeBundles = self.get('edgeBundles');
    var edgePoints = self.get('edgePoints');
    var edgeBundle = edgeBundles[eidx];
    var resForce = {
      x: 0,
      y: 0
    };
    edgeBundle.forEach(function (eb) {
      var force = {
        x: edgePoints[eb][pidx].x - edgePoints[eidx][pidx].x,
        y: edgePoints[eb][pidx].y - edgePoints[eidx][pidx].y
      };

      if (Math.abs(force.x) > eps || Math.abs(force.y) > eps) {
        var length_1 = getEucliDis(edgePoints[eb][pidx], edgePoints[eidx][pidx]);
        var diff = 1 / length_1;
        resForce.x += force.x * diff;
        resForce.y += force.y * diff;
      }
    });
    return resForce;
  };

  Bundling.prototype.isTicking = function () {
    return this.get('ticking');
  };

  Bundling.prototype.getSimulation = function () {
    return this.get('forceSimulation');
  };

  Bundling.prototype.destroy = function () {
    if (this.get('ticking')) {
      this.getSimulation().stop();
    }

    _super.prototype.destroy.call(this);
  };

  return Bundling;
}(Base);

export default Bundling;