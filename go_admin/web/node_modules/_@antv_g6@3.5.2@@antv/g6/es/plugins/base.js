import deepMix from '@antv/util/lib/deep-mix';
import each from '@antv/util/lib/each';
import wrapBehavior from '@antv/util/lib/wrap-behavior';

var PluginBase =
/** @class */
function () {
  /**
   * 插件基类的构造函数
   * @param cfgs 插件的配置项
   */
  function PluginBase(cfgs) {
    this._cfgs = deepMix(this.getDefaultCfgs(), cfgs);
    this._events = {};
    this.destroyed = false;
  }
  /**
   * 获取默认的插件配置
   */


  PluginBase.prototype.getDefaultCfgs = function () {
    return {};
  };
  /**
   * 初始化插件
   * @param graph Graph 实例
   */


  PluginBase.prototype.initPlugin = function (graph) {
    var self = this;
    self.set('graph', graph);
    var events = self.getEvents();
    var bindEvents = {};
    each(events, function (v, k) {
      var event = wrapBehavior(self, v);
      bindEvents[k] = event;
      graph.on(k, event);
    });
    this._events = bindEvents;
    this.init();
  };
  /**
   * 初始化方法，供子类实现
   */


  PluginBase.prototype.init = function () {};
  /**
   * 获取插件中的事件和事件处理方法，供子类实现
   */


  PluginBase.prototype.getEvents = function () {
    return {};
  };
  /**
   * 获取配置项中的某个值
   * @param key 键值
   */


  PluginBase.prototype.get = function (key) {
    return this._cfgs[key];
  };
  /**
   * 将指定的值存储到 cfgs 中
   * @param key 键值
   * @param val 设置的值
   */


  PluginBase.prototype.set = function (key, val) {
    this._cfgs[key] = val;
  };
  /**
   * 销毁方法，供子类复写
   */


  PluginBase.prototype.destroy = function () {};
  /**
   * 销毁插件
   */


  PluginBase.prototype.destroyPlugin = function () {
    this.destroy();
    var graph = this.get('graph');
    var events = this._events;
    each(events, function (v, k) {
      graph.off(k, v);
    });
    this._events = null;
    this._cfgs = null;
    this.destroyed = true;
  };

  return PluginBase;
}();

export default PluginBase;