import { __assign } from "tslib";
import { calculationItemsBBox } from '../util/base';
import Global from '../global';
import { each } from '@antv/util/lib';
/**
 * 遍历拖动的 Combo 下的所有 Combo
 * @param data 拖动的 Combo
 * @param fn
 */

var traverseCombo = function traverseCombo(data, fn) {
  if (fn(data) === false) {
    return;
  }

  if (data) {
    var combos = data.get('combos');

    if (combos.length === 0) {
      return false;
    }

    each(combos, function (child) {
      traverseCombo(child, fn);
    });
  }
};

export default {
  getDefaultCfg: function getDefaultCfg() {
    return {
      enableDelegate: false,
      delegateStyle: {},
      // 拖动节点过程中是否只改变 Combo 的大小，而不改变其结构
      onlyChangeComboSize: false,
      // 拖动过程中目标 combo 状态样式
      activeState: '',
      selectedState: 'selected'
    };
  },
  getEvents: function getEvents() {
    return {
      'combo:dragstart': 'onDragStart',
      'combo:drag': 'onDrag',
      'combo:dragend': 'onDragEnd',
      'combo:drop': 'onDrop',
      'combo:dragenter': 'onDragEnter',
      'combo:dragleave': 'onDragLeave'
    };
  },
  validationCombo: function validationCombo(evt) {
    var item = evt.item;

    if (!item) {
      return;
    }

    if (!this.shouldUpdate(this, evt)) {
      return;
    }

    var type = item.getType();

    if (type !== 'combo') {
      return;
    }
  },
  onDragStart: function onDragStart(evt) {
    var _this = this;

    var graph = this.graph;
    var item = evt.item;
    this.validationCombo(item);
    this.targets = []; // 获取所有选中的 Combo

    var combos = graph.findAllByState('combo', this.selectedState);
    var currentCombo = item.get('id');
    var dragCombos = combos.filter(function (combo) {
      var comboId = combo.get('id');
      return currentCombo === comboId;
    });

    if (dragCombos.length === 0) {
      this.targets.push(item);
    } else {
      this.targets = combos;
    }

    if (this.activeState) {
      this.targets.map(function (combo) {
        var model = combo.getModel();

        if (model.parentId) {
          var parentCombo = graph.findById(model.parentId);

          if (parentCombo) {
            graph.setItemState(parentCombo, _this.activeState, true);
          }
        }
      });
    }

    this.point = {};
    this.originPoint = {};
    this.origin = {
      x: evt.x,
      y: evt.y
    };
    this.currentItemChildCombos = [];
    traverseCombo(item, function (param) {
      if (param.destroyed) {
        return false;
      }

      var model = param.getModel();

      _this.currentItemChildCombos.push(model.id);

      return true;
    });
  },
  onDrag: function onDrag(evt) {
    var _this = this;

    if (!this.origin) {
      return;
    }

    this.validationCombo(evt);

    if (this.enableDelegate) {
      this.updateDelegate(evt);
    } else {
      if (this.activeState) {
        var graph_1 = this.graph;
        var item = evt.item;
        var model_1 = item.getModel(); // 拖动过程中实时计算距离

        var combos = graph_1.getCombos();
        var sourceBBox = item.getBBox();
        var centerX_1 = sourceBBox.centerX,
            centerY_1 = sourceBBox.centerY,
            width_1 = sourceBBox.width; // 参与计算的 Combo，需要排除掉：
        // 1、拖动 combo 自己
        // 2、拖动 combo 的 parent
        // 3、拖动 Combo 的 children

        var calcCombos = combos.filter(function (combo) {
          var cmodel = combo.getModel(); // 被拖动的是最外层的 Combo，无 parent，排除自身和子元素

          if (!model_1.parentId) {
            return cmodel.id !== model_1.id && !_this.currentItemChildCombos.includes(cmodel.id);
          }

          return cmodel.id !== model_1.id && !_this.currentItemChildCombos.includes(cmodel.id);
        });
        calcCombos.map(function (combo) {
          var _a = combo.getBBox(),
              cx = _a.centerX,
              cy = _a.centerY,
              w = _a.width; // 拖动的 combo 和要进入的 combo 之间的距离


          var disX = centerX_1 - cx;
          var disY = centerY_1 - cy; // 圆心距离

          var distance = 2 * Math.sqrt(disX * disX + disY * disY);

          if (width_1 + w - distance > 0.8 * width_1) {
            graph_1.setItemState(combo, _this.activeState, true);
          } else {
            graph_1.setItemState(combo, _this.activeState, false);
          }
        });
      }

      each(this.targets, function (item) {
        _this.updateCombo(item, evt);
      });
    }
  },
  onDrop: function onDrop(evt) {
    var _this = this; // 拖动的目标 combo


    var item = evt.item;

    if (!item || !this.targets) {
      return;
    }

    var graph = this.graph;
    var targetModel = item.getModel();
    this.targets.map(function (combo) {
      var model = combo.getModel();

      if (model.parentId !== targetModel.id) {
        if (_this.activeState) {
          graph.setItemState(item, _this.activeState, false);
        } // 将 Combo 放置到某个 Combo 上面时，只有当 onlyChangeComboSize 为 false 时候才更新 Combo 结构


        if (!_this.onlyChangeComboSize) {
          graph.updateComboTree(combo, targetModel.id);
        }
      }
    }); // 如果已经拖放下了，则不需要再通过距离判断了

    this.endComparison = true;
  },
  onDragEnter: function onDragEnter(evt) {
    if (!this.origin) {
      return;
    }

    this.validationCombo(evt);
    var item = evt.item;
    var graph = this.graph;

    if (this.activeState) {
      graph.setItemState(item, this.activeState, true);
    }
  },
  onDragLeave: function onDragLeave(evt) {
    if (!this.origin) {
      return;
    }

    this.validationCombo(evt);
    var item = evt.item;
    var graph = this.graph;

    if (this.activeState) {
      graph.setItemState(item, this.activeState, false);
    }
  },
  onDragEnd: function onDragEnd(evt) {
    var _this = this;

    var graph = this.graph;
    this.validationCombo(evt); // 当启用 delegate 时，拖动结束时需要更新 combo

    if (this.enableDelegate) {
      each(this.targets, function (item) {
        _this.updateCombo(item, evt);
      });
    }

    var item = evt.item; // 表示是否是拖出操作

    var isDragOut = false;
    var model = item.getModel(); // 拖动 Combo 结束后，如果 onlyChangeComboSize 值为 true 则只更新 Combo 位置，不更新结构

    if (this.onlyChangeComboSize) {
      graph.updateCombos();
    } else {
      // 拖动结束时计算拖入还是拖出, 需要更新 combo
      // 1. 是否将当前 combo 拖出了 父 combo；
      // 2. 是否将当前 combo 拖入了新的 combo
      var type = item.getType();

      if (type === 'combo') {
        var parentId = model.parentId;
        var currentBBox = null;
        var parentCombo_1 = this.getParentCombo(parentId); // 当只有存在 parentCombo 时才处理拖出的情况

        if (parentCombo_1) {
          if (this.enableDelegate) {
            currentBBox = this.delegateShape.getBBox();
          } else {
            currentBBox = item.getBBox();
          }

          var cx = currentBBox.x,
              cy = currentBBox.y,
              centerX = currentBBox.centerX,
              centerY = currentBBox.centerY,
              width = currentBBox.width; //判断是否拖出了 combo，需要满足： 
          // 1、有 parent；
          // 2、拿拖动的对象和它父parent比较

          var parentBBox = parentCombo_1.getBBox();
          var minX = parentBBox.minX,
              minY = parentBBox.minY,
              maxX = parentBBox.maxX,
              maxY = parentBBox.maxY,
              pcx = parentBBox.centerX,
              pcy = parentBBox.centerY,
              w = parentBBox.width; // 拖出了父 combo
          // 如果直接拖出到了 父 combo 周边，则不用计算距离圆心距离

          if (cx <= minX || cx >= maxX || cy <= minY || cy >= maxY) {
            if (this.activeState) {
              graph.setItemState(parentCombo_1, this.activeState, false);
            }

            isDragOut = true; // 表示正在拖出操作

            graph.updateComboTree(item);
          } else {
            // 拖动的 combo 和要进入的 combo 之间的距离
            var disX = centerX - pcx;
            var disY = centerY - pcy; // 圆心距离

            var distance = 2 * Math.sqrt(disX * disX + disY * disY); // 拖出的还在父 combo 包围盒范围内，但实际上已经拖出去了

            if (width + w - distance < 0.8 * width) {
              if (this.activeState) {
                graph.setItemState(parentCombo_1, this.activeState, false);
              }

              isDragOut = true;
              graph.updateComboTree(item);
            }
          }
        } // 拖入


        if (!this.endComparison && !isDragOut) {
          // 判断是否拖入了 父 Combo，需要满足：
          // 1、拖放最终位置是 combo，且不是父 Combo；
          // 2、拖动 Combo 进入到非父 Combo 超过 50%；
          var combos = graph.getCombos();
          var sourceBBox = item.getBBox();
          var centerX_2 = sourceBBox.centerX,
              centerY_2 = sourceBBox.centerY,
              width_2 = sourceBBox.width; // 参与计算的 Combo，需要排除掉：
          // 1、拖动 combo 自己
          // 2、拖动 combo 的 parent
          // 3、拖动 Combo 的 children

          var calcCombos = combos.filter(function (combo) {
            var cmodel = combo.getModel(); // 被拖动的是最外层的 Combo，无 parent，排除自身和子元素

            if (!model.parentId) {
              return cmodel.id !== model.id && !_this.currentItemChildCombos.includes(cmodel.id);
            }

            return cmodel.id !== model.id && !_this.currentItemChildCombos.includes(cmodel.id);
          });
          calcCombos.map(function (combo) {
            var current = combo.getModel();

            var _a = combo.getBBox(),
                cx = _a.centerX,
                cy = _a.centerY,
                w = _a.width; // 拖动的 combo 和要进入的 combo 之间的距离


            var disX = centerX_2 - cx;
            var disY = centerY_2 - cy; // 圆心距离

            var distance = 2 * Math.sqrt(disX * disX + disY * disY);

            if (_this.activeState) {
              graph.setItemState(combo, _this.activeState, false);
            }

            if (width_2 + w - distance > 0.8 * width_2) {
              graph.updateComboTree(item, current.id);
            }
          });
        }
      }
    } // 删除delegate shape


    if (this.delegateShape) {
      var delegateGroup = graph.get('delegateGroup');
      delegateGroup.clear();
      this.delegateShape = null;
    }

    var parentCombo = this.getParentCombo(model.parentId);

    if (parentCombo && this.activeState) {
      graph.setItemState(parentCombo, this.activeState, false);
    }

    this.point = [];
    this.origin = null;
    this.originPoint = null;
    this.targets.length = 0;
  },

  /**
   * 遍历 comboTree，分别更新 node 和 combo
   * @param data
   * @param fn
   */
  traverse: function traverse(data, fn) {
    var _this = this;

    if (fn(data) === false) {
      return;
    }

    if (data) {
      var combos = data.get('combos');
      each(combos, function (child) {
        _this.traverse(child, fn);
      });
      var nodes = data.get('nodes');
      each(nodes, function (child) {
        _this.traverse(child, fn);
      });
    }
  },
  updateCombo: function updateCombo(item, evt) {
    var _this = this;

    this.traverse(item, function (param) {
      if (param.destroyed) {
        return false;
      }

      _this.updateSignleItem(param, evt);

      return true;
    });
  },

  /**
   *
   * @param item 当前正在拖动的元素
   * @param evt
   */
  updateSignleItem: function updateSignleItem(item, evt) {
    var origin = this.origin;
    var graph = this.graph;
    var model = item.getModel();
    var itemId = item.get('id');

    if (!this.point[itemId]) {
      this.point[itemId] = {
        x: model.x,
        y: model.y
      };
    }

    var x = evt.x - origin.x + this.point[itemId].x;
    var y = evt.y - origin.y + this.point[itemId].y;
    graph.updateItem(item, {
      x: x,
      y: y
    });
  },

  /**
   * 根据 ID 获取父 Combo
   * @param parentId 父 Combo ID
   */
  getParentCombo: function getParentCombo(parentId) {
    var graph = this.graph;

    if (!parentId) {
      return undefined;
    }

    var parentCombo = graph.findById(parentId);

    if (!parentCombo) {
      return undefined;
    }

    return parentCombo;
  },
  updateDelegate: function updateDelegate(evt) {
    var graph = this.graph; // 当没有 delegate shape 时创建

    if (!this.delegateShape) {
      var delegateGroup = graph.get('delegateGroup');
      var bbox = null;

      if (this.targets.length > 1) {
        bbox = calculationItemsBBox(this.targets);
      } else {
        bbox = this.targets[0].getBBox();
      }

      var x = bbox.x,
          y = bbox.y,
          width = bbox.width,
          height = bbox.height,
          minX = bbox.minX,
          minY = bbox.minY;
      this.originPoint = {
        x: x,
        y: y,
        width: width,
        height: height,
        minX: minX,
        minY: minY
      };
      var attrs = Object.assign({}, Global.delegateStyle, this.delegateStyle);
      this.delegateShape = delegateGroup.addShape('rect', {
        attrs: __assign({
          width: bbox.width,
          height: bbox.height,
          x: bbox.x,
          y: bbox.y
        }, attrs),
        name: 'combo-delegate-shape'
      });
    } else {
      var clientX = evt.x - this.origin.x + this.originPoint.minX;
      var clientY = evt.y - this.origin.y + this.originPoint.minY;
      this.delegateShape.attr({
        x: clientX,
        y: clientY
      });
    }
  }
};