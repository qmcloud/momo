import clone from '@antv/util/lib/clone';
import deepMix from '@antv/util/lib/deep-mix';
import each from '@antv/util/lib/each';
import isArray from '@antv/util/lib/is-array';
import isObject from '@antv/util/lib/is-object';
import isString from '@antv/util/lib/is-string';
import upperFirst from '@antv/util/lib/upper-first';
import Edge from '../../item/edge';
import Node from '../../item/node';
import Combo from '../../item/combo';
import { traverseTreeUp, traverseTree, getComboBBox } from '../../util/graphic';
var NODE = 'node';
var EDGE = 'edge';
var VEDGE = 'vedge';
var COMBO = 'combo';
var CFG_PREFIX = 'default';
var MAPPER_SUFFIX = 'Mapper';
var STATE_SUFFIX = 'stateStyles';
var hasOwnProperty = Object.hasOwnProperty;

var ItemController =
/** @class */
function () {
  function ItemController(graph) {
    this.graph = graph;
    this.destroyed = false;
  }
  /**
   * 增加 Item 实例
   *
   * @param {ITEM_TYPE} type 实例类型，node 或 edge
   * @param {(NodeConfig & EdgeConfig)} model 数据模型
   * @returns {(Item)}
   * @memberof ItemController
   */


  ItemController.prototype.addItem = function (type, model) {
    var _this = this;

    var graph = this.graph;
    var parent = graph.get(type + "Group") || graph.get('group');
    var vType = type === VEDGE ? EDGE : type;
    var upperType = upperFirst(vType);
    var item = null; // 获取 this.get('styles') 中的值

    var styles = graph.get(vType + upperFirst(STATE_SUFFIX)) || {};
    var defaultModel = graph.get(CFG_PREFIX + upperType);

    if (model[STATE_SUFFIX]) {
      // 设置 this.get('styles') 中的值
      styles = model[STATE_SUFFIX];
    }

    var mapper = graph.get(vType + MAPPER_SUFFIX);

    if (mapper) {
      var mappedModel = mapper(model);

      if (mappedModel[STATE_SUFFIX]) {
        // 设置 this.get('styles') 中的值
        styles = mappedModel[STATE_SUFFIX];
        delete mappedModel[STATE_SUFFIX];
      } // 如果配置了 defaultEdge 或 defaultNode，则将默认配置的数据也合并进去


      model = deepMix({}, defaultModel, model, mappedModel);
    } else if (defaultModel) {
      // 很多布局会直接修改原数据模型，所以不能用 merge 的形式，逐个写入原 model 中
      each(defaultModel, function (val, cfg) {
        if (!hasOwnProperty.call(model, cfg)) {
          if (isObject(val)) {
            model[cfg] = clone(val);
          } else {
            model[cfg] = defaultModel[cfg];
          }
        }
      });
    }

    if (model.shape && !model.type) {
      console.warn('shape 字段即将被废弃，请使用 type 代替');
    }

    graph.emit('beforeadditem', {
      type: type,
      model: model
    });

    if (type === EDGE || type === VEDGE) {
      var source = void 0;
      var target = void 0;
      source = model.source; // eslint-disable-line prefer-destructuring

      target = model.target; // eslint-disable-line prefer-destructuring

      if (source && isString(source)) {
        source = graph.findById(source);
      }

      if (target && isString(target)) {
        target = graph.findById(target);
      }

      if (!source || !target) {
        console.warn("The source or target node of edge " + model.id + " does not exist!");
        return;
      }

      if (source.getType && source.getType() === 'combo') model.isComboEdge = true;
      if (target.getType && target.getType() === 'combo') model.isComboEdge = true;
      item = new Edge({
        model: model,
        source: source,
        target: target,
        styles: styles,
        linkCenter: graph.get('linkCenter'),
        group: parent.addGroup()
      });
    } else if (type === NODE) {
      item = new Node({
        model: model,
        styles: styles,
        group: parent.addGroup()
      });
    } else if (type === COMBO) {
      var children = model.children;
      var comboBBox = getComboBBox(children, graph);
      model.x = comboBBox.x || Math.random() * 100;
      model.y = comboBBox.y || Math.random() * 100;
      var comboGroup = parent.addGroup();
      comboGroup.setZIndex(model.depth);
      item = new Combo({
        model: model,
        styles: styles,
        bbox: comboBBox,
        group: comboGroup
      });
      children && children.forEach(function (child) {
        var childItem = graph.findById(child.id);
        item.addChild(childItem);
      }); // collapse the combo if the collapsed is true in the model

      if (model.collapsed) {
        setTimeout(function () {
          graph.collapseCombo(item);

          _this.updateCombo(item, []);
        }, 250);
      }
    }

    if (item) {
      graph.get(type + "s").push(item);
      graph.get('itemMap')[item.get('id')] = item;
      graph.emit('afteradditem', {
        item: item,
        model: model
      }); // eslint-disable-next-line consistent-return

      return item;
    }
  };
  /**
   * 更新节点或边
   *
   * @param {Item} item ID 或 实例
   * @param {(EdgeConfig | Partial<NodeConfig>)} cfg 数据模型
   * @returns
   * @memberof ItemController
   */


  ItemController.prototype.updateItem = function (item, cfg) {
    var graph = this.graph;

    if (isString(item)) {
      item = graph.findById(item);
    }

    if (!item || item.destroyed) {
      return;
    } // 更新的 item 的类型


    var type = '';
    if (item.getType) type = item.getType();
    var mapper = graph.get(type + MAPPER_SUFFIX);
    var model = item.getModel();

    if (mapper) {
      var result = deepMix({}, model, cfg);
      var mappedModel = mapper(result); // 将 update 时候用户传入的参数与mapperModel做deepMix，以便复用之前设置的参数值

      var newModel = deepMix({}, model, mappedModel, cfg);

      if (mappedModel[STATE_SUFFIX]) {
        item.set('styles', newModel[STATE_SUFFIX]);
        delete newModel[STATE_SUFFIX];
      }

      each(newModel, function (val, key) {
        cfg[key] = val;
      });
    } else {
      // merge update传进来的对象参数，model中没有的数据不做处理，对象和字符串值也不做处理，直接替换原来的
      each(cfg, function (val, key) {
        if (model[key]) {
          if (isObject(val) && !isArray(val)) {
            cfg[key] = Object.assign({}, model[key], cfg[key]);
          }
        }
      });
    } // emit beforeupdateitem 事件


    graph.emit('beforeupdateitem', {
      item: item,
      cfg: cfg
    });

    if (type === EDGE) {
      // 若是边要更新source || target, 为了不影响示例内部model，并且重新计算startPoint和endPoint，手动设置
      if (cfg.source) {
        var source = cfg.source;

        if (isString(source)) {
          source = graph.findById(source);
        }

        item.setSource(source);
      }

      if (cfg.target) {
        var target = cfg.target;

        if (isString(target)) {
          target = graph.findById(target);
        }

        item.setTarget(target);
      }
    }

    item.update(cfg);

    if (type === NODE || type === COMBO) {
      var edges = item.getEdges();
      each(edges, function (edge) {
        graph.refreshItem(edge);
      });
    }

    graph.emit('afterupdateitem', {
      item: item,
      cfg: cfg
    });
  };
  /**
   * 根据 combo 的子元素更新 combo 的位置及大小
   *
   * @param {ICombo} combo ID 或 实例
   * @returns
   * @memberof ItemController
   */


  ItemController.prototype.updateCombo = function (combo, children) {
    var graph = this.graph;

    if (isString(combo)) {
      combo = graph.findById(combo);
    }

    if (!combo || combo.destroyed) {
      return;
    }

    var comboBBox = getComboBBox(children, graph);
    combo.set('bbox', comboBBox);
    combo.update({
      x: comboBBox.x,
      y: comboBBox.y
    });
  };
  /**
   * 收起 combo，隐藏相关元素
   */


  ItemController.prototype.collapseCombo = function (combo) {
    var graph = this.graph;

    if (isString(combo)) {
      combo = graph.findById(combo);
    }

    var children = combo.getChildren();
    children.nodes.forEach(function (node) {
      graph.hideItem(node);
    });
    children.combos.forEach(function (combo) {
      graph.hideItem(combo);
    });
  };
  /**
   * 展开 combo，相关元素出现
   * 若子 combo 原先是收起状态，则保持它的收起状态
   */


  ItemController.prototype.expandCombo = function (combo) {
    var graph = this.graph;

    if (isString(combo)) {
      combo = graph.findById(combo);
    }

    var children = combo.getChildren();
    children.nodes.forEach(function (node) {
      graph.showItem(node);
    });
    children.combos.forEach(function (combo) {
      if (combo.getModel().collapsed) {
        combo.show();
      } else {
        graph.showItem(combo);
      }
    });
  };
  /**
   * 删除指定的节点或边
   *
   * @param {Item} item item ID 或实例
   * @returns {void}
   * @memberof ItemController
   */


  ItemController.prototype.removeItem = function (item) {
    var _this = this;

    var graph = this.graph;

    if (isString(item)) {
      item = graph.findById(item);
    }

    if (!item || item.destroyed) {
      return;
    }

    graph.emit('beforeremoveitem', {
      item: item
    });
    var type = '';
    if (item.getType) type = item.getType();
    var items = graph.get(type + "s");
    var index = items.indexOf(item);
    if (index > -1) items.splice(index, 1);

    if (type === EDGE) {
      var vitems = graph.get("v" + type + "s");
      var vindex = vitems.indexOf(item);
      if (vindex > -1) vitems.splice(vindex, 1);
    }

    var itemId = item.get('id');
    var itemMap = graph.get('itemMap');
    delete itemMap[itemId];
    var comboTrees = graph.get('comboTrees');
    var id = item.get('id');

    if (type === NODE) {
      if (comboTrees) {
        var brothers_1 = comboTrees;
        var found_1 = false; // the flag to terminate the forEach circulation
        // remove the node from the children array of its parent fromt he tree

        comboTrees.forEach(function (ctree) {
          if (found_1) return;
          traverseTree(ctree, function (combo) {
            if (combo.id === id && brothers_1) {
              var index_1 = brothers_1.indexOf(combo);
              brothers_1.splice(index_1, 1);
              found_1 = true;
              return false; // terminate the traverse
            }

            brothers_1 = combo.children;
            return true;
          });
        });
      } // 若移除的是节点，需要将与之相连的边一同删除


      var edges = item.getEdges();

      for (var i = edges.length; i >= 0; i--) {
        graph.removeItem(edges[i]);
      }
    } else if (type === COMBO) {
      var comboInTree_1; // find the subtree rooted at the item to be removed

      var found_2 = false; // the flag to terminate the forEach circulation

      comboTrees && comboTrees.forEach(function (ctree) {
        if (found_2) return;
        traverseTree(ctree, function (combo) {
          if (combo.id === id) {
            comboInTree_1 = combo;
            found_2 = true;
            return false; // terminate the traverse
          }

          return true;
        });
      });
      comboInTree_1.removed = true;

      if (comboInTree_1 && comboInTree_1.children) {
        comboInTree_1.children.forEach(function (child) {
          _this.removeItem(child.id);
        });
      } // 若移除的是 combo，需要将与之相连的边一同删除


      var edges = item.getEdges();

      for (var i = edges.length; i >= 0; i--) {
        graph.removeItem(edges[i]);
      }
    }

    item.destroy();
    graph.emit('afterremoveitem', {
      item: item
    });
  };
  /**
   * 更新 item 状态
   *
   * @param {Item} item Item 实例
   * @param {string} state 状态名称
   * @param {boolean} value 是否启用状态或状态值
   * @returns {void}
   * @memberof ItemController
   */


  ItemController.prototype.setItemState = function (item, state, value) {
    var graph = this.graph;
    var stateName = state;

    if (isString(value)) {
      stateName = state + ":" + value;
    }

    if (item.hasState(stateName) === value || isString(value) && item.hasState(stateName)) {
      return;
    }

    graph.emit('beforeitemstatechange', {
      item: item,
      state: stateName,
      enabled: value
    });
    item.setState(state, value);
    graph.autoPaint();
    graph.emit('afteritemstatechange', {
      item: item,
      state: stateName,
      enabled: value
    });
  };
  /**
   * 清除所有指定的状态
   *
   * @param {Item} item Item 实例
   * @param {string[]} states 状态名称集合
   * @memberof ItemController
   */


  ItemController.prototype.clearItemStates = function (item, states) {
    var graph = this.graph;

    if (isString(item)) {
      item = graph.findById(item);
    }

    graph.emit('beforeitemstatesclear', {
      item: item,
      states: states
    });
    item.clearStates(states);
    graph.emit('afteritemstatesclear', {
      item: item,
      states: states
    });
  };
  /**
   * 刷新指定的 Item
   *
   * @param {Item} item Item ID 或 实例
   * @memberof ItemController
   */


  ItemController.prototype.refreshItem = function (item) {
    var graph = this.graph;

    if (isString(item)) {
      item = graph.findById(item);
    }

    graph.emit('beforeitemrefresh', {
      item: item
    }); // 调用 Item 的 refresh 方法，实现刷新功能

    item.refresh();
    graph.emit('afteritemrefresh', {
      item: item
    });
  };
  /**
   * 根据 graph 上用 combos 数据生成的 comboTree 来增加所有 combos
   *
   * @param {ComboTree[]} comboTrees graph 上用 combos 数据生成的 comboTree
   * @param {ComboConfig[]} comboModels combos 数据
   * @memberof ItemController
   */


  ItemController.prototype.addCombos = function (comboTrees, comboModels) {
    var _this = this;

    comboTrees && comboTrees.forEach(function (ctree) {
      traverseTreeUp(ctree, function (child) {
        var comboModel;
        comboModels.forEach(function (model) {
          if (model.id === child.id) {
            model.children = child.children;
            model.depth = child.depth;
            comboModel = model;
          }
        });

        if (comboModel) {
          _this.addItem('combo', comboModel);
        }

        return true;
      });
    });
    var comboGroup = this.graph.get('comboGroup');
    if (comboGroup) comboGroup.sort();
  };
  /**
   * 改变Item的显示状态
   *
   * @param {Item} item Item ID 或 实例
   * @param {boolean} visible 是否显示
   * @memberof ItemController
   */


  ItemController.prototype.changeItemVisibility = function (item, visible) {
    var _this = this;

    var graph = this.graph;

    if (isString(item)) {
      item = graph.findById(item);
    }

    if (!item) {
      console.warn('The item to be shown or hidden does not exist!');
      return;
    }

    graph.emit('beforeitemvisibilitychange', {
      item: item,
      visible: visible
    });
    item.changeVisibility(visible);

    if (item.getType && item.getType() === NODE) {
      var edges = item.getEdges();
      each(edges, function (edge) {
        // 若隐藏节点，则将与之关联的边也隐藏
        // 若显示节点，则将与之关联的边也显示，但是需要判断边两端的节点都是可见的
        if (visible && !(edge.get('source').isVisible() && edge.get('target').isVisible())) {
          return;
        }

        _this.changeItemVisibility(edge, visible);
      });
    } else if (item.getType && item.getType() === COMBO) {
      var comboTrees = graph.get('comboTrees');
      var id_1 = item.get('id');
      var children_1 = [];
      var found_3 = false; // flag the terminate the forEach

      comboTrees && comboTrees.forEach(function (ctree) {
        if (found_3) return;
        if (!ctree.children || ctree.children.length === 0) return;
        traverseTree(ctree, function (combo) {
          if (combo.id === id_1) {
            children_1 = combo.children;
            found_3 = true;
            return false; // terminate the traverse
          }

          return true;
        });
      });
      children_1.forEach(function (child) {
        var childItem = graph.findById(child.id);

        _this.changeItemVisibility(childItem, visible);
      });
      var edges = item.getEdges();
      each(edges, function (edge) {
        // 若隐藏 combo，则将与 combo 本身关联的边也隐藏
        // 若显示 combo，则将与 combo 本身关联的边也显示，但是需要判断边两端的节点都是可见的
        if (visible && !(edge.get('source').isVisible() && edge.get('target').isVisible())) {
          return;
        }

        _this.changeItemVisibility(edge, visible);
      });
    }

    graph.emit('afteritemvisibilitychange', {
      item: item,
      visible: visible
    });
  };

  ItemController.prototype.destroy = function () {
    this.graph = null;
    this.destroyed = true;
  };

  return ItemController;
}();

export default ItemController;