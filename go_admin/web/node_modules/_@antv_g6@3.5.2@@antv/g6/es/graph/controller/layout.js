function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

import Layout from '../../layout';
import LayoutWorker from '../../layout/worker/layout.worker';
import { LAYOUT_MESSAGE } from '../../layout/worker/layoutConst';
import { isNaN } from '../../util/base';
var helper = {
  // pollyfill
  requestAnimationFrame: function requestAnimationFrame(callback) {
    var fn = window.requestAnimationFrame || window.webkitRequestAnimationFrame || function (cb) {
      return setTimeout(cb, 16);
    };

    return fn(callback);
  },
  cancelAnimationFrame: function cancelAnimationFrame(requestId) {
    var fn = window.cancelAnimationFrame || window.webkitCancelAnimationFrame || function (reqId) {
      return clearTimeout(reqId);
    };

    return fn(requestId);
  }
};

var LayoutController =
/** @class */
function () {
  function LayoutController(graph) {
    this.graph = graph;
    this.layoutCfg = graph.get('layout') || {};
    this.layoutType = this.layoutCfg.type;
    this.worker = null;
    this.workerData = {};
    this.initLayout();
  } // eslint-disable-next-line class-methods-use-this


  LayoutController.prototype.initLayout = function () {// no data before rendering
  }; // get layout worker and create one if not exists


  LayoutController.prototype.getWorker = function () {
    if (this.worker) {
      return this.worker;
    }

    if (typeof Worker === 'undefined') {
      // 如果当前浏览器不支持 web worker，则不使用 web worker
      console.warn('Web worker is not supported in current browser.');
      this.worker = null;
    } else {
      this.worker = new LayoutWorker();
    }

    return this.worker;
  }; // stop layout worker


  LayoutController.prototype.stopWorker = function () {
    var workerData = this.workerData;

    if (!this.worker) {
      return;
    }

    this.worker.terminate();
    this.worker = null; // 重新开始新的布局之前，先取消之前布局的requestAnimationFrame。

    if (workerData.requestId) {
      helper.cancelAnimationFrame(workerData.requestId);
      workerData.requestId = null;
    }

    if (workerData.requestId2) {
      helper.cancelAnimationFrame(workerData.requestId2);
      workerData.requestId2 = null;
    }
  };

  LayoutController.prototype.getLayoutType = function () {
    return this.layoutCfg.type;
  };
  /**
   * @param {function} success callback
   * @return {boolean} 是否使用web worker布局
   */


  LayoutController.prototype.layout = function (success) {
    var graph = this.graph;
    this.data = this.setDataFromGraph();
    var nodes = this.data.nodes;

    if (!nodes) {
      return false;
    }

    var width = graph.get('width');
    var height = graph.get('height');
    var layoutCfg = {};
    Object.assign(layoutCfg, {
      width: width,
      height: height,
      center: [width / 2, height / 2]
    }, this.layoutCfg);
    this.layoutCfg = layoutCfg;
    var hasLayoutType = !!this.layoutType;
    var layoutMethod = this.layoutMethod;

    if (layoutMethod) {
      layoutMethod.destroy();
    }

    graph.emit('beforelayout');
    var allHavePos = this.initPositions(layoutCfg.center, nodes);
    this.stopWorker();

    if (layoutCfg.workerEnabled && this.layoutWithWorker(this.data, success)) {
      // 如果启用布局web worker并且浏览器支持web worker，用web worker布局。否则回退到不用web worker布局。
      return true;
    }

    if (this.layoutType === 'force' || this.layoutType === 'g6force') {
      var onTick_1 = layoutCfg.onTick;

      var tick = function tick() {
        if (onTick_1) {
          onTick_1();
        }

        graph.refreshPositions();
      };

      layoutCfg.tick = tick;
      var onLayoutEnd_1 = layoutCfg.onLayoutEnd;

      layoutCfg.onLayoutEnd = function () {
        if (onLayoutEnd_1) {
          onLayoutEnd_1();
        }

        graph.emit('afterlayout');
      };
    } else if (this.layoutType === 'comboForce') {
      layoutCfg.comboTrees = graph.get('comboTrees');
    }

    if (this.layoutType !== undefined) {
      try {
        layoutMethod = new Layout[this.layoutType](layoutCfg);
      } catch (e) {
        console.warn("The layout method: " + this.layoutType + " does not exist! Please specify it first.");
        return false;
      }

      layoutMethod.init(this.data); // 若存在节点没有位置信息，且没有设置 layout，在 initPositions 中 random 给出了所有节点的位置，不需要再次执行 random 布局
      // 所有节点都有位置信息，且指定了 layout，则执行布局（代表不是第一次进行布局）

      if (hasLayoutType) {
        layoutMethod.execute();
      }

      this.layoutMethod = layoutMethod;
    }

    if (hasLayoutType && this.layoutType !== 'force' || !allHavePos && this.layoutType !== 'force') {
      graph.emit('afterlayout');
      this.refreshLayout();
    }

    return false;
  };
  /**
   * layout with web worker
   * @param {object} data graph data
   * @param {function} success callback function
   * @return {boolean} 是否支持web worker
   */


  LayoutController.prototype.layoutWithWorker = function (data, success) {
    var _this = this;

    var nodes = data.nodes,
        edges = data.edges;

    var _a = this,
        layoutCfg = _a.layoutCfg,
        graph = _a.graph;

    var worker = this.getWorker(); // 每次worker message event handler调用之间的共享数据，会被修改。

    var workerData = this.workerData;

    if (!worker) {
      return false;
    }

    workerData.requestId = null;
    workerData.requestId2 = null;
    workerData.currentTick = null;
    workerData.currentTickData = null;
    graph.emit('beforelayout'); // NOTE: postMessage的message参数里面不能包含函数，否则postMessage会报错，
    // 例如：'function could not be cloned'。
    // 详情参考：https://developer.mozilla.org/en-US/docs/Web/API/Web_Workers_API/Structured_clone_algorithm
    // 所以这里需要把过滤layoutCfg里的函数字段过滤掉。

    var filteredLayoutCfg = filterObject(layoutCfg, function (value) {
      return typeof value !== 'function';
    });
    worker.postMessage({
      type: LAYOUT_MESSAGE.RUN,
      nodes: nodes,
      edges: edges,
      layoutCfg: filteredLayoutCfg
    });

    worker.onmessage = function (event) {
      _this.handleWorkerMessage(event, data, success);
    };

    return true;
  }; // success callback will be called when updating graph positions for the first time.


  LayoutController.prototype.handleWorkerMessage = function (event, data, success) {
    var _a = this,
        graph = _a.graph,
        workerData = _a.workerData,
        layoutCfg = _a.layoutCfg;

    var eventData = event.data;
    var type = eventData.type;

    var onTick = function onTick() {
      if (layoutCfg.onTick) {
        layoutCfg.onTick();
      }
    };

    var onLayoutEnd = function onLayoutEnd() {
      if (layoutCfg.onLayoutEnd) {
        layoutCfg.onLayoutEnd();
      }

      graph.emit('afterlayout');
    };

    switch (type) {
      case LAYOUT_MESSAGE.TICK:
        workerData.currentTick = eventData.currentTick;
        workerData.currentTickData = eventData;

        if (!workerData.requestId) {
          workerData.requestId = helper.requestAnimationFrame(function () {
            updateLayoutPosition(data, eventData);
            graph.refreshPositions();
            onTick();

            if (eventData.currentTick === 1 && success) {
              success();
            }

            if (eventData.currentTick === eventData.totalTicks) {
              // 如果是最后一次tick
              onLayoutEnd();
            } else if (workerData.currentTick === eventData.totalTicks) {
              // 注意这里workerData.currentTick可能已经不再是前面赋值时候的值了，
              // 因为在requestAnimationFrame等待时间里，可能产生新的tick。
              // 如果当前tick不是最后一次tick，并且所有的tick消息都已发出来了，那么需要用最后一次tick的数据再刷新一次。
              workerData.requestId2 = helper.requestAnimationFrame(function () {
                updateLayoutPosition(data, workerData.currentTickData);
                graph.refreshPositions();
                workerData.requestId2 = null;
                onTick();
                onLayoutEnd();
              });
            }

            workerData.requestId = null;
          });
        }

        break;

      case LAYOUT_MESSAGE.END:
        // 如果没有tick消息（非力导布局）
        if (workerData.currentTick == null) {
          updateLayoutPosition(data, eventData);
          this.refreshLayout(); // 非力导布局，没有tick消息，只有end消息，所以需要执行一次回调。

          if (success) {
            success();
          }

          graph.emit('afterlayout');
        }

        break;

      case LAYOUT_MESSAGE.ERROR:
        break;

      default:
        break;
    }
  }; // 绘制


  LayoutController.prototype.refreshLayout = function () {
    var graph = this.graph;

    if (graph.get('animate')) {
      graph.positionsAnimate();
    } else {
      graph.refreshPositions();
    }
  }; // 更新布局参数


  LayoutController.prototype.updateLayoutCfg = function (cfg) {
    var _a = this,
        graph = _a.graph,
        layoutMethod = _a.layoutMethod;

    this.layoutType = cfg.type;

    if (!layoutMethod) {
      console.warn('You did not assign any layout type and the graph has no previous layout method!');
      return;
    }

    this.data = this.setDataFromGraph();
    this.stopWorker();

    if (cfg.workerEnabled && this.layoutWithWorker(this.data, null)) {
      // 如果启用布局web worker并且浏览器支持web worker，用web worker布局。否则回退到不用web worker布局。
      return;
    }

    layoutMethod.init(this.data);
    layoutMethod.updateCfg(cfg);
    graph.emit('beforelayout');
    layoutMethod.execute();

    if (this.layoutType !== 'force') {
      graph.emit('afterlayout');
    }

    this.refreshLayout();
  }; // 更换布局


  LayoutController.prototype.changeLayout = function (layoutType) {
    var _a = this,
        graph = _a.graph,
        layoutMethod = _a.layoutMethod;

    this.layoutType = layoutType;
    this.layoutCfg = graph.get('layout') || {};
    this.layoutCfg.type = layoutType;

    if (layoutMethod) {
      layoutMethod.destroy();
    }

    this.layout();
  }; // 更换数据


  LayoutController.prototype.changeData = function () {
    var layoutMethod = this.layoutMethod;

    if (layoutMethod) {
      layoutMethod.destroy();
    }

    this.layout();
  }; // 从 this.graph 获取数据


  LayoutController.prototype.setDataFromGraph = function () {
    var nodes = [];
    var edges = [];
    var combos = [];
    var nodeItems = this.graph.getNodes();
    var edgeItems = this.graph.getEdges();
    var comboItems = this.graph.getCombos();
    nodeItems.forEach(function (nodeItem) {
      if (!nodeItem.isVisible()) return;
      var model = nodeItem.getModel();
      nodes.push(model);
    });
    edgeItems.forEach(function (edgeItem) {
      if (edgeItem.destroyed || !edgeItem.isVisible()) return;
      var model = edgeItem.getModel();
      if (!model.isComboEdge) edges.push(model);
    });
    comboItems.forEach(function (comboItem) {
      if (comboItem.destroyed || !comboItem.isVisible()) return;
      var model = comboItem.getModel();
      combos.push(model);
    });
    var data = {
      nodes: nodes,
      edges: edges,
      combos: combos
    };
    return data;
  }; // 重新布局


  LayoutController.prototype.relayout = function (reloadData) {
    var _a = this,
        graph = _a.graph,
        layoutMethod = _a.layoutMethod,
        layoutCfg = _a.layoutCfg;

    if (reloadData) {
      this.data = this.setDataFromGraph();
      var nodes = this.data.nodes;

      if (!nodes) {
        return false;
      }

      this.initPositions(layoutCfg.center, nodes);
      layoutMethod.init(this.data);
    }

    if (this.layoutType === 'force') {
      layoutMethod.ticking = false;
      layoutMethod.forceSimulation.stop();
    }

    graph.emit('beforelayout');
    layoutMethod.execute();

    if (this.layoutType !== 'force') {
      graph.emit('afterlayout');
    }

    this.refreshLayout();
  }; // 控制布局动画
  // eslint-disable-next-line class-methods-use-this


  LayoutController.prototype.layoutAnimate = function () {}; // // 根据 type 创建 Layout 实例
  // private _getLayout() {
  // }
  // 将当前节点的平均中心移动到原点


  LayoutController.prototype.moveToZero = function () {
    var graph = this.graph;
    var data = graph.get('data');
    var nodes = data.nodes;

    if (nodes[0].x === undefined || nodes[0].x === null || isNaN(nodes[0].x)) {
      return;
    }

    var meanCenter = [0, 0];
    nodes.forEach(function (node) {
      meanCenter[0] += node.x;
      meanCenter[1] += node.y;
    });
    meanCenter[0] /= nodes.length;
    meanCenter[1] /= nodes.length;
    nodes.forEach(function (node) {
      node.x -= meanCenter[0];
      node.y -= meanCenter[1];
    });
  }; // 初始化节点到 center 附近


  LayoutController.prototype.initPositions = function (center, nodes) {
    var graph = this.graph;

    if (!nodes) {
      return false;
    }

    var allHavePos = true;
    nodes.forEach(function (node) {
      if (isNaN(node.x)) {
        allHavePos = false;
        node.x = (Math.random() - 0.5) * 0.7 * graph.get('width') + center[0];
      }

      if (isNaN(node.y)) {
        allHavePos = false;
        node.y = (Math.random() - 0.5) * 0.7 * graph.get('height') + center[1];
      }
    });
    return allHavePos;
  };

  LayoutController.prototype.destroy = function () {
    var layoutMethod = this.layoutMethod;
    this.graph = null;

    if (layoutMethod) {
      layoutMethod.destroy();
    }

    var worker = this.worker;

    if (worker) {
      worker.terminate();
      this.worker = null;
    }

    this.destroyed = true;
  };

  return LayoutController;
}();

export default LayoutController;

function updateLayoutPosition(data, layoutData) {
  var nodes = data.nodes;
  var layoutNodes = layoutData.nodes;
  nodes.forEach(function (node, i) {
    node.x = layoutNodes[i].x;
    node.y = layoutNodes[i].y;
  });
}

function filterObject(collection, callback) {
  var result = {};

  if (collection && _typeof(collection) === 'object') {
    Object.keys(collection).forEach(function (key) {
      if (collection.hasOwnProperty(key) && callback(collection[key])) {
        result[key] = collection[key];
      }
    });
    return result;
  }

  return collection;
}