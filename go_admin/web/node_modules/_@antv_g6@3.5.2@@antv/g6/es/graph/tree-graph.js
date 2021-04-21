import { __extends } from "tslib";
import Hierarchy from '@antv/hierarchy';
import { each, isString } from '@antv/util/lib';
import { radialLayout, traverseTree } from '../util/graphic';
import Graph from './graph';

var TreeGraph =
/** @class */
function (_super) {
  __extends(TreeGraph, _super);

  function TreeGraph(cfg) {
    var _this = _super.call(this, cfg) || this;

    _this.layoutAnimating = false; // 用于缓存动画结束后需要删除的节点

    _this.set('removeList', []);

    _this.set('layoutMethod', _this.getLayout());

    return _this;
  }
  /**
   * 通过 Layout 配置获取布局配置
   */


  TreeGraph.prototype.getLayout = function () {
    var layout = this.get('layout');

    if (!layout) {
      return null;
    }

    if (typeof layout === 'function') {
      return layout;
    }

    if (!layout.type) {
      layout.type = 'dendrogram';
    }

    if (!layout.direction) {
      layout.direction = 'TB';
    }

    if (layout.radial) {
      return function (data) {
        var layoutData = Hierarchy[layout.type](data, layout);
        radialLayout(layoutData);
        return layoutData;
      };
    }

    return function (data) {
      return Hierarchy[layout.type](data, layout);
    };
  };
  /**
   * 返回指定节点在树图数据中的索引
   * @param children 树图数据
   * @param child 树图中某一个 Item 的数据
   */


  TreeGraph.indexOfChild = function (children, id) {
    var index = -1; // eslint-disable-next-line consistent-return

    each(children, function (former, i) {
      if (id === former.id) {
        index = i;
        return false;
      }
    });
    return index;
  };

  TreeGraph.prototype.getDefaultCfg = function () {
    var cfg = _super.prototype.getDefaultCfg.call(this); // 树图默认打开动画


    cfg.animate = true;
    return cfg;
  };
  /**
   * 向🌲树中添加数据
   * @param treeData 树图数据
   * @param parent 父节点实例
   * @param animate 是否开启动画
   */


  TreeGraph.prototype.innerAddChild = function (treeData, parent, animate) {
    var self = this;
    var model = treeData.data;

    if (model) {
      // model 中应存储真实的数据，特别是真实的 children
      model.x = treeData.x;
      model.y = treeData.y;
      model.depth = treeData.depth;
    }

    var node = self.addItem('node', model);

    if (parent) {
      node.set('parent', parent);

      if (animate) {
        var origin_1 = parent.get('originAttrs');

        if (origin_1) {
          node.set('originAttrs', origin_1);
        } else {
          var parentModel = parent.getModel();
          node.set('originAttrs', {
            x: parentModel.x,
            y: parentModel.y
          });
        }
      }

      var childrenList = parent.get('children');

      if (!childrenList) {
        parent.set('children', [node]);
      } else {
        childrenList.push(node);
      }

      self.addItem('edge', {
        source: parent,
        target: node,
        id: parent.get('id') + ":" + node.get('id')
      });
    } // 渲染到视图上应参考布局的children, 避免多绘制了收起的节点


    each(treeData.children || [], function (child) {
      self.innerAddChild(child, node, animate);
    });
    self.emit('afteraddchild', {
      item: node,
      parent: parent
    });
    return node;
  };
  /**
   * 将数据上的变更转换到视图上
   * @param data
   * @param parent
   * @param animate
   */


  TreeGraph.prototype.innerUpdateChild = function (data, parent, animate) {
    var self = this;
    var current = self.findById(data.id); // 若子树不存在，整体添加即可

    if (!current) {
      self.innerAddChild(data, parent, animate);
      return;
    } // 更新新节点下所有子节点


    each(data.children || [], function (child) {
      self.innerUpdateChild(child, current, animate);
    }); // 用现在节点的children实例来删除移除的子节点

    var children = current.get('children');

    if (children) {
      var len = children.length;

      if (len > 0) {
        for (var i = children.length - 1; i >= 0; i--) {
          var child = children[i].getModel();

          if (TreeGraph.indexOfChild(data.children || [], child.id) === -1) {
            self.innerRemoveChild(child.id, {
              x: data.x,
              y: data.y
            }, animate); // 更新父节点下缓存的子节点 item 实例列表

            children.splice(i, 1);
          }
        }
      }
    }

    var oriX;
    var oriY;

    if (current.get('originAttrs')) {
      oriX = current.get('originAttrs').x;
      oriY = current.get('originAttrs').y;
    }

    var model = current.getModel();

    if (animate) {
      // 如果有动画，先缓存节点运动再更新节点
      current.set('originAttrs', {
        x: model.x,
        y: model.y
      });
    }

    current.set('model', data.data);

    if (oriX !== data.x || oriY !== data.y) {
      current.updatePosition({
        x: data.x,
        y: data.y
      });
    }
  };
  /**
   * 删除子节点Item对象
   * @param id
   * @param to
   * @param animate
   */


  TreeGraph.prototype.innerRemoveChild = function (id, to, animate) {
    var self = this;
    var node = self.findById(id);

    if (!node) {
      return;
    }

    each(node.get('children'), function (child) {
      self.innerRemoveChild(child.getModel().id, to, animate);
    });

    if (animate) {
      var model = node.getModel();
      node.set('to', to);
      node.set('originAttrs', {
        x: model.x,
        y: model.y
      });
      self.get('removeList').push(node);
    } else {
      self.removeItem(node);
    }
  };
  /**
   * 更新数据模型，差量更新并重新渲染
   * @param {object} data 数据模型
   */


  TreeGraph.prototype.changeData = function (data) {
    var self = this;

    if (data) {
      self.data(data);
      self.render();
    } else {
      self.layout(this.get('fitView'));
    }
  };
  /**
   * 已更名为 updateLayout，为保持兼容暂且保留。
   * 更改并应用树布局算法
   * @param {object} layout 布局算法
   */


  TreeGraph.prototype.changeLayout = function (layout) {
    // eslint-disable-next-line no-console
    console.warn('Please call updateLayout instead of changeLayout. changeLayout will be discarded soon');
    var self = this;
    self.updateLayout(layout);
  };
  /**
   * 更改并应用树布局算法
   * @param {object} layout 布局算法
   */


  TreeGraph.prototype.updateLayout = function (layout) {
    var self = this;

    if (!layout) {
      // eslint-disable-next-line no-console
      console.warn('layout cannot be null');
      return;
    }

    self.set('layout', layout);
    self.set('layoutMethod', self.getLayout());
    self.layout();
  };
  /**
   * 已更名为 layout，为保持兼容暂且保留。
   * 根据目前的 data 刷新布局，更新到画布上。用于变更数据之后刷新视图。
   * @param {boolean} fitView 更新布局时是否需要适应窗口
   */


  TreeGraph.prototype.refreshLayout = function (fitView) {
    // eslint-disable-next-line no-console
    console.warn('Please call layout instead of refreshLayout. refreshLayout will be discarded soon');
    var self = this;
    self.layout(fitView);
  };
  /**
   * 根据目前的 data 刷新布局，更新到画布上。用于变更数据之后刷新视图。
   * @param {boolean} fitView 更新布局时是否需要适应窗口
   */


  TreeGraph.prototype.layout = function (fitView) {
    var self = this;
    var data = self.get('data');
    var layoutMethod = self.get('layoutMethod');
    var layoutData = layoutMethod(data, self.get('layout'));
    var animate = self.get('animate');
    self.emit('beforerefreshlayout', {
      data: data,
      layoutData: layoutData
    });
    self.emit('beforelayout');
    self.innerUpdateChild(layoutData, undefined, animate);

    if (fitView) {
      var viewController = self.get('viewController');
      viewController.fitView();
    }

    if (!animate) {
      // 如果没有动画，目前仅更新了节点的位置，刷新一下边的样式
      self.refresh();
      self.paint();
    } else {
      self.layoutAnimate(layoutData);
    }

    self.emit('afterrefreshlayout', {
      data: data,
      layoutData: layoutData
    });
    self.emit('afterlayout');
  };
  /**
   * 添加子树到对应 id 的节点
   * @param {TreeGraphData} data 子树数据模型
   * @param {string} parent 子树的父节点id
   */


  TreeGraph.prototype.addChild = function (data, parent) {
    var self = this;
    self.emit('beforeaddchild', {
      model: data,
      parent: parent
    }); // 将数据添加到源数据中，走changeData方法

    if (!isString(parent)) {
      parent = parent.get('id');
    }

    var parentData = self.findDataById(parent);

    if (parentData) {
      if (!parentData.children) {
        parentData.children = [];
      }

      parentData.children.push(data);
      self.changeData();
    }
  };
  /**
   * 更新源数据，差量更新子树
   * @param {TreeGraphData} data 子树数据模型
   * @param {string} parent 子树的父节点id
   */


  TreeGraph.prototype.updateChild = function (data, parent) {
    var self = this; // 如果没有父节点或找不到该节点，是全量的更新，直接重置data

    if (!parent || !self.findById(parent)) {
      self.changeData(data);
      return;
    }

    var parentModel = self.findById(parent).getModel();
    var current = self.findById(data.id);

    if (!parentModel.children) {
      // 当 current 不存在时，children 为空数组
      parentModel.children = [];
    } // 如果不存在该节点，则添加


    if (!current) {
      parentModel.children.push(data);
    } else {
      var index = TreeGraph.indexOfChild(parentModel.children, data.id);
      parentModel.children[index] = data;
    }

    self.changeData();
  };
  /**
   * 删除子树
   * @param {string} id 子树根节点id
   */


  TreeGraph.prototype.removeChild = function (id) {
    var self = this;
    var node = self.findById(id);

    if (!node) {
      return;
    }

    var parent = node.get('parent');

    if (parent && !parent.destroyed) {
      var parentNode = self.findDataById(parent.get('id'));
      var siblings = parentNode && parentNode.children || [];
      var model = node.getModel();
      var index = TreeGraph.indexOfChild(siblings, model.id);
      siblings.splice(index, 1);
    }

    self.changeData();
  };
  /**
   * 根据id获取对应的源数据
   * @param {string} id 元素id
   * @param {TreeGraphData | undefined} parent 从哪个节点开始寻找，为空时从根节点开始查找
   * @return {TreeGraphData} 对应源数据
   */


  TreeGraph.prototype.findDataById = function (id, parent) {
    var self = this;

    if (!parent) {
      parent = self.get('data');
    }

    if (id === parent.id) {
      return parent;
    }

    var result = null; // eslint-disable-next-line consistent-return

    each(parent.children || [], function (child) {
      if (child.id === id) {
        result = child;
        return false;
      }

      result = self.findDataById(id, child);

      if (result) {
        return false;
      }
    });
    return result;
  };
  /**
   * 布局动画接口，用于数据更新时做节点位置更新的动画
   * @param {TreeGraphData} data 更新的数据
   * @param {function} onFrame 定义节点位置更新时如何移动
   */


  TreeGraph.prototype.layoutAnimate = function (data, onFrame) {
    var self = this;
    var animateCfg = this.get('animateCfg');
    self.emit('beforeanimate', {
      data: data
    }); // 如果边中没有指定锚点，但是本身有锚点控制，在动画过程中保持锚点不变

    self.getEdges().forEach(function (edge) {
      var model = edge.get('model');

      if (!model.sourceAnchor) {
        model.sourceAnchor = edge.get('sourceAnchorIndex');
      }
    });
    this.get('canvas').animate(function (ratio) {
      traverseTree(data, function (child) {
        var node = self.findById(child.id); // 只有当存在node的时候才执行

        if (node) {
          var origin_2 = node.get('originAttrs');
          var model = node.get('model');

          if (!origin_2) {
            origin_2 = {
              x: model.x,
              y: model.y
            };
            node.set('originAttrs', origin_2);
          }

          if (onFrame) {
            var attrs = onFrame(node, ratio, origin_2, data);
            node.set('model', Object.assign(model, attrs));
          } else {
            model.x = origin_2.x + (child.x - origin_2.x) * ratio;
            model.y = origin_2.y + (child.y - origin_2.y) * ratio;
          }
        }

        return true;
      });
      each(self.get('removeList'), function (node) {
        var model = node.getModel();
        var from = node.get('originAttrs');
        var to = node.get('to');
        model.x = from.x + (to.x - from.x) * ratio;
        model.y = from.y + (to.y - from.y) * ratio;
      });
      self.refreshPositions();
    }, {
      duration: animateCfg.duration,
      easing: animateCfg.ease,
      callback: function callback() {
        each(self.getNodes(), function (node) {
          node.set('originAttrs', null);
        });
        each(self.get('removeList'), function (node) {
          self.removeItem(node);
        });
        self.set('removeList', []);

        if (animateCfg.callback) {
          animateCfg.callback();
        }

        self.emit('afteranimate', {
          data: data
        });
      },
      delay: animateCfg.delay
    });
  };
  /**
   * 立即停止布局动画
   */


  TreeGraph.prototype.stopLayoutAnimate = function () {
    this.get('canvas').stopAnimate();
    this.emit('layoutanimateend', {
      data: this.get('data')
    });
    this.layoutAnimating = false;
  };
  /**
   * 是否在布局动画
   * @return {boolean} 是否有布局动画
   */


  TreeGraph.prototype.isLayoutAnimating = function () {
    return this.layoutAnimating;
  };
  /**
   * 根据data接口的数据渲染视图
   */


  TreeGraph.prototype.render = function () {
    var self = this;
    var data = self.get('data');

    if (!data) {
      throw new Error('data must be defined first');
    }

    self.clear();
    self.emit('beforerender');
    self.layout(this.get('fitView'));
    self.emit('afterrender');
  };
  /**
   * 导出图数据
   * @return {object} data
   */


  TreeGraph.prototype.save = function () {
    return this.get('data');
  };

  return TreeGraph;
}(Graph);

export default TreeGraph;