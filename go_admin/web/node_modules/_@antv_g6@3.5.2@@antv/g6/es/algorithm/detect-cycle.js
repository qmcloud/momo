import dfs from './dfs';

var detectDirectedCycle = function detectDirectedCycle(graph) {
  var cycle = null;
  var dfsParentMap = {}; // 所有没有被访问的节点集合

  var unvisitedSet = {}; // 正在被访问的节点集合

  var visitingSet = {}; // 所有已经被访问过的节点集合

  var visitedSet = {}; // 初始化 unvisitedSet

  graph.getNodes().forEach(function (node) {
    unvisitedSet[node.getID()] = node;
  });
  var callbacks = {
    enter: function enter(_a) {
      var currentNode = _a.current,
          previousNode = _a.previous;

      if (visitingSet[currentNode.getID()]) {
        // 如果当前节点正在访问中，则说明检测到环路了
        cycle = {};
        var currentCycleNode = currentNode;
        var previousCycleNode = previousNode;

        while (previousCycleNode.getID() !== currentNode.getID()) {
          cycle[currentCycleNode.getID()] = previousCycleNode;
          currentCycleNode = previousCycleNode;
          previousCycleNode = dfsParentMap[previousCycleNode.getID()];
        }

        cycle[currentCycleNode.getID()] = previousCycleNode;
      } else {
        // 如果不存在正在访问集合中，则将其放入正在访问集合，并从未访问集合中删除
        visitingSet[currentNode.getID()] = currentNode;
        delete unvisitedSet[currentNode.getID()]; // 更新 DSF parents 列表

        dfsParentMap[currentNode.getID()] = previousNode;
      }
    },
    leave: function leave(_a) {
      var currentNode = _a.current; // 如果所有的节点的子节点都已经访问过了，则从正在访问集合中删除掉，并将其移入到已访问集合中，
      // 同时也意味着当前节点的所有邻居节点都被访问过了

      visitedSet[currentNode.getID()] = currentNode;
      delete visitingSet[currentNode.getID()];
    },
    allowTraversal: function allowTraversal(_a) {
      var nextNode = _a.next; // 如果检测到环路则需要终止所有进一步的遍历，否则会导致无限循环遍历

      if (cycle) {
        return false;
      } // 仅允许遍历没有访问的节点，visitedSet 中的都已经访问过了


      return !visitedSet[nextNode.getID()];
    }
  }; // 开始遍历节点

  while (Object.keys(unvisitedSet).length) {
    // 从第一个节点开始进行 DFS 遍历
    var firsetUnVisitedKey = Object.keys(unvisitedSet)[0];
    dfs(graph, firsetUnVisitedKey, callbacks);
  }

  return cycle;
};

export default detectDirectedCycle;