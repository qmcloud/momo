"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _default = {
  version: '3.5.2',
  rootContainerClassName: 'root-container',
  nodeContainerClassName: 'node-container',
  edgeContainerClassName: 'edge-container',
  comboContainerClassName: 'combo-container',
  customGroupContainerClassName: 'custom-group-container',
  delegateContainerClassName: 'delegate-container',
  defaultShapeFillColor: '#C6E5FF',
  defaultShapeStrokeColor: '#5B8FF9',
  defaultLoopPosition: 'top',
  nodeLabel: {
    style: {
      fill: '#595959',
      textAlign: 'center',
      textBaseline: 'middle'
    },
    offset: 5
  },
  defaultNode: {
    type: 'circle',
    style: {
      fill: '#C6E5FF',
      lineWidth: 1,
      stroke: '#5B8FF9'
    },
    size: 20,
    color: '#5B8FF9'
  },
  edgeLabel: {
    style: {
      fill: '#595959',
      textAlign: 'center',
      textBaseline: 'middle'
    }
  },
  defaultEdge: {
    type: 'line',
    style: {
      stroke: '#e2e2e2'
    },
    size: 1,
    color: '#e2e2e2'
  },
  comboLabel: {
    style: {
      fill: '#595959',
      // textAlign: 'center',
      textBaseline: 'middle'
    },
    refY: 10,
    refX: 10
  },
  defaultCombo: {
    type: 'circle',
    style: {
      fill: '#F3F9FF',
      lineWidth: 1,
      stroke: '#A3B1BF',
      opacity: 0.8,
      r: 5,
      width: 20,
      height: 10
    },
    size: [20, 5],
    color: '#A3B1BF',
    padding: [25, 20, 15, 20]
  },
  // 节点应用状态后的样式，默认仅提供 active 和 selected 用户可以自己扩展
  nodeStateStyle: {},
  delegateStyle: {
    fill: '#F3F9FF',
    fillOpacity: 0.5,
    stroke: '#1890FF',
    strokeOpacity: 0.9,
    lineDash: [5, 5]
  }
};
exports.default = _default;