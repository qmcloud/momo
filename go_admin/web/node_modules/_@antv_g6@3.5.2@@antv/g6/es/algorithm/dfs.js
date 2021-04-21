function initCallbacks(callbacks) {
  if (callbacks === void 0) {
    callbacks = {};
  }

  var initiatedCallback = callbacks;

  var stubCallback = function stubCallback() {};

  var allowTraversalCallback = function () {
    var seen = {};
    return function (_a) {
      var next = _a.next;

      if (!seen[next.get('id')]) {
        seen[next.get('id')] = true;
        return true;
      }

      return false;
    };
  }();

  initiatedCallback.allowTraversal = callbacks.allowTraversal || allowTraversalCallback;
  initiatedCallback.enter = callbacks.enter || stubCallback;
  initiatedCallback.leave = callbacks.leave || stubCallback;
  return initiatedCallback;
}
/**
 * @param {Graph} graph
 * @param {GraphNode} currentNode
 * @param {GraphNode} previousNode
 * @param {Callbacks} callbacks
 */


function depthFirstSearchRecursive(graph, currentNode, previousNode, callbacks) {
  callbacks.enter({
    current: currentNode,
    previous: previousNode
  });
  graph.getSourceNeighbors(currentNode).forEach(function (nextNode) {
    if (callbacks.allowTraversal({
      previous: previousNode,
      current: currentNode,
      next: nextNode
    })) {
      depthFirstSearchRecursive(graph, nextNode, currentNode, callbacks);
    }
  });
  callbacks.leave({
    current: currentNode,
    previous: previousNode
  });
}
/**
 * 深度优先遍历图
 * @param data GraphData 图数据
 * @param startNodeId 开始遍历的节点的 ID
 * @param originalCallbacks 回调
 */


export default function depthFirstSearch(graph, startNodeId, callbacks) {
  var previousNode = null;
  var startNode = graph.findById(startNodeId);
  depthFirstSearchRecursive(graph, startNode, previousNode, initCallbacks(callbacks));
}