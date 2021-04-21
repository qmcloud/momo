import { clone, each, wrapBehavior } from '@antv/util/lib';
import behaviorOption from './behaviorOption';

var Behavior =
/** @class */
function () {
  function Behavior() {}
  /**
   * 自定义 Behavior
   * @param type Behavior 名称
   * @param behavior Behavior 定义的方法集合
   */


  Behavior.registerBehavior = function (type, behavior) {
    if (!behavior) {
      throw new Error("please specify handler for this behavior: " + type);
    }

    var prototype = clone(behaviorOption);
    Object.assign(prototype, behavior); // eslint-disable-next-line func-names

    var base = function base(cfg) {
      var _this = this;

      Object.assign(this, this.getDefaultCfg(), cfg);
      var events = this.getEvents();
      this.events = null;
      var eventsToBind = {};

      if (events) {
        each(events, function (handle, event) {
          eventsToBind[event] = wrapBehavior(_this, handle);
        });
        this.events = eventsToBind;
      }
    };

    base.prototype = prototype;
    Behavior.types[type] = base;
  };

  Behavior.hasBehavior = function (type) {
    return !!Behavior.types[type];
  };

  Behavior.getBehavior = function (type) {
    return Behavior.types[type];
  }; // 所有自定义的 Behavior 的实例


  Behavior.types = {};
  return Behavior;
}();

export default Behavior;