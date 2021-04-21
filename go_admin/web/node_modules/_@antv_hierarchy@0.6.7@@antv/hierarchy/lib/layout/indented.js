function positionNode(node, previousNode, dx, dropCap) {
  if (!dropCap) {
    try {
      if (node.id === node.parent.children[0].id) {
        node.x += dx * node.depth;
        node.y = previousNode ? previousNode.y : 0;
        return;
      }
    } catch (e) {// skip to normal when a node has no parent
    }
  }

  node.x += dx * node.depth;
  node.y = previousNode ? previousNode.y + previousNode.height : 0;
  return;
}

module.exports = function (root, indent, dropCap) {
  var previousNode = null;
  root.eachNode(function (node) {
    positionNode(node, previousNode, indent, dropCap);
    previousNode = node;
  });
};