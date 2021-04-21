import each from '@antv/util/lib/each';
import isString from '@antv/util/lib/is-string';
var timer = null;

var StateController =
/** @class */
function () {
  function StateController(graph) {
    this.graph = graph;
    /**
     * this.cachedStates = {
     *    enabled: {
     *        hover: [Node]
     *    },
     *     disabled: {}
     *  }
     */

    this.cachedStates = {
      enabled: {},
      disabled: {}
    };
    this.destroyed = false;
  }
  /**
   * 检查 cache 的可用性
   *
   * @private
   * @param {Item} item
   * @param {string} state
   * @param {object} cache
   * @returns
   * @memberof State
   */


  StateController.checkCache = function (item, state, cache) {
    if (!cache[state]) {
      return;
    }

    var index = cache[state].indexOf(item);

    if (index >= 0) {
      cache[state].splice(index, 1);
    }
  };
  /**
   * 缓存 state
   *
   * @private
   * @param {Item} item Item 实例
   * @param {string} state 状态名称
   * @param {object} states
   * @memberof State
   */


  StateController.cacheState = function (item, state, states) {
    if (!states[state]) {
      states[state] = [];
    }

    states[state].push(item);
  };
  /**
   * 更新 Item 的状态
   *
   * @param {Item} item Item实例
   * @param {string} state 状态名称
   * @param {boolean} enabled 状态是否可用
   * @memberof State
   */


  StateController.prototype.updateState = function (item, state, enabled) {
    var _this = this;

    var checkCache = StateController.checkCache,
        cacheState = StateController.cacheState;

    if (item.destroyed) {
      return;
    }

    var cachedStates = this.cachedStates;
    var enabledStates = cachedStates.enabled;
    var disabledStates = cachedStates.disabled;

    if (enabled) {
      checkCache(item, state, disabledStates);
      cacheState(item, state, enabledStates);
    } else {
      checkCache(item, state, enabledStates);
      cacheState(item, state, disabledStates);
    }

    if (timer) {
      clearTimeout(timer);
    }

    timer = setTimeout(function () {
      timer = null;

      _this.updateGraphStates();
    }, 16);
  };
  /**
   * 批量更新 states，兼容 updateState，支持更新一个 state
   *
   * @param {Item} item
   * @param {(string | string[])} states
   * @param {boolean} enabled
   * @memberof State
   */


  StateController.prototype.updateStates = function (item, states, enabled) {
    var _this = this;

    if (isString(states)) {
      this.updateState(item, states, enabled);
    } else {
      states.forEach(function (state) {
        _this.updateState(item, state, enabled);
      });
    }
  };
  /**
   * 更新 states
   *
   * @memberof State
   */


  StateController.prototype.updateGraphStates = function () {
    var states = this.graph.get('states');
    var cachedStates = this.cachedStates;
    each(cachedStates.disabled, function (val, key) {
      if (states[key]) {
        states[key] = states[key].filter(function (item) {
          return val.indexOf(item) < 0 && !val.destroyed;
        });
      }
    });
    each(cachedStates.enabled, function (val, key) {
      if (!states[key]) {
        states[key] = val;
      } else {
        var map_1 = {};
        states[key].forEach(function (item) {
          if (!item.destroyed) {
            map_1[item.get('id')] = true;
          }
        });
        val.forEach(function (item) {
          if (!item.destroyed) {
            var id = item.get('id');

            if (!map_1[id]) {
              map_1[id] = true;
              states[key].push(item);
            }
          }
        });
      }
    });
    this.graph.emit('graphstatechange', {
      states: states
    });
    this.cachedStates = {
      enabled: {},
      disabled: {}
    };
  };

  StateController.prototype.destroy = function () {
    this.graph = null;
    this.cachedStates = null;

    if (timer) {
      clearTimeout(timer);
    }

    timer = null;
    this.destroyed = true;
  };

  return StateController;
}();

export default StateController;