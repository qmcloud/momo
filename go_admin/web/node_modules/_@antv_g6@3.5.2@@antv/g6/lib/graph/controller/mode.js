"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _each = _interopRequireDefault(require("@antv/util/lib/each"));

var _isArray = _interopRequireDefault(require("@antv/util/lib/is-array"));

var _isString = _interopRequireDefault(require("@antv/util/lib/is-string"));

var _behavior = _interopRequireDefault(require("../../behavior/behavior"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var ModeController =
/** @class */
function () {
  function ModeController(graph) {
    this.graph = graph;
    this.destroyed = false;
    this.modes = graph.get('modes') || {
      default: []
    };
    this.formatModes();
    this.mode = graph.get('defaultMode') || 'default';
    this.currentBehaves = [];
    this.setMode(this.mode);
  }

  ModeController.prototype.formatModes = function () {
    var modes = this.modes;
    (0, _each.default)(modes, function (mode) {
      (0, _each.default)(mode, function (behavior, i) {
        if ((0, _isString.default)(behavior)) {
          mode[i] = {
            type: behavior
          };
        }
      });
    });
  };

  ModeController.prototype.setBehaviors = function (mode) {
    var graph = this.graph;
    var behaviors = this.modes[mode];
    var behaves = [];
    var behave;
    (0, _each.default)(behaviors || [], function (behavior) {
      var BehaviorInstance = _behavior.default.getBehavior(behavior.type);

      if (!BehaviorInstance) {
        return;
      }

      behave = new BehaviorInstance(behavior);

      if (behave) {
        behave.bind(graph);
        behaves.push(behave);
      }
    });
    this.currentBehaves = behaves;
  };

  ModeController.mergeBehaviors = function (modeBehaviors, behaviors) {
    (0, _each.default)(behaviors, function (behavior) {
      if (modeBehaviors.indexOf(behavior) < 0) {
        if ((0, _isString.default)(behavior)) {
          behavior = {
            type: behavior
          };
        }

        modeBehaviors.push(behavior);
      }
    });
    return modeBehaviors;
  };

  ModeController.filterBehaviors = function (modeBehaviors, behaviors) {
    var result = [];
    modeBehaviors.forEach(function (behavior) {
      var type = '';

      if ((0, _isString.default)(behavior)) {
        type = behavior;
      } else {
        // eslint-disable-next-line prefer-destructuring
        type = behavior.type;
      }

      if (behaviors.indexOf(type) < 0) {
        result.push(behavior);
      }
    });
    return result;
  };

  ModeController.prototype.setMode = function (mode) {
    var _a = this,
        modes = _a.modes,
        graph = _a.graph;

    var current = mode;
    var behaviors = modes[current];

    if (!behaviors) {
      return;
    }

    graph.emit('beforemodechange', {
      mode: mode
    });
    (0, _each.default)(this.currentBehaves, function (behave) {
      behave.unbind(graph);
    });
    this.setBehaviors(current);
    graph.emit('aftermodechange', {
      mode: mode
    });
    this.mode = mode;
  };

  ModeController.prototype.getMode = function () {
    return this.mode;
  };
  /**
   * 动态增加或删除 Behavior
   *
   * @param {ModeType[]} behaviors
   * @param {(ModeType[] | ModeType)} modes
   * @param {boolean} isAdd
   * @returns {Mode}
   * @memberof Mode
   */


  ModeController.prototype.manipulateBehaviors = function (behaviors, modes, isAdd) {
    var _this = this;

    var behaves;

    if (!(0, _isArray.default)(behaviors)) {
      behaves = [behaviors];
    } else {
      behaves = behaviors;
    }

    if ((0, _isArray.default)(modes)) {
      (0, _each.default)(modes, function (mode) {
        if (!_this.modes[mode]) {
          if (isAdd) {
            _this.modes[mode] = behaves;
          }
        } else if (isAdd) {
          _this.modes[mode] = ModeController.mergeBehaviors(_this.modes[mode] || [], behaves);
        } else {
          _this.modes[mode] = ModeController.filterBehaviors(_this.modes[mode] || [], behaves);
        }
      });
      return this;
    }

    var currentMode = modes;

    if (!modes) {
      currentMode = this.mode; // isString(this.mode) ? this.mode : this.mode.type
    }

    if (!this.modes[currentMode]) {
      if (isAdd) {
        this.modes[currentMode] = behaves;
      }
    }

    if (isAdd) {
      this.modes[currentMode] = ModeController.mergeBehaviors(this.modes[currentMode] || [], behaves);
    } else {
      this.modes[currentMode] = ModeController.filterBehaviors(this.modes[currentMode] || [], behaves);
    }

    this.setMode(this.mode);
    return this;
  };

  ModeController.prototype.destroy = function () {
    this.graph = null;
    this.modes = null;
    this.currentBehaves = null;
    this.destroyed = true;
  };

  return ModeController;
}();

var _default = ModeController;
exports.default = _default;