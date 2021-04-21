"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var degree = function degree(graph) {
  var degrees = {};
  graph.getNodes().forEach(function (node) {
    degrees[node.getID()] = {
      degree: 0,
      inDegree: 0,
      outDegree: 0
    };
  });
  graph.getEdges().forEach(function (edge) {
    degrees[edge.getSource().getID()].degree++;
    degrees[edge.getSource().getID()].outDegree++;
    degrees[edge.getTarget().getID()].degree++;
    degrees[edge.getTarget().getID()].inDegree++;
  });
  return degrees;
};

var _default = degree;
exports.default = _default;