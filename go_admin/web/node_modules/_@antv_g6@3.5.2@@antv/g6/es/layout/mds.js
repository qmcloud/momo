/**
 * @fileOverview MDS layout
 * @author shiwu.wyy@antfin.com
 */
import { __extends } from "tslib";
import { Matrix as MLMatrix, SingularValueDecomposition } from 'ml-matrix';
import { floydWarshall, getAdjMatrix, scaleMatrix } from '../util/math';
import { BaseLayout } from './layout';
/**
 * mds 布局
 */

var MDSLayout =
/** @class */
function (_super) {
  __extends(MDSLayout, _super);

  function MDSLayout() {
    var _this = _super !== null && _super.apply(this, arguments) || this;
    /** 布局中心 */


    _this.center = [0, 0];
    /** 边长度 */

    _this.linkDistance = 50;
    _this.scaledDistances = null;
    return _this;
  }

  MDSLayout.prototype.getDefaultCfg = function () {
    return {
      center: [0, 0],
      linkDistance: 50
    };
  };
  /**
   * 执行布局
   */


  MDSLayout.prototype.execute = function () {
    var self = this;
    var nodes = self.nodes,
        _a = self.edges,
        edges = _a === void 0 ? [] : _a;
    var center = self.center;

    if (!nodes || nodes.length === 0) {
      return;
    }

    if (nodes.length === 1) {
      nodes[0].x = center[0];
      nodes[0].y = center[1];
      return;
    }

    var linkDistance = self.linkDistance; // the graph-theoretic distance (shortest path distance) matrix

    var adjMatrix = getAdjMatrix({
      nodes: nodes,
      edges: edges
    }, false);
    var distances = floydWarshall(adjMatrix);
    self.handleInfinity(distances); // scale the ideal edge length acoording to linkDistance

    var scaledD = scaleMatrix(distances, linkDistance);
    self.scaledDistances = scaledD; // get positions by MDS

    var positions = self.runMDS();
    self.positions = positions;
    positions.forEach(function (p, i) {
      nodes[i].x = p[0] + center[0];
      nodes[i].y = p[1] + center[1];
    });
  };
  /**
   * mds 算法
   * @return {array} positions 计算后的节点位置数组
   */


  MDSLayout.prototype.runMDS = function () {
    var self = this;
    var dimension = 2;
    var distances = self.scaledDistances; // square distances

    var M = MLMatrix.mul(MLMatrix.pow(distances, 2), -0.5); // double centre the rows/columns

    var rowMeans = M.mean('row');
    var colMeans = M.mean('column');
    var totalMean = M.mean();
    M.add(totalMean).subRowVector(rowMeans).subColumnVector(colMeans); // take the SVD of the double centred matrix, and return the
    // points from it

    var ret = new SingularValueDecomposition(M);
    var eigenValues = MLMatrix.sqrt(ret.diagonalMatrix).diagonal();
    return ret.leftSingularVectors.toJSON().map(function (row) {
      return MLMatrix.mul([row], [eigenValues]).toJSON()[0].splice(0, dimension);
    });
  };

  MDSLayout.prototype.handleInfinity = function (distances) {
    var maxDistance = -999999;
    distances.forEach(function (row) {
      row.forEach(function (value) {
        if (value === Infinity) {
          return;
        }

        if (maxDistance < value) {
          maxDistance = value;
        }
      });
    });
    distances.forEach(function (row, i) {
      row.forEach(function (value, j) {
        if (value === Infinity) {
          distances[i][j] = maxDistance;
        }
      });
    });
  };

  return MDSLayout;
}(BaseLayout);

export default MDSLayout;