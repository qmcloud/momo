/**
 * @fileOverview layout base file
 * @author shiwu.wyy@antfin.com
 */
import { __extends } from "tslib"; // import augment from '@antv/util/lib/augment';

import each from '@antv/util/lib/each';
import mix from '@antv/util/lib/mix';
/**
 * 基础布局，将被自定义布局所继承
 */

var BaseLayout =
/** @class */
function () {
  function BaseLayout() {
    this.nodes = [];
    this.edges = [];
    this.combos = [];
    this.positions = [];
    this.destroyed = false;
  }

  BaseLayout.prototype.init = function (data) {
    var self = this;
    self.nodes = data.nodes || [];
    self.edges = data.edges || [];
    self.combos = data.combos || [];
  };

  BaseLayout.prototype.execute = function () {};

  BaseLayout.prototype.layout = function (data) {
    var self = this;
    self.init(data);
    self.execute();
  };

  BaseLayout.prototype.getDefaultCfg = function () {
    return {};
  };

  BaseLayout.prototype.updateCfg = function (cfg) {
    var self = this;
    mix(self, cfg);
  };

  BaseLayout.prototype.destroy = function () {
    var self = this;
    self.positions = null;
    self.nodes = null;
    self.edges = null;
    self.destroyed = true;
  };

  return BaseLayout;
}();

export { BaseLayout };
var Layout = {
  /**
   * 注册布局的方法
   * @param {string} type 布局类型，外部引用指定必须，不要与已有布局类型重名
   * @param {object} layout 布局方法
   */
  registerLayout: function registerLayout(type, layout, layoutCons) {
    if (layoutCons === void 0) {
      layoutCons = BaseLayout;
    }

    if (!layout) {
      throw new Error("please specify handler for this layout: " + type);
    } // tslint:disable-next-line: max-classes-per-file


    var GLayout =
    /** @class */
    function (_super) {
      __extends(GLayout, _super);

      function GLayout(cfg) {
        var _this = _super.call(this) || this;

        var self = _this;
        var props = {};
        var defaultCfg = self.getDefaultCfg();
        mix(props, defaultCfg, layout, cfg);
        each(props, function (value, key) {
          self[key] = value;
        });
        return _this;
      }

      return GLayout;
    }(layoutCons);

    Layout[type] = GLayout;
  }
};
export default Layout;