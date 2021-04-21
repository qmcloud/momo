/**
 * @fileOverview random layout
 * @author shiwu.wyy@antfin.com
 */
import { __extends } from "tslib";
import { BaseLayout } from './layout';
/**
 * 随机布局
 */

var RandomLayout =
/** @class */
function (_super) {
  __extends(RandomLayout, _super);

  function RandomLayout() {
    var _this = _super !== null && _super.apply(this, arguments) || this;
    /** 布局中心 */


    _this.center = [0, 0];
    /** 宽度 */

    _this.width = 300;
    /** 高度 */

    _this.height = 300;
    return _this;
  }

  RandomLayout.prototype.getDefaultCfg = function () {
    return {
      center: [0, 0],
      width: 300,
      height: 300
    };
  };
  /**
   * 执行布局
   */


  RandomLayout.prototype.execute = function () {
    var self = this;
    var nodes = self.nodes;
    var layoutScale = 0.9;
    var center = self.center;

    if (!self.width && typeof window !== 'undefined') {
      self.width = window.innerWidth;
    }

    if (!self.height && typeof window !== 'undefined') {
      self.height = window.innerHeight;
    }

    if (nodes) {
      nodes.forEach(function (node) {
        node.x = (Math.random() - 0.5) * layoutScale * self.width + center[0];
        node.y = (Math.random() - 0.5) * layoutScale * self.height + center[1];
      });
    }
  };

  return RandomLayout;
}(BaseLayout);

export default RandomLayout;