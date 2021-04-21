import { Matrix as MLMatrix, SingularValueDecomposition } from 'ml-matrix';

var MDS =
/** @class */
function () {
  function MDS(params) {
    this.distances = params.distances;
    this.dimension = params.dimension || 2;
    this.linkDistance = params.linkDistance;
  }

  MDS.prototype.layout = function () {
    var self = this;
    var dimension = self.dimension,
        distances = self.distances,
        linkDistance = self.linkDistance;

    try {
      // square distances
      var M = MLMatrix.mul(MLMatrix.pow(distances, 2), -0.5); // double centre the rows/columns

      var rowMeans = M.mean('row');
      var colMeans = M.mean('column');
      var totalMean = M.mean();
      M.add(totalMean).subRowVector(rowMeans).subColumnVector(colMeans); // take the SVD of the double centred matrix, and return the
      // points from it

      var ret = new SingularValueDecomposition(M);
      var eigenValues_1 = MLMatrix.sqrt(ret.diagonalMatrix).diagonal();
      return ret.leftSingularVectors.toJSON().map(function (row) {
        return MLMatrix.mul([row], [eigenValues_1]).toJSON()[0].splice(0, dimension);
      });
    } catch (_a) {
      var res = [];

      for (var i = 0; i < distances.length; i++) {
        var x = Math.random() * linkDistance;
        var y = Math.random() * linkDistance;
        res.push([x, y]);
      }

      return res;
    }
  };

  return MDS;
}();

export default MDS;