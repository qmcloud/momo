"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _tslib = require("tslib");

var _modifyCss = _interopRequireDefault(require("@antv/dom-util/lib/modify-css"));

var _base = _interopRequireDefault(require("../base"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var Menu =
/** @class */
function (_super) {
  (0, _tslib.__extends)(Menu, _super);

  function Menu(cfg) {
    return _super.call(this, cfg) || this;
  }

  Menu.prototype.getDefaultCfgs = function () {
    return {
      createDOM: true,
      container: null,
      className: 'g6-analyzer-menu',
      getContent: undefined,
      // 菜单展示事件
      onShow: function onShow() {
        return true;
      },
      // 菜单隐藏事件
      onHide: function onHide() {
        return true;
      }
    };
  }; // class-methods-use-this


  Menu.prototype.getEvents = function () {
    return {
      contextmenu: 'onMenuShow'
    };
  };

  Menu.prototype.init = function () {
    if (!this.get('createDOM')) {
      return;
    } // 如果指定组件生成 menu 内容,生成菜单 dom


    var menu = document.createElement('div');
    menu.className = this.get('className');
    (0, _modifyCss.default)(menu, {
      visibility: 'hidden'
    });
    var container = this.get('container');

    if (!container) {
      container = this.get('graph').get('container');
    }

    container.appendChild(menu);
    this.set('menu', menu);
  };

  Menu.prototype.onMenuShow = function (e) {
    var self = this; // e.preventDefault()
    // e.stopPropagation()

    var menu = this.get('menu');
    var getContent = this.get('getContent');
    var onShow = this.get('onShow');

    if (getContent) {
      menu.innerHTML = getContent(e);
    }

    if (menu) {
      var graph = this.get('graph');
      var width = graph.get('width');
      var height = graph.get('height');
      var bbox = menu.getBoundingClientRect();
      var x = e.canvasX;
      var y = e.canvasY; // 若菜单超出画布范围，反向

      if (x + bbox.width > width) {
        x = width - bbox.width;
        e.canvasX = x;
      }

      if (y + bbox.height > height) {
        y = height - bbox.height;
        e.canvasY = y;
      }

      if (!onShow || onShow(e) !== false) {
        (0, _modifyCss.default)(menu, {
          top: y,
          left: x,
          visibility: 'visible'
        });
      }
    } else {
      onShow(e);
    }

    var handler = function handler() {
      self.onMenuHide();
    }; // 如果在页面中其他任意地方进行click, 隐去菜单


    document.body.addEventListener('click', handler);
    this.set('handler', handler);
  };

  Menu.prototype.onMenuHide = function () {
    var menu = this.get('menu');
    var hide = this.get('onHide');
    var hasHide = hide();

    if (hasHide) {
      if (menu) {
        (0, _modifyCss.default)(menu, {
          visibility: 'hidden'
        });
      } // 隐藏菜单后需要移除事件监听


      document.body.removeEventListener('click', this.get('handler'));
    }
  };

  Menu.prototype.destroy = function () {
    var menu = this.get('menu');
    var handler = this.get('handler');

    if (menu) {
      menu.parentNode.removeChild(menu);
    }

    if (handler) {
      document.body.removeEventListener('click', handler);
    }
  };

  return Menu;
}(_base.default);

var _default = Menu;
exports.default = _default;