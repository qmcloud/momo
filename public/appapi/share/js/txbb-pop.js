/**
 * Txbb.Pop 组件
 *
 * 同学帮帮弹出层组件
 * 0.4.0
 * by zhangyang
 */
(function (factory) {
  'use strict';

  if (typeof define !== 'undefined' && define.amd) {
    define('Txbb/Pop', function () {
      return factory.call(null);
    });
  } else {
    if (!window.Txbb) window.Txbb = {};
    window.Txbb.Pop = factory.call(null);
  }

}(function () {
  'use strict';

  // helps
  function q(s) {
    return document.querySelector(s);
  }

  function elem(nodeType, attrs) {
    var node = document.createElement(nodeType);
    if (attrs) {
      for (var k in attrs) {
        node[k] = attrs[k];
      }
    }
    return node;
  }

  function extend(origin, extendObj) {
    var back = {};
    if (origin) {
      for (var k1 in origin) {
        back[k1] = origin[k1];
      }
    }
    if (extendObj) {
      for (var k2 in extendObj) {
        back[k2] = extendObj[k2];
      }
    }
    return back;
  }

  Element.prototype._css = function (attrs) {
    if (!this) return this;
    if (attrs) {
      for (var k in attrs) {
        if (this.style.hasOwnProperty(k))
          this.style[k] = attrs[k];
      }
    }
    return this;
  };
  Element.prototype._attr = function (attrs) {
    if (!this) return this;

    if (attrs && typeof attrs === 'object') {
      for (var k in attrs) {
        this.setAttribute(k, attrs[k]);
      }
    }

    if (attrs && typeof attrs === 'string') return this.getAttribute(attrs);

    return this;
  };
  Element.prototype._remove = function () {
    this.parentNode.removeChild(this);
  };
  Element.prototype._show = function () {
    this.style.display = 'block';
  };
  Element.prototype._hide = function () {
    this.style.display = 'none';
  };
  Element.prototype._addEvent = function (eventNames, handler) {
    var events = eventNames.split(/\s+/);
    var _this = this;
    events.forEach(function (evt) {
      _this.addEventListener(evt, handler, false);
    });
    return _this;
  };
  Element.prototype._offset = function () {
    var offset = {
      left: 0,
      top: 0
    };
    var dom = this;
    while (dom && dom !== document.body) {
      offset.left += dom.offsetLeft;
      offset.top += dom.offsetTop;
      dom = dom.offsetParent; // 0.1.1 修复 _offset 方法错误
    }
    return offset;
  };

  /*-------------- 华丽丽的分割线 ---------------*/
  var empty = function () {
  };
  var toastTiming;

  var toastStyleTop ={
  'box-sizing': 'border-box',
  'width': '90%',
  'background-color': 'rgba(0,0,0,0.8)',
  'box-shadow':'0 0 3px #e64a58',
  'color': '#fff',
  'font-size': '16px',
  'border-radius': '50px',
  'position': 'fixed',
  'top':'10px',
  'left': '5%',
  'text-align': 'center',
  'padding': '10px 0',
  'z-index': 26
  };
  var toastStyleCenter ={
  'box-sizing': 'border-box',
  'width': '90%',
  'background-color': 'rgba(0,0,0,0.8)',
  'box-shadow':'0 0 3px #e64a58',
  'color': '#fff',
  'font-size': '16px',
  'border-radius': '50px',
  'position': 'fixed',
  'bottom':'20%',
  'left': '5%',
  'margin-top':'-1.4rem',
  'text-align': 'center',
  'padding': '10px 0',
  'z-index': 26
  };
  var toastStyleBottom ={
  'box-sizing': 'border-box',
  'width': '90%',
  'background-color': 'rgba(0,0,0,0.8)',
  'box-shadow':'0 0 3px #e64a58',
  'color': '#fff',
  'font-size': '16px',
  'border-radius': '50px',
  'position': 'fixed',
  'bottom':'10px',
  'left': '5%',
  'text-align': 'center',
  'padding': '10px 0',
  'z-index': 26
  };
    

  function hideToast() {
    toastTiming = setTimeout(function () {
      q('#J-TxbbToast')._css({
        'opacity': 0,
        '-webkit-transition': 'opacity 1s linear'
      });
      toastTiming = setTimeout(function () {
        q('#J-TxbbToast')._remove();
      }, 1000);
    }, 1000);
  }

  function toast(msg,pos) {
    if (q('#J-TxbbToast')) {
      clearTimeout(toastTiming);
      q('#J-TxbbToast')._css({
        'opacity': 1,
        '-webkit-transition': 'none'
      }).innerHTML = msg;
      hideToast();
      return;
    }

    var div = elem('div', {id: 'J-TxbbToast'});
    if(pos=='top'){
      div._css(toastStyleTop).innerHTML = msg;
    }else if(pos=='center'){
      div._css(toastStyleCenter).innerHTML = msg;
    }else{
      div._css(toastStyleBottom).innerHTML = msg;
    }
    
    document.body.appendChild(div);
    hideToast();
  }
  return function (name, options,pos) {
    if (name === 'toast') {
      toast(options,pos);
      return;
    }
  };
}));
