"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _lib = require("@antv/util/lib");

var _behaviorOption = _interopRequireDefault(require("./behaviorOption"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

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

    var prototype = (0, _lib.clone)(_behaviorOption.default);
    Object.assign(prototype, behavior); // eslint-disable-next-line func-names

    var base = function base(cfg) {
      var _this = this;

      Object.assign(this, this.getDefaultCfg(), cfg);
      var events = this.getEvents();
      this.events = null;
      var eventsToBind = {};

      if (events) {
        (0, _lib.each)(events, function (handle, event) {
          eventsToBind[event] = (0, _lib.wrapBehavior)(_this, handle);
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

var _default = Behavior;
exports.default = _default;