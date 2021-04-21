(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["Hierarchy"] = factory();
	else
		root["Hierarchy"] = factory();
})(typeof self !== 'undefined' ? self : this, function() {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 31);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_type__ = __webpack_require__(4);

/* harmony default export */ __webpack_exports__["a"] = (function (value) {
  return Array.isArray ? Array.isArray(value) : Object(__WEBPACK_IMPORTED_MODULE_0__is_type__["a" /* default */])(value, 'Array');
});

/***/ }),
/* 1 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var isArrayLike = function (value) {
  /**
   * isArrayLike([1, 2, 3]) => true
   * isArrayLike(document.body.children) => true
   * isArrayLike('abc') => true
   * isArrayLike(Function) => false
   */
  return value !== null && typeof value !== 'function' && isFinite(value.length);
};

/* harmony default export */ __webpack_exports__["a"] = (isArrayLike);

/***/ }),
/* 2 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_object__ = __webpack_require__(13);



function each(elements, func) {
  if (!elements) {
    return;
  }

  var rst;

  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(elements)) {
    for (var i = 0, len = elements.length; i < len; i++) {
      rst = func(elements[i], i);

      if (rst === false) {
        break;
      }
    }
  } else if (Object(__WEBPACK_IMPORTED_MODULE_1__is_object__["a" /* default */])(elements)) {
    for (var k in elements) {
      if (elements.hasOwnProperty(k)) {
        rst = func(elements[k], k);

        if (rst === false) {
          break;
        }
      }
    }
  }
}

/* harmony default export */ __webpack_exports__["a"] = (each);

/***/ }),
/* 3 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_type__ = __webpack_require__(4);
/**
 * 是否为函数
 * @param  {*} fn 对象
 * @return {Boolean}  是否函数
 */

/* harmony default export */ __webpack_exports__["a"] = (function (value) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_type__["a" /* default */])(value, 'Function');
});

/***/ }),
/* 4 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var toString = {}.toString;

var isType = function (value, type) {
  return toString.call(value) === '[object ' + type + ']';
};

/* harmony default export */ __webpack_exports__["a"] = (isType);

/***/ }),
/* 5 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_type__ = __webpack_require__(4);
/**
 * 判断是否数字
 * @return {Boolean} 是否数字
 */


var isNumber = function (value) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_type__["a" /* default */])(value, 'Number');
};

/* harmony default export */ __webpack_exports__["a"] = (isNumber);

/***/ }),
/* 6 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// isFinite,
var isNil = function (value) {
  /**
   * isNil(null) => true
   * isNil() => true
   */
  return value === null || value === undefined;
};

/* harmony default export */ __webpack_exports__["a"] = (isNil);

/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

var _require = __webpack_require__(33),
    mix = _require.mix;

module.exports = {
  assign: mix
};

/***/ }),
/* 8 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_type__ = __webpack_require__(4);

/* harmony default export */ __webpack_exports__["a"] = (function (str) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_type__["a" /* default */])(str, 'String');
});

/***/ }),
/* 9 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_object_like__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_type__ = __webpack_require__(4);



var isPlainObject = function (value) {
  /**
   * isObjectLike(new Foo) => false
   * isObjectLike([1, 2, 3]) => false
   * isObjectLike({ x: 0, y: 0 }) => true
   * isObjectLike(Object.create(null)) => true
   */
  if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_object_like__["a" /* default */])(value) || !Object(__WEBPACK_IMPORTED_MODULE_1__is_type__["a" /* default */])(value, 'Object')) {
    return false;
  }

  if (Object.getPrototypeOf(value) === null) {
    return true;
  }

  var proto = value;

  while (Object.getPrototypeOf(proto) !== null) {
    proto = Object.getPrototypeOf(proto);
  }

  return Object.getPrototypeOf(value) === proto;
};

/* harmony default export */ __webpack_exports__["a"] = (isPlainObject);

/***/ }),
/* 10 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_nil__ = __webpack_require__(6);

/* harmony default export */ __webpack_exports__["a"] = (function (value) {
  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_nil__["a" /* default */])(value)) return '';
  return value.toString();
});

/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

var hierarchy = __webpack_require__(18);

var Layout = /*#__PURE__*/function () {
  function Layout(root, options) {
    if (options === void 0) {
      options = {};
    }

    var me = this;
    me.options = options;
    me.rootNode = hierarchy(root, options);
  }

  var _proto = Layout.prototype;

  _proto.execute = function execute() {
    throw new Error('please override this method');
  };

  return Layout;
}();

module.exports = Layout;

/***/ }),
/* 12 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array_like__ = __webpack_require__(1);


var contains = function (arr, value) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_array_like__["a" /* default */])(arr)) {
    return false;
  }

  return arr.indexOf(value) > -1;
};

/* harmony default export */ __webpack_exports__["a"] = (contains);

/***/ }),
/* 13 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony default export */ __webpack_exports__["a"] = (function (value) {
  /**
   * isObject({}) => true
   * isObject([1, 2, 3]) => true
   * isObject(Function) => true
   * isObject(null) => false
   */
  var type = typeof value;
  return value !== null && type === 'object' || type === 'function';
});

/***/ }),
/* 14 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array_like__ = __webpack_require__(1);



var filter = function (arr, func) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_1__is_array_like__["a" /* default */])(arr)) {
    return arr;
  }

  var result = [];
  Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(arr, function (value, index) {
    if (func(value, index)) {
      result.push(value);
    }
  });
  return result;
};

/* harmony default export */ __webpack_exports__["a"] = (filter);

/***/ }),
/* 15 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var isObjectLike = function (value) {
  /**
   * isObjectLike({}) => true
   * isObjectLike([1, 2, 3]) => true
   * isObjectLike(Function) => false
   * isObjectLike(null) => false
   */
  return typeof value === 'object' && value !== null;
};

/* harmony default export */ __webpack_exports__["a"] = (isObjectLike);

/***/ }),
/* 16 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = mix;
// FIXME: Mutable param should be forbidden in static lang.
function _mix(dist, obj) {
  for (var key in obj) {
    if (obj.hasOwnProperty(key) && key !== 'constructor' && obj[key] !== undefined) {
      dist[key] = obj[key];
    }
  }
}

function mix(dist, src1, src2, src3) {
  if (src1) _mix(dist, src1);
  if (src2) _mix(dist, src2);
  if (src3) _mix(dist, src3);
  return dist;
}

/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

var separateTree = __webpack_require__(30);

var VALID_DIRECTIONS = ['LR', // left to right
'RL', // right to left
'TB', // top to bottom
'BT', // bottom to top
'H', // horizontal
'V' // vertical
];
var HORIZONTAL_DIRECTIONS = ['LR', 'RL', 'H'];

var isHorizontal = function isHorizontal(direction) {
  return HORIZONTAL_DIRECTIONS.indexOf(direction) > -1;
};

var DEFAULT_DIRECTION = VALID_DIRECTIONS[0];

module.exports = function (root, options, layoutAlgrithm) {
  var direction = options.direction || DEFAULT_DIRECTION;
  options.isHorizontal = isHorizontal(direction);

  if (direction && VALID_DIRECTIONS.indexOf(direction) === -1) {
    throw new TypeError("Invalid direction: " + direction);
  }

  if (direction === VALID_DIRECTIONS[0]) {
    // LR
    layoutAlgrithm(root, options);
  } else if (direction === VALID_DIRECTIONS[1]) {
    // RL
    layoutAlgrithm(root, options);
    root.right2left();
  } else if (direction === VALID_DIRECTIONS[2]) {
    // TB
    layoutAlgrithm(root, options);
  } else if (direction === VALID_DIRECTIONS[3]) {
    // BT
    layoutAlgrithm(root, options);
    root.bottom2top();
  } else if (direction === VALID_DIRECTIONS[4] || direction === VALID_DIRECTIONS[5]) {
    // H or V
    // separate into left and right trees
    var _separateTree = separateTree(root, options),
        left = _separateTree.left,
        right = _separateTree.right; // do layout for left and right trees


    layoutAlgrithm(left, options);
    layoutAlgrithm(right, options);
    options.isHorizontal ? left.right2left() : left.bottom2top(); // combine left and right trees

    right.translate(left.x - right.x, left.y - right.y); // translate root

    root.x = left.x;
    root.y = right.y;
    var bb = root.getBoundingBox();

    if (options.isHorizontal) {
      if (bb.top < 0) {
        root.translate(0, -bb.top);
      }
    } else {
      if (bb.left < 0) {
        root.translate(-bb.left, 0);
      }
    }
  } // fixed root position, default value is true


  var fixedRoot = options.fixedRoot;
  if (fixedRoot === undefined) fixedRoot = true;

  if (fixedRoot) {
    root.translate(-(root.x + root.width / 2 + root.hgap), -(root.y + root.height / 2 + root.vgap));
  }

  return root;
};

/***/ }),
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

/* eslint-disable no-cond-assign */
var util = __webpack_require__(7);

var PEM = 18;
var DEFAULT_HEIGHT = PEM * 2;
var DEFAULT_GAP = PEM;
var DEFAULT_OPTIONS = {
  getId: function getId(d) {
    return d.id || d.name;
  },
  getPreH: function getPreH(d) {
    return d.preH || 0;
  },
  getPreV: function getPreV(d) {
    return d.preV || 0;
  },
  getHGap: function getHGap(d) {
    return d.hgap || DEFAULT_GAP;
  },
  getVGap: function getVGap(d) {
    return d.vgap || DEFAULT_GAP;
  },
  getChildren: function getChildren(d) {
    return d.children;
  },
  getHeight: function getHeight(d) {
    return d.height || DEFAULT_HEIGHT;
  },
  getWidth: function getWidth(d) {
    var label = d.label || ' ';
    return d.width || label.split('').length * PEM; // FIXME DO NOT get width like this
  }
};

function Node(data, options) {
  var me = this;
  me.vgap = me.hgap = 0;
  if (data instanceof Node) return data;
  me.data = data;
  /*
   * Gaps: filling space between nodes
   * (x, y) ----------------------
   * |            vgap            |
   * |    --------------------    h
   * | h |                    |   e
   * | g |                    |   i
   * | a |                    |   g
   * | p |                    |   h
   * |   ---------------------    t
   * |                            |
   *  -----------width------------
   */

  var hgap = options.getHGap(data);
  var vgap = options.getVGap(data);
  me.preH = options.getPreH(data);
  me.preV = options.getPreV(data);
  me.width = options.getWidth(data);
  me.height = options.getHeight(data);
  me.width += me.preH;
  me.height += me.preV;
  me.id = options.getId(data);
  me.x = me.y = 0;
  me.depth = 0;

  if (!me.children) {
    me.children = [];
  }

  me.addGap(hgap, vgap);
  return me;
}

util.assign(Node.prototype, {
  isRoot: function isRoot() {
    return this.depth === 0;
  },
  isLeaf: function isLeaf() {
    return this.children.length === 0;
  },
  addGap: function addGap(hgap, vgap) {
    var me = this;
    me.hgap += hgap;
    me.vgap += vgap;
    me.width += 2 * hgap;
    me.height += 2 * vgap;
  },
  eachNode: function eachNode(callback) {
    // Depth First traverse
    var me = this;
    var nodes = [me];
    var current;

    while (current = nodes.shift()) {
      callback(current);
      nodes = current.children.concat(nodes);
    }
  },
  DFTraverse: function DFTraverse(callback) {
    // Depth First traverse
    this.eachNode(callback);
  },
  BFTraverse: function BFTraverse(callback) {
    // Breadth First traverse
    var me = this;
    var nodes = [me];
    var current;

    while (current = nodes.shift()) {
      callback(current);
      nodes = nodes.concat(current.children);
    }
  },
  getBoundingBox: function getBoundingBox() {
    // BBox for just one tree node
    var bb = {
      left: Number.MAX_VALUE,
      top: Number.MAX_VALUE,
      width: 0,
      height: 0
    };
    this.eachNode(function (node) {
      bb.left = Math.min(bb.left, node.x);
      bb.top = Math.min(bb.top, node.y);
      bb.width = Math.max(bb.width, node.x + node.width);
      bb.height = Math.max(bb.height, node.y + node.height);
    });
    return bb;
  },
  // translate
  translate: function translate(tx, ty) {
    if (tx === void 0) {
      tx = 0;
    }

    if (ty === void 0) {
      ty = 0;
    }

    this.eachNode(function (node) {
      node.x += tx;
      node.y += ty;
      node.x += node.preH;
      node.y += node.preV;
    });
  },
  right2left: function right2left() {
    var me = this;
    var bb = me.getBoundingBox();
    me.eachNode(function (node) {
      node.x = node.x - (node.x - bb.left) * 2 - node.width; // node.x = - node.x;
    });
    me.translate(bb.width, 0);
  },
  bottom2top: function bottom2top() {
    var me = this;
    var bb = me.getBoundingBox();
    me.eachNode(function (node) {
      node.y = node.y - (node.y - bb.top) * 2 - node.height; // node.y = - node.y;
    });
    me.translate(0, bb.height);
  }
});

function hierarchy(data, options, isolated) {
  if (options === void 0) {
    options = {};
  }

  options = util.assign({}, DEFAULT_OPTIONS, options);
  var root = new Node(data, options);
  var nodes = [root];
  var node;

  if (!isolated && !data.collapsed) {
    while (node = nodes.shift()) {
      if (!node.data.collapsed) {
        var children = options.getChildren(node.data);
        var length = children ? children.length : 0;
        node.children = new Array(length);

        if (children && length) {
          for (var i = 0; i < length; i++) {
            var child = new Node(children[i], options);
            node.children[i] = child;
            nodes.push(child);
            child.parent = node;
            child.depth = node.depth + 1;
          }
        }
      }
    }
  }

  return root;
}

module.exports = hierarchy;

/***/ }),
/* 19 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_nil__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__keys__ = __webpack_require__(20);



function isMatch(obj, attrs) {
  var _keys = Object(__WEBPACK_IMPORTED_MODULE_1__keys__["a" /* default */])(attrs);

  var length = _keys.length;
  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_nil__["a" /* default */])(obj)) return !length;

  for (var i = 0; i < length; i += 1) {
    var key = _keys[i];

    if (attrs[key] !== obj[key] || !(key in obj)) {
      return false;
    }
  }

  return true;
}

/* harmony default export */ __webpack_exports__["a"] = (isMatch);

/***/ }),
/* 20 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_function__ = __webpack_require__(3);


var keys = Object.keys ? function (obj) {
  return Object.keys(obj);
} : function (obj) {
  var result = [];
  Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(obj, function (value, key) {
    if (!(Object(__WEBPACK_IMPORTED_MODULE_1__is_function__["a" /* default */])(obj) && key === 'prototype')) {
      result.push(key);
    }
  });
  return result;
};
/* harmony default export */ __webpack_exports__["a"] = (keys);

/***/ }),
/* 21 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array_like__ = __webpack_require__(1);

var splice = Array.prototype.splice;

var pullAt = function pullAt(arr, indexes) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_array_like__["a" /* default */])(arr)) {
    return [];
  }

  var length = arr ? indexes.length : 0;
  var last = length - 1;

  while (length--) {
    var previous = void 0;
    var index = indexes[length];

    if (length === last || index !== previous) {
      previous = index;
      splice.call(arr, index, 1);
    }
  }

  return arr;
};

/* harmony default export */ __webpack_exports__["a"] = (pullAt);

/***/ }),
/* 22 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__contains__ = __webpack_require__(12);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__each__ = __webpack_require__(2);



var uniq = function (arr) {
  var resultArr = [];
  Object(__WEBPACK_IMPORTED_MODULE_1__each__["a" /* default */])(arr, function (item) {
    if (!Object(__WEBPACK_IMPORTED_MODULE_0__contains__["a" /* default */])(resultArr, item)) {
      resultArr.push(item);
    }
  });
  return resultArr;
};

/* harmony default export */ __webpack_exports__["a"] = (uniq);

/***/ }),
/* 23 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_function__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__group_by__ = __webpack_require__(24);




var groupToMap = function (data, condition) {
  if (!condition) {
    return {
      0: data
    };
  }

  if (!Object(__WEBPACK_IMPORTED_MODULE_1__is_function__["a" /* default */])(condition)) {
    var paramsCondition_1 = Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(condition) ? condition : condition.replace(/\s+/g, '').split('*');

    condition = function (row) {
      var unique = '_'; // 避免出现数字作为Key的情况，会进行按照数字的排序

      for (var i = 0, l = paramsCondition_1.length; i < l; i++) {
        unique += row[paramsCondition_1[i]] && row[paramsCondition_1[i]].toString();
      }

      return unique;
    };
  }

  var groups = Object(__WEBPACK_IMPORTED_MODULE_2__group_by__["a" /* default */])(data, condition);
  return groups;
};

/* harmony default export */ __webpack_exports__["a"] = (groupToMap);

/***/ }),
/* 24 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__is_function__ = __webpack_require__(3);



var hasOwnProperty = Object.prototype.hasOwnProperty;

function groupBy(data, condition) {
  if (!condition || !Object(__WEBPACK_IMPORTED_MODULE_1__is_array__["a" /* default */])(data)) {
    return {};
  }

  var result = {}; // 兼容方法和 字符串的写法

  var predicate = Object(__WEBPACK_IMPORTED_MODULE_2__is_function__["a" /* default */])(condition) ? condition : function (item) {
    return item[condition];
  };
  var key;
  Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(data, function (item) {
    key = predicate(item);

    if (hasOwnProperty.call(result, key)) {
      result[key].push(item);
    } else {
      result[key] = [item];
    }
  });
  return result;
}

/* harmony default export */ __webpack_exports__["a"] = (groupBy);

/***/ }),
/* 25 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony default export */ __webpack_exports__["a"] = (function (obj, key) {
  return obj.hasOwnProperty(key);
});

/***/ }),
/* 26 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_function__ = __webpack_require__(3);

 // @ts-ignore

var values = Object.values ? function (obj) {
  return Object.values(obj);
} : function (obj) {
  var result = [];
  Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(obj, function (value, key) {
    if (!(Object(__WEBPACK_IMPORTED_MODULE_1__is_function__["a" /* default */])(obj) && key === 'prototype')) {
      result.push(value);
    }
  });
  return result;
};
/* harmony default export */ __webpack_exports__["a"] = (values);

/***/ }),
/* 27 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var toString = {}.toString;

var getType = function (value) {
  return toString.call(value).replace(/^\[object /, '').replace(/]$/, '');
};

/* harmony default export */ __webpack_exports__["a"] = (getType);

/***/ }),
/* 28 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var objectProto = Object.prototype;

var isPrototype = function (value) {
  var Ctor = value && value.constructor;
  var proto = typeof Ctor === 'function' && Ctor.prototype || objectProto;
  return value === proto;
};

/* harmony default export */ __webpack_exports__["a"] = (isPrototype);

/***/ }),
/* 29 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_object_like__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array_like__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__is_string__ = __webpack_require__(8);




var isEqual = function (value, other) {
  if (value === other) {
    return true;
  }

  if (!value || !other) {
    return false;
  }

  if (Object(__WEBPACK_IMPORTED_MODULE_2__is_string__["a" /* default */])(value) || Object(__WEBPACK_IMPORTED_MODULE_2__is_string__["a" /* default */])(other)) {
    return false;
  }

  if (Object(__WEBPACK_IMPORTED_MODULE_1__is_array_like__["a" /* default */])(value) || Object(__WEBPACK_IMPORTED_MODULE_1__is_array_like__["a" /* default */])(other)) {
    if (value.length !== other.length) {
      return false;
    }

    var rst = true;

    for (var i = 0; i < value.length; i++) {
      rst = isEqual(value[i], other[i]);

      if (!rst) {
        break;
      }
    }

    return rst;
  }

  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_object_like__["a" /* default */])(value) || Object(__WEBPACK_IMPORTED_MODULE_0__is_object_like__["a" /* default */])(other)) {
    var valueKeys = Object.keys(value);
    var otherKeys = Object.keys(other);

    if (valueKeys.length !== otherKeys.length) {
      return false;
    }

    var rst = true;

    for (var i = 0; i < valueKeys.length; i++) {
      rst = isEqual(value[valueKeys[i]], other[valueKeys[i]]);

      if (!rst) {
        break;
      }
    }

    return rst;
  }

  return false;
};

/* harmony default export */ __webpack_exports__["a"] = (isEqual);

/***/ }),
/* 30 */
/***/ (function(module, exports, __webpack_require__) {

var hierarchy = __webpack_require__(18);

module.exports = function (root, options) {
  // separate into left and right trees
  var left = hierarchy(root.data, options, true); // root only

  var right = hierarchy(root.data, options, true); // root only
  // automatically

  var treeSize = root.children.length;
  var rightTreeSize = Math.round(treeSize / 2); // separate left and right tree by meta data

  var getSide = options.getSide || function (child, index) {
    if (index < rightTreeSize) {
      return 'right';
    }

    return 'left';
  };

  for (var i = 0; i < treeSize; i++) {
    var child = root.children[i];
    var side = getSide(child, i);

    if (side === 'right') {
      right.children.push(child);
    } else {
      left.children.push(child);
    }
  }

  left.eachNode(function (node) {
    if (!node.isRoot()) {
      node.side = 'left';
    }
  });
  right.eachNode(function (node) {
    if (!node.isRoot()) {
      node.side = 'right';
    }
  });
  return {
    left: left,
    right: right
  };
};

/***/ }),
/* 31 */
/***/ (function(module, exports, __webpack_require__) {

var hierarchy = {
  compactBox: __webpack_require__(32),
  dendrogram: __webpack_require__(114),
  indented: __webpack_require__(116),
  mindmap: __webpack_require__(118)
};
module.exports = hierarchy;

/***/ }),
/* 32 */
/***/ (function(module, exports, __webpack_require__) {

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; subClass.__proto__ = superClass; }

var TreeLayout = __webpack_require__(11);

var nonLayeredTidyTree = __webpack_require__(113);

var doTreeLayout = __webpack_require__(17);

var util = __webpack_require__(7);

var CompactBoxTreeLayout = /*#__PURE__*/function (_TreeLayout) {
  _inheritsLoose(CompactBoxTreeLayout, _TreeLayout);

  function CompactBoxTreeLayout() {
    return _TreeLayout.apply(this, arguments) || this;
  }

  var _proto = CompactBoxTreeLayout.prototype;

  _proto.execute = function execute() {
    var me = this;
    return doTreeLayout(me.rootNode, me.options, nonLayeredTidyTree);
  };

  return CompactBoxTreeLayout;
}(TreeLayout);

var DEFAULT_OPTIONS = {};

function compactBoxLayout(root, options) {
  options = util.assign({}, DEFAULT_OPTIONS, options);
  return new CompactBoxTreeLayout(root, options).execute();
}

module.exports = compactBoxLayout;

/***/ }),
/* 33 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__contains__ = __webpack_require__(12);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "contains", function() { return __WEBPACK_IMPORTED_MODULE_0__contains__["a"]; });
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "includes", function() { return __WEBPACK_IMPORTED_MODULE_0__contains__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__difference__ = __webpack_require__(34);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "difference", function() { return __WEBPACK_IMPORTED_MODULE_1__difference__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__find__ = __webpack_require__(35);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "find", function() { return __WEBPACK_IMPORTED_MODULE_2__find__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__find_index__ = __webpack_require__(36);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "findIndex", function() { return __WEBPACK_IMPORTED_MODULE_3__find_index__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__first_value__ = __webpack_require__(37);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "firstValue", function() { return __WEBPACK_IMPORTED_MODULE_4__first_value__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__flatten__ = __webpack_require__(38);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "flatten", function() { return __WEBPACK_IMPORTED_MODULE_5__flatten__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__flatten_deep__ = __webpack_require__(39);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "flattenDeep", function() { return __WEBPACK_IMPORTED_MODULE_6__flatten_deep__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7__get_range__ = __webpack_require__(40);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "getRange", function() { return __WEBPACK_IMPORTED_MODULE_7__get_range__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8__pull__ = __webpack_require__(41);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "pull", function() { return __WEBPACK_IMPORTED_MODULE_8__pull__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_9__pull_at__ = __webpack_require__(21);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "pullAt", function() { return __WEBPACK_IMPORTED_MODULE_9__pull_at__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_10__reduce__ = __webpack_require__(42);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "reduce", function() { return __WEBPACK_IMPORTED_MODULE_10__reduce__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_11__remove__ = __webpack_require__(43);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "remove", function() { return __WEBPACK_IMPORTED_MODULE_11__remove__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_12__sort_by__ = __webpack_require__(44);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "sortBy", function() { return __WEBPACK_IMPORTED_MODULE_12__sort_by__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_13__union__ = __webpack_require__(45);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "union", function() { return __WEBPACK_IMPORTED_MODULE_13__union__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_14__uniq__ = __webpack_require__(22);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "uniq", function() { return __WEBPACK_IMPORTED_MODULE_14__uniq__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_15__values_of_key__ = __webpack_require__(46);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "valuesOfKey", function() { return __WEBPACK_IMPORTED_MODULE_15__values_of_key__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_16__head__ = __webpack_require__(47);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "head", function() { return __WEBPACK_IMPORTED_MODULE_16__head__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_17__last__ = __webpack_require__(48);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "last", function() { return __WEBPACK_IMPORTED_MODULE_17__last__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_18__starts_with__ = __webpack_require__(49);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "startsWith", function() { return __WEBPACK_IMPORTED_MODULE_18__starts_with__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_19__ends_with__ = __webpack_require__(50);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "endsWith", function() { return __WEBPACK_IMPORTED_MODULE_19__ends_with__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_20__filter__ = __webpack_require__(14);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "filter", function() { return __WEBPACK_IMPORTED_MODULE_20__filter__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_21__every__ = __webpack_require__(51);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "every", function() { return __WEBPACK_IMPORTED_MODULE_21__every__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_22__some__ = __webpack_require__(52);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "some", function() { return __WEBPACK_IMPORTED_MODULE_22__some__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_23__group__ = __webpack_require__(53);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "group", function() { return __WEBPACK_IMPORTED_MODULE_23__group__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_24__group_by__ = __webpack_require__(24);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "groupBy", function() { return __WEBPACK_IMPORTED_MODULE_24__group_by__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_25__group_to_map__ = __webpack_require__(23);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "groupToMap", function() { return __WEBPACK_IMPORTED_MODULE_25__group_to_map__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_26__get_wrap_behavior__ = __webpack_require__(54);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "getWrapBehavior", function() { return __WEBPACK_IMPORTED_MODULE_26__get_wrap_behavior__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_27__wrap_behavior__ = __webpack_require__(55);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "wrapBehavior", function() { return __WEBPACK_IMPORTED_MODULE_27__wrap_behavior__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_28__number2color__ = __webpack_require__(56);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "number2color", function() { return __WEBPACK_IMPORTED_MODULE_28__number2color__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_29__parse_radius__ = __webpack_require__(57);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "parseRadius", function() { return __WEBPACK_IMPORTED_MODULE_29__parse_radius__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_30__clamp__ = __webpack_require__(58);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "clamp", function() { return __WEBPACK_IMPORTED_MODULE_30__clamp__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_31__fixed_base__ = __webpack_require__(59);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "fixedBase", function() { return __WEBPACK_IMPORTED_MODULE_31__fixed_base__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_32__is_decimal__ = __webpack_require__(60);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isDecimal", function() { return __WEBPACK_IMPORTED_MODULE_32__is_decimal__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_33__is_even__ = __webpack_require__(61);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isEven", function() { return __WEBPACK_IMPORTED_MODULE_33__is_even__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_34__is_integer__ = __webpack_require__(62);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isInteger", function() { return __WEBPACK_IMPORTED_MODULE_34__is_integer__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_35__is_negative__ = __webpack_require__(63);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isNegative", function() { return __WEBPACK_IMPORTED_MODULE_35__is_negative__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_36__is_number_equal__ = __webpack_require__(64);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isNumberEqual", function() { return __WEBPACK_IMPORTED_MODULE_36__is_number_equal__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_37__is_odd__ = __webpack_require__(65);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isOdd", function() { return __WEBPACK_IMPORTED_MODULE_37__is_odd__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_38__is_positive__ = __webpack_require__(66);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isPositive", function() { return __WEBPACK_IMPORTED_MODULE_38__is_positive__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_39__max_by__ = __webpack_require__(67);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "maxBy", function() { return __WEBPACK_IMPORTED_MODULE_39__max_by__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_40__min_by__ = __webpack_require__(68);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "minBy", function() { return __WEBPACK_IMPORTED_MODULE_40__min_by__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_41__mod__ = __webpack_require__(69);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "mod", function() { return __WEBPACK_IMPORTED_MODULE_41__mod__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_42__to_degree__ = __webpack_require__(70);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "toDegree", function() { return __WEBPACK_IMPORTED_MODULE_42__to_degree__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_43__to_integer__ = __webpack_require__(71);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "toInteger", function() { return __WEBPACK_IMPORTED_MODULE_43__to_integer__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_44__to_radian__ = __webpack_require__(72);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "toRadian", function() { return __WEBPACK_IMPORTED_MODULE_44__to_radian__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_45__for_in__ = __webpack_require__(73);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "forIn", function() { return __WEBPACK_IMPORTED_MODULE_45__for_in__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_46__has__ = __webpack_require__(25);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "has", function() { return __WEBPACK_IMPORTED_MODULE_46__has__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_47__has_key__ = __webpack_require__(74);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "hasKey", function() { return __WEBPACK_IMPORTED_MODULE_47__has_key__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_48__has_value__ = __webpack_require__(75);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "hasValue", function() { return __WEBPACK_IMPORTED_MODULE_48__has_value__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_49__keys__ = __webpack_require__(20);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "keys", function() { return __WEBPACK_IMPORTED_MODULE_49__keys__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_50__is_match__ = __webpack_require__(19);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isMatch", function() { return __WEBPACK_IMPORTED_MODULE_50__is_match__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_51__values__ = __webpack_require__(26);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "values", function() { return __WEBPACK_IMPORTED_MODULE_51__values__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_52__lower_case__ = __webpack_require__(76);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "lowerCase", function() { return __WEBPACK_IMPORTED_MODULE_52__lower_case__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_53__lower_first__ = __webpack_require__(77);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "lowerFirst", function() { return __WEBPACK_IMPORTED_MODULE_53__lower_first__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_54__substitute__ = __webpack_require__(78);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "substitute", function() { return __WEBPACK_IMPORTED_MODULE_54__substitute__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_55__upper_case__ = __webpack_require__(79);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "upperCase", function() { return __WEBPACK_IMPORTED_MODULE_55__upper_case__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_56__upper_first__ = __webpack_require__(80);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "upperFirst", function() { return __WEBPACK_IMPORTED_MODULE_56__upper_first__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_57__get_type__ = __webpack_require__(27);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "getType", function() { return __WEBPACK_IMPORTED_MODULE_57__get_type__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_58__is_arguments__ = __webpack_require__(81);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isArguments", function() { return __WEBPACK_IMPORTED_MODULE_58__is_arguments__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_59__is_array__ = __webpack_require__(0);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isArray", function() { return __WEBPACK_IMPORTED_MODULE_59__is_array__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_60__is_array_like__ = __webpack_require__(1);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isArrayLike", function() { return __WEBPACK_IMPORTED_MODULE_60__is_array_like__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_61__is_boolean__ = __webpack_require__(82);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isBoolean", function() { return __WEBPACK_IMPORTED_MODULE_61__is_boolean__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_62__is_date__ = __webpack_require__(83);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isDate", function() { return __WEBPACK_IMPORTED_MODULE_62__is_date__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_63__is_error__ = __webpack_require__(84);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isError", function() { return __WEBPACK_IMPORTED_MODULE_63__is_error__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_64__is_function__ = __webpack_require__(3);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isFunction", function() { return __WEBPACK_IMPORTED_MODULE_64__is_function__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_65__is_finite__ = __webpack_require__(85);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isFinite", function() { return __WEBPACK_IMPORTED_MODULE_65__is_finite__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_66__is_nil__ = __webpack_require__(6);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isNil", function() { return __WEBPACK_IMPORTED_MODULE_66__is_nil__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_67__is_null__ = __webpack_require__(86);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isNull", function() { return __WEBPACK_IMPORTED_MODULE_67__is_null__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_68__is_number__ = __webpack_require__(5);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isNumber", function() { return __WEBPACK_IMPORTED_MODULE_68__is_number__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_69__is_object__ = __webpack_require__(13);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isObject", function() { return __WEBPACK_IMPORTED_MODULE_69__is_object__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_70__is_object_like__ = __webpack_require__(15);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isObjectLike", function() { return __WEBPACK_IMPORTED_MODULE_70__is_object_like__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_71__is_plain_object__ = __webpack_require__(9);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isPlainObject", function() { return __WEBPACK_IMPORTED_MODULE_71__is_plain_object__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_72__is_prototype__ = __webpack_require__(28);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isPrototype", function() { return __WEBPACK_IMPORTED_MODULE_72__is_prototype__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_73__is_reg_exp__ = __webpack_require__(87);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isRegExp", function() { return __WEBPACK_IMPORTED_MODULE_73__is_reg_exp__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_74__is_string__ = __webpack_require__(8);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isString", function() { return __WEBPACK_IMPORTED_MODULE_74__is_string__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_75__is_type__ = __webpack_require__(4);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isType", function() { return __WEBPACK_IMPORTED_MODULE_75__is_type__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_76__is_undefined__ = __webpack_require__(88);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isUndefined", function() { return __WEBPACK_IMPORTED_MODULE_76__is_undefined__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_77__is_element__ = __webpack_require__(89);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isElement", function() { return __WEBPACK_IMPORTED_MODULE_77__is_element__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_78__request_animation_frame__ = __webpack_require__(90);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "requestAnimationFrame", function() { return __WEBPACK_IMPORTED_MODULE_78__request_animation_frame__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_79__clear_animation_frame__ = __webpack_require__(91);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "clearAnimationFrame", function() { return __WEBPACK_IMPORTED_MODULE_79__clear_animation_frame__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_80__augment__ = __webpack_require__(92);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "augment", function() { return __WEBPACK_IMPORTED_MODULE_80__augment__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_81__clone__ = __webpack_require__(93);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "clone", function() { return __WEBPACK_IMPORTED_MODULE_81__clone__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_82__debounce__ = __webpack_require__(94);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "debounce", function() { return __WEBPACK_IMPORTED_MODULE_82__debounce__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_83__memoize__ = __webpack_require__(95);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "memoize", function() { return __WEBPACK_IMPORTED_MODULE_83__memoize__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_84__deep_mix__ = __webpack_require__(96);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "deepMix", function() { return __WEBPACK_IMPORTED_MODULE_84__deep_mix__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_85__each__ = __webpack_require__(2);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "each", function() { return __WEBPACK_IMPORTED_MODULE_85__each__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_86__extend__ = __webpack_require__(97);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "extend", function() { return __WEBPACK_IMPORTED_MODULE_86__extend__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_87__index_of__ = __webpack_require__(98);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "indexOf", function() { return __WEBPACK_IMPORTED_MODULE_87__index_of__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_88__is_empty__ = __webpack_require__(99);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isEmpty", function() { return __WEBPACK_IMPORTED_MODULE_88__is_empty__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_89__is_equal__ = __webpack_require__(29);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isEqual", function() { return __WEBPACK_IMPORTED_MODULE_89__is_equal__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_90__is_equal_with__ = __webpack_require__(100);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "isEqualWith", function() { return __WEBPACK_IMPORTED_MODULE_90__is_equal_with__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_91__map__ = __webpack_require__(101);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "map", function() { return __WEBPACK_IMPORTED_MODULE_91__map__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_92__map_values__ = __webpack_require__(102);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "mapValues", function() { return __WEBPACK_IMPORTED_MODULE_92__map_values__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_93__mix__ = __webpack_require__(16);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "mix", function() { return __WEBPACK_IMPORTED_MODULE_93__mix__["a"]; });
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "assign", function() { return __WEBPACK_IMPORTED_MODULE_93__mix__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_94__get__ = __webpack_require__(103);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "get", function() { return __WEBPACK_IMPORTED_MODULE_94__get__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_95__set__ = __webpack_require__(104);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "set", function() { return __WEBPACK_IMPORTED_MODULE_95__set__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_96__pick__ = __webpack_require__(105);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "pick", function() { return __WEBPACK_IMPORTED_MODULE_96__pick__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_97__throttle__ = __webpack_require__(106);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "throttle", function() { return __WEBPACK_IMPORTED_MODULE_97__throttle__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_98__to_array__ = __webpack_require__(107);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "toArray", function() { return __WEBPACK_IMPORTED_MODULE_98__to_array__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_99__to_string__ = __webpack_require__(10);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "toString", function() { return __WEBPACK_IMPORTED_MODULE_99__to_string__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_100__unique_id__ = __webpack_require__(108);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "uniqueId", function() { return __WEBPACK_IMPORTED_MODULE_100__unique_id__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_101__noop__ = __webpack_require__(109);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "noop", function() { return __WEBPACK_IMPORTED_MODULE_101__noop__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_102__identity__ = __webpack_require__(110);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "identity", function() { return __WEBPACK_IMPORTED_MODULE_102__identity__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_103__size__ = __webpack_require__(111);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "size", function() { return __WEBPACK_IMPORTED_MODULE_103__size__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_104__cache__ = __webpack_require__(112);
/* harmony reexport (binding) */ __webpack_require__.d(__webpack_exports__, "Cache", function() { return __WEBPACK_IMPORTED_MODULE_104__cache__["a"]; });
// array

























 // event


 // format


 // math















 // object







 // string





 // type























 // other
























 // 不知道为什么，需要把这个 export，不然 ts 会报类型错误



/***/ }),
/* 34 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__filter__ = __webpack_require__(14);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__contains__ = __webpack_require__(12);


/**
 * Flattens `array` a single level deep.
 *
 * @param {Array} arr The array to inspect.
 * @param {Array} values The values to exclude.
 * @return {Array} Returns the new array of filtered values.
 * @example
 * difference([2, 1], [2, 3]);  // => [1]
 */

var difference = function (arr, values) {
  if (values === void 0) {
    values = [];
  }

  return Object(__WEBPACK_IMPORTED_MODULE_0__filter__["a" /* default */])(arr, function (value) {
    return !Object(__WEBPACK_IMPORTED_MODULE_1__contains__["a" /* default */])(values, value);
  });
};

/* harmony default export */ __webpack_exports__["a"] = (difference);

/***/ }),
/* 35 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_function__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_match__ = __webpack_require__(19);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__is_plain_object__ = __webpack_require__(9);





function find(arr, predicate) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_2__is_array__["a" /* default */])(arr)) return null;

  var _predicate;

  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_function__["a" /* default */])(predicate)) {
    _predicate = predicate;
  }

  if (Object(__WEBPACK_IMPORTED_MODULE_3__is_plain_object__["a" /* default */])(predicate)) {
    _predicate = function (a) {
      return Object(__WEBPACK_IMPORTED_MODULE_1__is_match__["a" /* default */])(a, predicate);
    };
  }

  if (_predicate) {
    for (var i = 0; i < arr.length; i += 1) {
      if (_predicate(arr[i])) {
        return arr[i];
      }
    }
  }

  return null;
}

/* harmony default export */ __webpack_exports__["a"] = (find);

/***/ }),
/* 36 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
function findIndex(arr, predicate, fromIndex) {
  if (fromIndex === void 0) {
    fromIndex = 0;
  }

  for (var i = fromIndex; i < arr.length; i++) {
    if (predicate(arr[i], i)) {
      // 找到终止循环
      return i;
    }
  }

  return -1;
}

/* harmony default export */ __webpack_exports__["a"] = (findIndex);

/***/ }),
/* 37 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_nil__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array__ = __webpack_require__(0);



var firstValue = function (data, name) {
  var rst = null;

  for (var i = 0; i < data.length; i++) {
    var obj = data[i];
    var value = obj[name];

    if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_nil__["a" /* default */])(value)) {
      if (Object(__WEBPACK_IMPORTED_MODULE_1__is_array__["a" /* default */])(value)) {
        rst = value[0]; // todo 这里是否应该使用递归，调用 firstValue @绝云
      } else {
        rst = value;
      }

      break;
    }
  }

  return rst;
};

/* harmony default export */ __webpack_exports__["a"] = (firstValue);

/***/ }),
/* 38 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);

/**
 * Flattens `array` a single level deep.
 *
 * @param {Array} arr The array to flatten.
 * @return {Array} Returns the new flattened array.
 * @example
 *
 * flatten([1, [2, [3, [4]], 5]]);  // => [1, 2, [3, [4]], 5]
 */

var flatten = function (arr) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(arr)) {
    return [];
  }

  var rst = [];

  for (var i = 0; i < arr.length; i++) {
    rst = rst.concat(arr[i]);
  }

  return rst;
};

/* harmony default export */ __webpack_exports__["a"] = (flatten);

/***/ }),
/* 39 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);

/**
 * Flattens `array` a single level deep.
 *
 * @param {Array} arr The array to flatten.
 * @param {Array} result The array to return.
 * @return {Array} Returns the new flattened array.
 * @example
 *
 * flattenDeep([1, [2, [3, [4]], 5]]);  // => [1, 2, 3, 4, 5]
 */

var flattenDeep = function (arr, result) {
  if (result === void 0) {
    result = [];
  }

  if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(arr)) {
    result.push(arr);
  } else {
    for (var i = 0; i < arr.length; i += 1) {
      flattenDeep(arr[i], result);
    }
  }

  return result;
};

/* harmony default export */ __webpack_exports__["a"] = (flattenDeep);

/***/ }),
/* 40 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__filter__ = __webpack_require__(14);



var getRange = function (values) {
  // 存在 NaN 时，min,max 判定会出问题
  values = Object(__WEBPACK_IMPORTED_MODULE_1__filter__["a" /* default */])(values, function (v) {
    return !isNaN(v);
  });

  if (!values.length) {
    // 如果没有数值则直接返回0
    return {
      min: 0,
      max: 0
    };
  }

  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(values[0])) {
    var tmp = [];

    for (var i = 0; i < values.length; i++) {
      tmp = tmp.concat(values[i]);
    }

    values = tmp;
  }

  var max = Math.max.apply(null, values);
  var min = Math.min.apply(null, values);
  return {
    min: min,
    max: max
  };
};

/* harmony default export */ __webpack_exports__["a"] = (getRange);

/***/ }),
/* 41 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var arrPrototype = Array.prototype;
var splice = arrPrototype.splice;
var indexOf = arrPrototype.indexOf;

var pull = function (arr) {
  var values = [];

  for (var _i = 1; _i < arguments.length; _i++) {
    values[_i - 1] = arguments[_i];
  }

  for (var i = 0; i < values.length; i++) {
    var value = values[i];
    var fromIndex = -1;

    while ((fromIndex = indexOf.call(arr, value)) > -1) {
      splice.call(arr, fromIndex, 1);
    }
  }

  return arr;
};

/* harmony default export */ __webpack_exports__["a"] = (pull);

/***/ }),
/* 42 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__is_plain_object__ = __webpack_require__(9);




var reduce = function (arr, fn, init) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_1__is_array__["a" /* default */])(arr) && !Object(__WEBPACK_IMPORTED_MODULE_2__is_plain_object__["a" /* default */])(arr)) {
    return arr;
  }

  var result = init;
  Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(arr, function (data, i) {
    result = fn(result, data, i);
  });
  return result;
};

/* harmony default export */ __webpack_exports__["a"] = (reduce);

/***/ }),
/* 43 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array_like__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__pull_at__ = __webpack_require__(21);



var remove = function (arr, predicate) {
  /**
   * const arr = [1, 2, 3, 4]
   * const evens = remove(arr, n => n % 2 == 0)
   * console.log(arr) // => [1, 3]
   * console.log(evens) // => [2, 4]
   */
  var result = [];

  if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_array_like__["a" /* default */])(arr)) {
    return result;
  }

  var i = -1;
  var indexes = [];
  var length = arr.length;

  while (++i < length) {
    var value = arr[i];

    if (predicate(value, i, arr)) {
      result.push(value);
      indexes.push(i);
    }
  }

  Object(__WEBPACK_IMPORTED_MODULE_1__pull_at__["a" /* default */])(arr, indexes);
  return result;
};

/* harmony default export */ __webpack_exports__["a"] = (remove);

/***/ }),
/* 44 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_string__ = __webpack_require__(8);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__is_function__ = __webpack_require__(3);




function sortBy(arr, key) {
  var comparer;

  if (Object(__WEBPACK_IMPORTED_MODULE_2__is_function__["a" /* default */])(key)) {
    comparer = function (a, b) {
      return key(a) - key(b);
    };
  } else {
    var keys_1 = [];

    if (Object(__WEBPACK_IMPORTED_MODULE_1__is_string__["a" /* default */])(key)) {
      keys_1.push(key);
    } else if (Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(key)) {
      keys_1 = key;
    }

    comparer = function (a, b) {
      for (var i = 0; i < keys_1.length; i += 1) {
        var prop = keys_1[i];

        if (a[prop] > b[prop]) {
          return 1;
        }

        if (a[prop] < b[prop]) {
          return -1;
        }
      }

      return 0;
    };
  }

  arr.sort(comparer);
  return arr;
}

/* harmony default export */ __webpack_exports__["a"] = (sortBy);

/***/ }),
/* 45 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__uniq__ = __webpack_require__(22);


var union = function () {
  var sources = [];

  for (var _i = 0; _i < arguments.length; _i++) {
    sources[_i] = arguments[_i];
  }

  return Object(__WEBPACK_IMPORTED_MODULE_0__uniq__["a" /* default */])([].concat.apply([], sources));
};

/* harmony default export */ __webpack_exports__["a"] = (union);

/***/ }),
/* 46 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__is_nil__ = __webpack_require__(6);



/* harmony default export */ __webpack_exports__["a"] = (function (data, name) {
  var rst = [];
  var tmpMap = {};
  data.forEach(function (obj) {
    var value = obj[name];

    if (!Object(__WEBPACK_IMPORTED_MODULE_2__is_nil__["a" /* default */])(value)) {
      // flatten
      if (!Object(__WEBPACK_IMPORTED_MODULE_1__is_array__["a" /* default */])(value)) {
        value = [value];
      }

      Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(value, function (val) {
        // unique
        if (!tmpMap[val]) {
          rst.push(val);
          tmpMap[val] = true;
        }
      });
    }
  });
  return rst;
});

/***/ }),
/* 47 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = head;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array_like__ = __webpack_require__(1);

function head(o) {
  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_array_like__["a" /* default */])(o)) {
    return o[0];
  }

  return undefined;
}

/***/ }),
/* 48 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = last;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array_like__ = __webpack_require__(1);

function last(o) {
  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_array_like__["a" /* default */])(o)) {
    var arr = o;
    return arr[arr.length - 1];
  }

  return undefined;
}

/***/ }),
/* 49 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_string__ = __webpack_require__(8);



function startsWith(arr, e) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(arr) || Object(__WEBPACK_IMPORTED_MODULE_1__is_string__["a" /* default */])(arr) ? arr[0] === e : false;
}

/* harmony default export */ __webpack_exports__["a"] = (startsWith);

/***/ }),
/* 50 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_string__ = __webpack_require__(8);



function endsWith(arr, e) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(arr) || Object(__WEBPACK_IMPORTED_MODULE_1__is_string__["a" /* default */])(arr) ? arr[arr.length - 1] === e : false;
}

/* harmony default export */ __webpack_exports__["a"] = (endsWith);

/***/ }),
/* 51 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * 只要有一个不满足条件就返回 false
 * @param arr
 * @param func
 */
var every = function (arr, func) {
  for (var i = 0; i < arr.length; i++) {
    if (!func(arr[i], i)) return false;
  }

  return true;
};

/* harmony default export */ __webpack_exports__["a"] = (every);

/***/ }),
/* 52 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * 只要有一个满足条件就返回 true
 * @param arr
 * @param func
 */
var some = function (arr, func) {
  for (var i = 0; i < arr.length; i++) {
    if (func(arr[i], i)) return true;
  }

  return false;
};

/* harmony default export */ __webpack_exports__["a"] = (some);

/***/ }),
/* 53 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__group_to_map__ = __webpack_require__(23);

/* harmony default export */ __webpack_exports__["a"] = (function (data, condition) {
  if (!condition) {
    // 没有条件，则自身改成数组
    return [data];
  }

  var groups = Object(__WEBPACK_IMPORTED_MODULE_0__group_to_map__["a" /* default */])(data, condition);
  var array = [];

  for (var i in groups) {
    array.push(groups[i]);
  }

  return array;
});

/***/ }),
/* 54 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * 获取封装的事件
 * @protected
 * @param  {Object} obj   对象
 * @param  {String} action 事件名称
 * @return {Function}        返回事件处理函数
 */
function getWrapBehavior(obj, action) {
  return obj['_wrap_' + action];
}

/* harmony default export */ __webpack_exports__["a"] = (getWrapBehavior);

/***/ }),
/* 55 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * 封装事件，便于使用上下文this,和便于解除事件时使用
 * @protected
 * @param  {Object} obj   对象
 * @param  {String} action 事件名称
 * @return {Function}        返回事件处理函数
 */
function wrapBehavior(obj, action) {
  if (obj['_wrap_' + action]) {
    return obj['_wrap_' + action];
  }

  var method = function (e) {
    obj[action](e);
  };

  obj['_wrap_' + action] = method;
  return method;
}

/* harmony default export */ __webpack_exports__["a"] = (wrapBehavior);

/***/ }),
/* 56 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var numColorCache = {};

function numberToColor(num) {
  // 增加缓存
  var color = numColorCache[num];

  if (!color) {
    var str = num.toString(16);

    for (var i = str.length; i < 6; i++) {
      str = '0' + str;
    }

    color = '#' + str;
    numColorCache[num] = color;
  }

  return color;
}

/* harmony default export */ __webpack_exports__["a"] = (numberToColor);

/***/ }),
/* 57 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);


function parseRadius(radius) {
  var r1 = 0,
      r2 = 0,
      r3 = 0,
      r4 = 0;

  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(radius)) {
    if (radius.length === 1) {
      r1 = r2 = r3 = r4 = radius[0];
    } else if (radius.length === 2) {
      r1 = r3 = radius[0];
      r2 = r4 = radius[1];
    } else if (radius.length === 3) {
      r1 = radius[0];
      r2 = r4 = radius[1];
      r3 = radius[2];
    } else {
      r1 = radius[0];
      r2 = radius[1];
      r3 = radius[2];
      r4 = radius[3];
    }
  } else {
    r1 = r2 = r3 = r4 = radius;
  }

  return {
    r1: r1,
    r2: r2,
    r3: r3,
    r4: r4
  };
}

/* harmony default export */ __webpack_exports__["a"] = (parseRadius);

/***/ }),
/* 58 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var clamp = function (a, min, max) {
  if (a < min) {
    return min;
  } else if (a > max) {
    return max;
  }

  return a;
};

/* harmony default export */ __webpack_exports__["a"] = (clamp);

/***/ }),
/* 59 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var fixedBase = function (v, base) {
  var str = base.toString();
  var index = str.indexOf('.');

  if (index === -1) {
    return Math.round(v);
  }

  var length = str.substr(index + 1).length;

  if (length > 20) {
    length = 20;
  }

  return parseFloat(v.toFixed(length));
};

/* harmony default export */ __webpack_exports__["a"] = (fixedBase);

/***/ }),
/* 60 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_number__ = __webpack_require__(5);


var isDecimal = function (num) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_number__["a" /* default */])(num) && num % 1 !== 0;
};

/* harmony default export */ __webpack_exports__["a"] = (isDecimal);

/***/ }),
/* 61 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_number__ = __webpack_require__(5);


var isEven = function (num) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_number__["a" /* default */])(num) && num % 2 === 0;
};

/* harmony default export */ __webpack_exports__["a"] = (isEven);

/***/ }),
/* 62 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_number__ = __webpack_require__(5);

var isInteger = Number.isInteger ? Number.isInteger : function (num) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_number__["a" /* default */])(num) && num % 1 === 0;
};
/* harmony default export */ __webpack_exports__["a"] = (isInteger);

/***/ }),
/* 63 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_number__ = __webpack_require__(5);


var isNegative = function (num) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_number__["a" /* default */])(num) && num < 0;
};

/* harmony default export */ __webpack_exports__["a"] = (isNegative);

/***/ }),
/* 64 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = isNumberEqual;
var PRECISION = 0.00001; // numbers less than this is considered as 0

function isNumberEqual(a, b, precision) {
  if (precision === void 0) {
    precision = PRECISION;
  }

  return Math.abs(a - b) < precision;
}
;

/***/ }),
/* 65 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_number__ = __webpack_require__(5);


var isOdd = function (num) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_number__["a" /* default */])(num) && num % 2 !== 0;
};

/* harmony default export */ __webpack_exports__["a"] = (isOdd);

/***/ }),
/* 66 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_number__ = __webpack_require__(5);


var isPositive = function (num) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_number__["a" /* default */])(num) && num > 0;
};

/* harmony default export */ __webpack_exports__["a"] = (isPositive);

/***/ }),
/* 67 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__is_function__ = __webpack_require__(3);



/**
 * @param {Array} arr The array to iterate over.
 * @param {Function} [fn] The iteratee invoked per element.
 * @return {*} Returns the maximum value.
 * @example
 *
 * var objects = [{ 'n': 1 }, { 'n': 2 }];
 *
 * maxBy(objects, function(o) { return o.n; });
 * // => { 'n': 2 }
 *
 * maxBy(objects, 'n');
 * // => { 'n': 2 }
 */

/* harmony default export */ __webpack_exports__["a"] = (function (arr, fn) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_1__is_array__["a" /* default */])(arr)) {
    return undefined;
  }

  var max = arr[0];
  var maxData;

  if (Object(__WEBPACK_IMPORTED_MODULE_2__is_function__["a" /* default */])(fn)) {
    maxData = fn(arr[0]);
  } else {
    maxData = arr[0][fn];
  }

  var data;
  Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(arr, function (val) {
    if (Object(__WEBPACK_IMPORTED_MODULE_2__is_function__["a" /* default */])(fn)) {
      data = fn(val);
    } else {
      data = val[fn];
    }

    if (data > maxData) {
      max = val;
      maxData = data;
    }
  });
  return max;
});

/***/ }),
/* 68 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__is_function__ = __webpack_require__(3);



/**
 * @param {Array} arr The array to iterate over.
 * @param {Function} [fn] The iteratee invoked per element.
 * @return {*} Returns the minimum value.
 * @example
 *
 * var objects = [{ 'n': 1 }, { 'n': 2 }];
 *
 * minBy(objects, function(o) { return o.n; });
 * // => { 'n': 1 }
 *
 * minBy(objects, 'n');
 * // => { 'n': 1 }
 */

/* harmony default export */ __webpack_exports__["a"] = (function (arr, fn) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_1__is_array__["a" /* default */])(arr)) {
    return undefined;
  }

  var min = arr[0];
  var minData;

  if (Object(__WEBPACK_IMPORTED_MODULE_2__is_function__["a" /* default */])(fn)) {
    minData = fn(arr[0]);
  } else {
    minData = arr[0][fn];
  }

  var data;
  Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(arr, function (val) {
    if (Object(__WEBPACK_IMPORTED_MODULE_2__is_function__["a" /* default */])(fn)) {
      data = fn(val);
    } else {
      data = val[fn];
    }

    if (data < minData) {
      min = val;
      minData = data;
    }
  });
  return min;
});

/***/ }),
/* 69 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var mod = function (n, m) {
  return (n % m + m) % m;
};

/* harmony default export */ __webpack_exports__["a"] = (mod);

/***/ }),
/* 70 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var DEGREE = 180 / Math.PI;

var toDegree = function (radian) {
  return DEGREE * radian;
};

/* harmony default export */ __webpack_exports__["a"] = (toDegree);

/***/ }),
/* 71 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony default export */ __webpack_exports__["a"] = (parseInt);

/***/ }),
/* 72 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var RADIAN = Math.PI / 180;

var toRadian = function (degree) {
  return RADIAN * degree;
};

/* harmony default export */ __webpack_exports__["a"] = (toRadian);

/***/ }),
/* 73 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);

/* harmony default export */ __webpack_exports__["a"] = (__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */]);

/***/ }),
/* 74 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__has__ = __webpack_require__(25);

/* harmony default export */ __webpack_exports__["a"] = (__WEBPACK_IMPORTED_MODULE_0__has__["a" /* default */]);

/***/ }),
/* 75 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__contains__ = __webpack_require__(12);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__values__ = __webpack_require__(26);


/* harmony default export */ __webpack_exports__["a"] = (function (obj, value) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__contains__["a" /* default */])(Object(__WEBPACK_IMPORTED_MODULE_1__values__["a" /* default */])(obj), value);
});

/***/ }),
/* 76 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__to_string__ = __webpack_require__(10);


var lowerCase = function (str) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__to_string__["a" /* default */])(str).toLowerCase();
};

/* harmony default export */ __webpack_exports__["a"] = (lowerCase);

/***/ }),
/* 77 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__to_string__ = __webpack_require__(10);


var lowerFirst = function (value) {
  var str = Object(__WEBPACK_IMPORTED_MODULE_0__to_string__["a" /* default */])(value);
  return str.charAt(0).toLowerCase() + str.substring(1);
};

/* harmony default export */ __webpack_exports__["a"] = (lowerFirst);

/***/ }),
/* 78 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
function substitute(str, o) {
  if (!str || !o) {
    return str;
  }

  return str.replace(/\\?\{([^{}]+)\}/g, function (match, name) {
    if (match.charAt(0) === '\\') {
      return match.slice(1);
    }

    return o[name] === undefined ? '' : o[name];
  });
}

/* harmony default export */ __webpack_exports__["a"] = (substitute);

/***/ }),
/* 79 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__to_string__ = __webpack_require__(10);


var upperCase = function (str) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__to_string__["a" /* default */])(str).toUpperCase();
};

/* harmony default export */ __webpack_exports__["a"] = (upperCase);

/***/ }),
/* 80 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__to_string__ = __webpack_require__(10);


var upperFirst = function (value) {
  var str = Object(__WEBPACK_IMPORTED_MODULE_0__to_string__["a" /* default */])(value);
  return str.charAt(0).toUpperCase() + str.substring(1);
};

/* harmony default export */ __webpack_exports__["a"] = (upperFirst);

/***/ }),
/* 81 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_type__ = __webpack_require__(4);
/**
 * 是否是参数类型
 *
 * @param {Object} value 测试的值
 * @return {Boolean}
 */


var isArguments = function (value) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_type__["a" /* default */])(value, 'Arguments');
};

/* harmony default export */ __webpack_exports__["a"] = (isArguments);

/***/ }),
/* 82 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_type__ = __webpack_require__(4);
/**
 * 是否是布尔类型
 *
 * @param {Object} value 测试的值
 * @return {Boolean}
 */


var isBoolean = function (value) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_type__["a" /* default */])(value, 'Boolean');
};

/* harmony default export */ __webpack_exports__["a"] = (isBoolean);

/***/ }),
/* 83 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_type__ = __webpack_require__(4);


var isDate = function (value) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_type__["a" /* default */])(value, 'Date');
};

/* harmony default export */ __webpack_exports__["a"] = (isDate);

/***/ }),
/* 84 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_type__ = __webpack_require__(4);
/**
 * 是否是参数类型
 *
 * @param {Object} value 测试的值
 * @return {Boolean}
 */


var isError = function (value) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_type__["a" /* default */])(value, 'Error');
};

/* harmony default export */ __webpack_exports__["a"] = (isError);

/***/ }),
/* 85 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_number__ = __webpack_require__(5);
/**
 * 判断是否为有限数
 * @return {Boolean}
 */

/* harmony default export */ __webpack_exports__["a"] = (function (value) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_number__["a" /* default */])(value) && isFinite(value);
});

/***/ }),
/* 86 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var isNull = function (value) {
  return value === null;
};

/* harmony default export */ __webpack_exports__["a"] = (isNull);

/***/ }),
/* 87 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_type__ = __webpack_require__(4);


var isRegExp = function (str) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_type__["a" /* default */])(str, 'RegExp');
};

/* harmony default export */ __webpack_exports__["a"] = (isRegExp);

/***/ }),
/* 88 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var isUndefined = function (value) {
  return value === undefined;
};

/* harmony default export */ __webpack_exports__["a"] = (isUndefined);

/***/ }),
/* 89 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * 判断是否HTML元素
 * @return {Boolean} 是否HTML元素
 */
var isElement = function (o) {
  return o instanceof Element || o instanceof HTMLDocument;
};

/* harmony default export */ __webpack_exports__["a"] = (isElement);

/***/ }),
/* 90 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = requestAnimationFrame;
function requestAnimationFrame(fn) {
  var method = window.requestAnimationFrame || window.webkitRequestAnimationFrame || // @ts-ignore
  window.mozRequestAnimationFrame || // @ts-ignore
  window.msRequestAnimationFrame || function (f) {
    return setTimeout(f, 16);
  };

  return method(fn);
}
;

/***/ }),
/* 91 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = cancelAnimationFrame;
function cancelAnimationFrame(handler) {
  var method = window.cancelAnimationFrame || window.webkitCancelAnimationFrame || // @ts-ignore
  window.mozCancelAnimationFrame || // @ts-ignore
  window.msCancelAnimationFrame || clearTimeout;
  method(handler);
}
;

/***/ }),
/* 92 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__mix__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_function__ = __webpack_require__(3);



var augment = function () {
  var args = [];

  for (var _i = 0; _i < arguments.length; _i++) {
    args[_i] = arguments[_i];
  }

  var c = args[0];

  for (var i = 1; i < args.length; i++) {
    var obj = args[i];

    if (Object(__WEBPACK_IMPORTED_MODULE_1__is_function__["a" /* default */])(obj)) {
      obj = obj.prototype;
    }

    Object(__WEBPACK_IMPORTED_MODULE_0__mix__["a" /* default */])(c.prototype, obj);
  }
};

/* harmony default export */ __webpack_exports__["a"] = (augment);

/***/ }),
/* 93 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);


var clone = function (obj) {
  if (typeof obj !== 'object' || obj === null) {
    return obj;
  }

  var rst;

  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(obj)) {
    rst = [];

    for (var i = 0, l = obj.length; i < l; i++) {
      if (typeof obj[i] === 'object' && obj[i] != null) {
        rst[i] = clone(obj[i]);
      } else {
        rst[i] = obj[i];
      }
    }
  } else {
    rst = {};

    for (var k in obj) {
      if (typeof obj[k] === 'object' && obj[k] != null) {
        rst[k] = clone(obj[k]);
      } else {
        rst[k] = obj[k];
      }
    }
  }

  return rst;
};

/* harmony default export */ __webpack_exports__["a"] = (clone);

/***/ }),
/* 94 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
function debounce(func, wait, immediate) {
  var timeout;
  return function () {
    var context = this,
        args = arguments;

    var later = function () {
      timeout = null;

      if (!immediate) {
        func.apply(context, args);
      }
    };

    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);

    if (callNow) {
      func.apply(context, args);
    }
  };
}

/* harmony default export */ __webpack_exports__["a"] = (debounce);

/***/ }),
/* 95 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_function__ = __webpack_require__(3);

/**
 * _.memoize(calColor);
 * _.memoize(calColor, (...args) => args[0]);
 * @param f
 * @param resolver
 */

/* harmony default export */ __webpack_exports__["a"] = (function (f, resolver) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_function__["a" /* default */])(f)) {
    throw new TypeError('Expected a function');
  }

  var memoized = function () {
    var args = [];

    for (var _i = 0; _i < arguments.length; _i++) {
      args[_i] = arguments[_i];
    } // 使用方法构造 key，如果不存在 resolver，则直接取第一个参数作为 key


    var key = resolver ? resolver.apply(this, args) : args[0];
    var cache = memoized.cache;

    if (cache.has(key)) {
      return cache.get(key);
    }

    var result = f.apply(this, args); // 缓存起来

    cache.set(key, result);
    return result;
  };

  memoized.cache = new Map();
  return memoized;
});

/***/ }),
/* 96 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array__ = __webpack_require__(0);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_plain_object__ = __webpack_require__(9);


var MAX_MIX_LEVEL = 5;

function _deepMix(dist, src, level, maxLevel) {
  level = level || 0;
  maxLevel = maxLevel || MAX_MIX_LEVEL;

  for (var key in src) {
    if (src.hasOwnProperty(key)) {
      var value = src[key];

      if (value !== null && Object(__WEBPACK_IMPORTED_MODULE_1__is_plain_object__["a" /* default */])(value)) {
        if (!Object(__WEBPACK_IMPORTED_MODULE_1__is_plain_object__["a" /* default */])(dist[key])) {
          dist[key] = {};
        }

        if (level < maxLevel) {
          _deepMix(dist[key], value, level + 1, maxLevel);
        } else {
          dist[key] = src[key];
        }
      } else if (Object(__WEBPACK_IMPORTED_MODULE_0__is_array__["a" /* default */])(value)) {
        dist[key] = [];
        dist[key] = dist[key].concat(value);
      } else if (value !== undefined) {
        dist[key] = value;
      }
    }
  }
} // todo 重写


var deepMix = function (rst) {
  var args = [];

  for (var _i = 1; _i < arguments.length; _i++) {
    args[_i - 1] = arguments[_i];
  }

  for (var i = 0; i < args.length; i += 1) {
    _deepMix(rst, args[i]);
  }

  return rst;
};

/* harmony default export */ __webpack_exports__["a"] = (deepMix);

/***/ }),
/* 97 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__mix__ = __webpack_require__(16);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_function__ = __webpack_require__(3);



var extend = function (subclass, superclass, overrides, staticOverrides) {
  // 如果只提供父类构造函数，则自动生成子类构造函数
  if (!Object(__WEBPACK_IMPORTED_MODULE_1__is_function__["a" /* default */])(superclass)) {
    overrides = superclass;
    superclass = subclass;

    subclass = function () {};
  }

  var create = Object.create ? function (proto, c) {
    return Object.create(proto, {
      constructor: {
        value: c
      }
    });
  } : function (proto, c) {
    function Tmp() {}

    Tmp.prototype = proto;
    var o = new Tmp();
    o.constructor = c;
    return o;
  };
  var superObj = create(superclass.prototype, subclass); // new superclass(),//实例化父类作为子类的prototype

  subclass.prototype = Object(__WEBPACK_IMPORTED_MODULE_0__mix__["a" /* default */])(superObj, subclass.prototype); // 指定子类的prototype

  subclass.superclass = create(superclass.prototype, superclass);
  Object(__WEBPACK_IMPORTED_MODULE_0__mix__["a" /* default */])(superObj, overrides);
  Object(__WEBPACK_IMPORTED_MODULE_0__mix__["a" /* default */])(subclass, staticOverrides);
  return subclass;
};

/* harmony default export */ __webpack_exports__["a"] = (extend);

/***/ }),
/* 98 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array_like__ = __webpack_require__(1);


var indexOf = function (arr, obj) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_array_like__["a" /* default */])(arr)) {
    return -1;
  }

  var m = Array.prototype.indexOf;

  if (m) {
    return m.call(arr, obj);
  }

  var index = -1;

  for (var i = 0; i < arr.length; i++) {
    if (arr[i] === obj) {
      index = i;
      break;
    }
  }

  return index;
};

/* harmony default export */ __webpack_exports__["a"] = (indexOf);

/***/ }),
/* 99 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_nil__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array_like__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__get_type__ = __webpack_require__(27);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__is_prototype__ = __webpack_require__(28);




var hasOwnProperty = Object.prototype.hasOwnProperty;

function isEmpty(value) {
  /**
   * isEmpty(null) => true
   * isEmpty() => true
   * isEmpty(true) => true
   * isEmpty(1) => true
   * isEmpty([1, 2, 3]) => false
   * isEmpty('abc') => false
   * isEmpty({ a: 1 }) => false
   */
  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_nil__["a" /* default */])(value)) {
    return true;
  }

  if (Object(__WEBPACK_IMPORTED_MODULE_1__is_array_like__["a" /* default */])(value)) {
    return !value.length;
  }

  var type = Object(__WEBPACK_IMPORTED_MODULE_2__get_type__["a" /* default */])(value);

  if (type === 'Map' || type === 'Set') {
    return !value.size;
  }

  if (Object(__WEBPACK_IMPORTED_MODULE_3__is_prototype__["a" /* default */])(value)) {
    return !Object.keys(value).length;
  }

  for (var key in value) {
    if (hasOwnProperty.call(value, key)) {
      return false;
    }
  }

  return true;
}

/* harmony default export */ __webpack_exports__["a"] = (isEmpty);

/***/ }),
/* 100 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_function__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_equal__ = __webpack_require__(29);


/**
 * @param {*} value The value to compare.
 * @param {*} other The other value to compare.
 * @param {Function} [fn] The function to customize comparisons.
 * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
 * @example
 *
 * function isGreeting(value) {
 *   return /^h(?:i|ello)$/.test(value);
 * }
 *
 * function customizer(objValue, othValue) {
 *   if (isGreeting(objValue) && isGreeting(othValue)) {
 *     return true;
 *   }
 * }
 *
 * var array = ['hello', 'goodbye'];
 * var other = ['hi', 'goodbye'];
 *
 * isEqualWith(array, other, customizer);  // => true
 */

/* harmony default export */ __webpack_exports__["a"] = (function (value, other, fn) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_function__["a" /* default */])(fn)) {
    return Object(__WEBPACK_IMPORTED_MODULE_1__is_equal__["a" /* default */])(value, other);
  }

  return !!fn(value, other);
});

/***/ }),
/* 101 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array_like__ = __webpack_require__(1);



var map = function (arr, func) {
  if (!Object(__WEBPACK_IMPORTED_MODULE_1__is_array_like__["a" /* default */])(arr)) {
    // @ts-ignore
    return arr;
  }

  var result = [];
  Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(arr, function (value, index) {
    result.push(func(value, index));
  });
  return result;
};

/* harmony default export */ __webpack_exports__["a"] = (map);

/***/ }),
/* 102 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_nil__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_object__ = __webpack_require__(13);



var identity = function (v) {
  return v;
};

/* harmony default export */ __webpack_exports__["a"] = (function (object, func) {
  if (func === void 0) {
    func = identity;
  }

  var r = {};

  if (Object(__WEBPACK_IMPORTED_MODULE_1__is_object__["a" /* default */])(object) && !Object(__WEBPACK_IMPORTED_MODULE_0__is_nil__["a" /* default */])(object)) {
    Object.keys(object).forEach(function (key) {
      // @ts-ignore
      r[key] = func(object[key], key);
    });
  }

  return r;
});

/***/ }),
/* 103 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_string__ = __webpack_require__(8);

/**
 * https://github.com/developit/dlv/blob/master/index.js
 * @param obj
 * @param key
 * @param defaultValue
 */

/* harmony default export */ __webpack_exports__["a"] = (function (obj, key, defaultValue) {
  var p = 0;
  var keyArr = Object(__WEBPACK_IMPORTED_MODULE_0__is_string__["a" /* default */])(key) ? key.split('.') : key;

  while (obj && p < keyArr.length) {
    obj = obj[keyArr[p++]];
  }

  return obj === undefined || p < keyArr.length ? defaultValue : obj;
});

/***/ }),
/* 104 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_object__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_string__ = __webpack_require__(8);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__is_number__ = __webpack_require__(5);



/**
 * https://github.com/developit/dlv/blob/master/index.js
 * @param obj
 * @param path
 * @param value
 */

/* harmony default export */ __webpack_exports__["a"] = (function (obj, path, value) {
  var o = obj;
  var keyArr = Object(__WEBPACK_IMPORTED_MODULE_1__is_string__["a" /* default */])(path) ? path.split('.') : path;
  keyArr.forEach(function (key, idx) {
    // 不是最后一个
    if (idx < keyArr.length - 1) {
      if (!Object(__WEBPACK_IMPORTED_MODULE_0__is_object__["a" /* default */])(o[key])) {
        o[key] = Object(__WEBPACK_IMPORTED_MODULE_2__is_number__["a" /* default */])(keyArr[idx + 1]) ? [] : {};
      }

      o = o[key];
    } else {
      o[key] = value;
    }
  });
  return obj;
});

/***/ }),
/* 105 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__each__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_plain_object__ = __webpack_require__(9);


var hasOwnProperty = Object.prototype.hasOwnProperty;
/* harmony default export */ __webpack_exports__["a"] = (function (object, keys) {
  if (object === null || !Object(__WEBPACK_IMPORTED_MODULE_1__is_plain_object__["a" /* default */])(object)) {
    return {};
  }

  var result = {};
  Object(__WEBPACK_IMPORTED_MODULE_0__each__["a" /* default */])(keys, function (key) {
    if (hasOwnProperty.call(object, key)) {
      result[key] = object[key];
    }
  });
  return result;
});

/***/ }),
/* 106 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony default export */ __webpack_exports__["a"] = (function (func, wait, options) {
  var timeout, context, args, result;
  var previous = 0;
  if (!options) options = {};

  var later = function () {
    previous = options.leading === false ? 0 : Date.now();
    timeout = null;
    result = func.apply(context, args);
    if (!timeout) context = args = null;
  };

  var throttled = function () {
    var now = Date.now();
    if (!previous && options.leading === false) previous = now;
    var remaining = wait - (now - previous);
    context = this;
    args = arguments;

    if (remaining <= 0 || remaining > wait) {
      if (timeout) {
        clearTimeout(timeout);
        timeout = null;
      }

      previous = now;
      result = func.apply(context, args);
      if (!timeout) context = args = null;
    } else if (!timeout && options.trailing !== false) {
      timeout = setTimeout(later, remaining);
    }

    return result;
  };

  throttled.cancel = function () {
    clearTimeout(timeout);
    previous = 0;
    timeout = context = args = null;
  };

  return throttled;
});

/***/ }),
/* 107 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_array_like__ = __webpack_require__(1);

/* harmony default export */ __webpack_exports__["a"] = (function (value) {
  return Object(__WEBPACK_IMPORTED_MODULE_0__is_array_like__["a" /* default */])(value) ? Array.prototype.slice.call(value) : [];
});

/***/ }),
/* 108 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var map = {};
/* harmony default export */ __webpack_exports__["a"] = (function (prefix) {
  prefix = prefix || 'g';

  if (!map[prefix]) {
    map[prefix] = 1;
  } else {
    map[prefix] += 1;
  }

  return prefix + map[prefix];
});

/***/ }),
/* 109 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony default export */ __webpack_exports__["a"] = (function () {});

/***/ }),
/* 110 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony default export */ __webpack_exports__["a"] = (function (v) {
  return v;
});

/***/ }),
/* 111 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = size;
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__is_nil__ = __webpack_require__(6);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__is_array_like__ = __webpack_require__(1);


function size(o) {
  if (Object(__WEBPACK_IMPORTED_MODULE_0__is_nil__["a" /* default */])(o)) {
    return 0;
  }

  if (Object(__WEBPACK_IMPORTED_MODULE_1__is_array_like__["a" /* default */])(o)) {
    return o.length;
  }

  return Object.keys(o).length;
}

/***/ }),
/* 112 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/**
 * k-v 存储
 */
var default_1 =
/** @class */
function () {
  function default_1() {
    this.map = {};
  }

  default_1.prototype.has = function (key) {
    return this.map[key] !== undefined;
  };

  default_1.prototype.get = function (key, def) {
    var v = this.map[key];
    return v === undefined ? def : v;
  };

  default_1.prototype.set = function (key, value) {
    this.map[key] = value;
  };

  default_1.prototype.clear = function () {
    this.map = {};
  };

  default_1.prototype.delete = function (key) {
    delete this.map[key];
  };

  default_1.prototype.size = function () {
    return Object.keys(this.map).length;
  };

  return default_1;
}();

/* harmony default export */ __webpack_exports__["a"] = (default_1);

/***/ }),
/* 113 */
/***/ (function(module, exports) {

// wrap tree node
function WrappedTree(w, h, y, c) {
  if (c === void 0) {
    c = [];
  }

  var me = this; // size

  me.w = w || 0;
  me.h = h || 0; // position

  me.y = y || 0;
  me.x = 0; // children

  me.c = c || [];
  me.cs = c.length; // modified

  me.prelim = 0;
  me.mod = 0;
  me.shift = 0;
  me.change = 0; // left/right tree

  me.tl = null;
  me.tr = null; // extreme left/right tree

  me.el = null;
  me.er = null; // modified left/right tree

  me.msel = 0;
  me.mser = 0;
}

WrappedTree.fromNode = function (root, isHorizontal) {
  if (!root) return null;
  var children = [];
  root.children.forEach(function (child) {
    children.push(WrappedTree.fromNode(child, isHorizontal));
  });
  if (isHorizontal) return new WrappedTree(root.height, root.width, root.x, children);
  return new WrappedTree(root.width, root.height, root.y, children);
}; // node utils


function moveRight(node, move, isHorizontal) {
  if (isHorizontal) {
    node.y += move;
  } else {
    node.x += move;
  }

  node.children.forEach(function (child) {
    moveRight(child, move, isHorizontal);
  });
}

function getMin(node, isHorizontal) {
  var res = isHorizontal ? node.y : node.x;
  node.children.forEach(function (child) {
    res = Math.min(getMin(child, isHorizontal), res);
  });
  return res;
}

function normalize(node, isHorizontal) {
  var min = getMin(node, isHorizontal);
  moveRight(node, -min, isHorizontal);
}

function convertBack(converted
/* WrappedTree */
, root
/* TreeNode */
, isHorizontal) {
  if (isHorizontal) {
    root.y = converted.x;
  } else {
    root.x = converted.x;
  }

  converted.c.forEach(function (child, i) {
    convertBack(child, root.children[i], isHorizontal);
  });
}

function layer(node, isHorizontal, d) {
  if (d === void 0) {
    d = 0;
  }

  if (isHorizontal) {
    node.x = d;
    d += node.width;
  } else {
    node.y = d;
    d += node.height;
  }

  node.children.forEach(function (child) {
    layer(child, isHorizontal, d);
  });
}

module.exports = function (root, options) {
  if (options === void 0) {
    options = {};
  }

  var isHorizontal = options.isHorizontal;

  function firstWalk(t) {
    if (t.cs === 0) {
      setExtremes(t);
      return;
    }

    firstWalk(t.c[0]);
    var ih = updateIYL(bottom(t.c[0].el), 0, null);

    for (var i = 1; i < t.cs; ++i) {
      firstWalk(t.c[i]);
      var min = bottom(t.c[i].er);
      separate(t, i, ih);
      ih = updateIYL(min, i, ih);
    }

    positionRoot(t);
    setExtremes(t);
  }

  function setExtremes(t) {
    if (t.cs === 0) {
      t.el = t;
      t.er = t;
      t.msel = t.mser = 0;
    } else {
      t.el = t.c[0].el;
      t.msel = t.c[0].msel;
      t.er = t.c[t.cs - 1].er;
      t.mser = t.c[t.cs - 1].mser;
    }
  }

  function separate(t, i, ih) {
    var sr = t.c[i - 1];
    var mssr = sr.mod;
    var cl = t.c[i];
    var mscl = cl.mod;

    while (sr !== null && cl !== null) {
      if (bottom(sr) > ih.low) ih = ih.nxt;
      var dist = mssr + sr.prelim + sr.w - (mscl + cl.prelim);

      if (dist > 0) {
        mscl += dist;
        moveSubtree(t, i, ih.index, dist);
      }

      var sy = bottom(sr);
      var cy = bottom(cl);

      if (sy <= cy) {
        sr = nextRightContour(sr);
        if (sr !== null) mssr += sr.mod;
      }

      if (sy >= cy) {
        cl = nextLeftContour(cl);
        if (cl !== null) mscl += cl.mod;
      }
    }

    if (!sr && !!cl) {
      setLeftThread(t, i, cl, mscl);
    } else if (!!sr && !cl) {
      setRightThread(t, i, sr, mssr);
    }
  }

  function moveSubtree(t, i, si, dist) {
    t.c[i].mod += dist;
    t.c[i].msel += dist;
    t.c[i].mser += dist;
    distributeExtra(t, i, si, dist);
  }

  function nextLeftContour(t) {
    return t.cs === 0 ? t.tl : t.c[0];
  }

  function nextRightContour(t) {
    return t.cs === 0 ? t.tr : t.c[t.cs - 1];
  }

  function bottom(t) {
    return t.y + t.h;
  }

  function setLeftThread(t, i, cl, modsumcl) {
    var li = t.c[0].el;
    li.tl = cl;
    var diff = modsumcl - cl.mod - t.c[0].msel;
    li.mod += diff;
    li.prelim -= diff;
    t.c[0].el = t.c[i].el;
    t.c[0].msel = t.c[i].msel;
  }

  function setRightThread(t, i, sr, modsumsr) {
    var ri = t.c[i].er;
    ri.tr = sr;
    var diff = modsumsr - sr.mod - t.c[i].mser;
    ri.mod += diff;
    ri.prelim -= diff;
    t.c[i].er = t.c[i - 1].er;
    t.c[i].mser = t.c[i - 1].mser;
  }

  function positionRoot(t) {
    t.prelim = (t.c[0].prelim + t.c[0].mod + t.c[t.cs - 1].mod + t.c[t.cs - 1].prelim + t.c[t.cs - 1].w) / 2 - t.w / 2;
  }

  function secondWalk(t, modsum) {
    modsum += t.mod;
    t.x = t.prelim + modsum;
    addChildSpacing(t);

    for (var i = 0; i < t.cs; i++) {
      secondWalk(t.c[i], modsum);
    }
  }

  function distributeExtra(t, i, si, dist) {
    if (si !== i - 1) {
      var nr = i - si;
      t.c[si + 1].shift += dist / nr;
      t.c[i].shift -= dist / nr;
      t.c[i].change -= dist - dist / nr;
    }
  }

  function addChildSpacing(t) {
    var d = 0;
    var modsumdelta = 0;

    for (var i = 0; i < t.cs; i++) {
      d += t.c[i].shift;
      modsumdelta += d + t.c[i].change;
      t.c[i].mod += modsumdelta;
    }
  }

  function updateIYL(low, index, ih) {
    while (ih !== null && low >= ih.low) {
      ih = ih.nxt;
    }

    return {
      low: low,
      index: index,
      nxt: ih
    };
  } // do layout


  layer(root, isHorizontal);
  var wt = WrappedTree.fromNode(root, isHorizontal);
  firstWalk(wt);
  secondWalk(wt, 0);
  convertBack(wt, root, isHorizontal);
  normalize(root, isHorizontal);
  return root;
};

/***/ }),
/* 114 */
/***/ (function(module, exports, __webpack_require__) {

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; subClass.__proto__ = superClass; }

var TreeLayout = __webpack_require__(11);

var dendrogram = __webpack_require__(115);

var doTreeLayout = __webpack_require__(17);

var util = __webpack_require__(7);

var DendrogramLayout = /*#__PURE__*/function (_TreeLayout) {
  _inheritsLoose(DendrogramLayout, _TreeLayout);

  function DendrogramLayout() {
    return _TreeLayout.apply(this, arguments) || this;
  }

  var _proto = DendrogramLayout.prototype;

  _proto.execute = function execute() {
    var me = this;
    me.rootNode.width = 0;
    return doTreeLayout(me.rootNode, me.options, dendrogram);
  };

  return DendrogramLayout;
}(TreeLayout);

var DEFAULT_OPTIONS = {};

function dendrogramLayout(root, options) {
  options = util.assign({}, DEFAULT_OPTIONS, options);
  return new DendrogramLayout(root, options).execute();
}

module.exports = dendrogramLayout;

/***/ }),
/* 115 */
/***/ (function(module, exports, __webpack_require__) {

// wrap tree node
// TODO considering size
var util = __webpack_require__(7);

function WrappedTree(height, children) {
  if (height === void 0) {
    height = 0;
  }

  if (children === void 0) {
    children = [];
  }

  var me = this;
  me.x = me.y = 0;
  me.leftChild = me.rightChild = null;
  me.height = 0;
  me.children = children;
}

var DEFAULT_OPTIONS = {
  isHorizontal: true,
  nodeSep: 20,
  nodeSize: 20,
  rankSep: 200,
  subTreeSep: 10
};

function convertBack(converted
/* WrappedTree */
, root
/* TreeNode */
, isHorizontal) {
  if (isHorizontal) {
    root.x = converted.x;
    root.y = converted.y;
  } else {
    root.x = converted.y;
    root.y = converted.x;
  }

  converted.children.forEach(function (child, i) {
    convertBack(child, root.children[i], isHorizontal);
  });
}

module.exports = function (root, options) {
  if (options === void 0) {
    options = {};
  }

  options = util.assign({}, DEFAULT_OPTIONS, options);
  var maxDepth = 0;

  function wrappedTreeFromNode(n) {
    if (!n) return null;
    n.width = 0;

    if (n.depth && n.depth > maxDepth) {
      maxDepth = n.depth; // get the max depth
    }

    var children = n.children;
    var childrenCount = children.length;
    var t = new WrappedTree(n.height, []);
    children.forEach(function (child, i) {
      var childWT = wrappedTreeFromNode(child);
      t.children.push(childWT);

      if (i === 0) {
        // t.leftChild = childWT.leftChild ? childWT.leftChild : childWT
        t.leftChild = childWT;
      }

      if (i === childrenCount - 1) {
        // t.rightChild = childWT.rightChild ? childWT.rightChild : childWT
        t.rightChild = childWT;
      }
    });
    t.originNode = n;
    t.isLeaf = n.isLeaf();
    return t;
  }

  function getDrawingDepth(t) {
    if (t.isLeaf || t.children.length === 0) {
      t.drawingDepth = maxDepth;
    } else {
      var depths = t.children.map(function (child) {
        return getDrawingDepth(child);
      });
      var minChildDepth = Math.min.apply(null, depths);
      t.drawingDepth = minChildDepth - 1;
    }

    return t.drawingDepth;
  }

  var prevLeaf;

  function position(t) {
    t.x = t.drawingDepth * options.rankSep;

    if (t.isLeaf) {
      t.y = 0;

      if (prevLeaf) {
        t.y = prevLeaf.y + prevLeaf.height + options.nodeSep;

        if (t.originNode.parent !== prevLeaf.originNode.parent) {
          t.y += options.subTreeSep;
        }
      }

      prevLeaf = t;
    } else {
      t.children.forEach(function (child) {
        position(child);
      });
      t.y = (t.leftChild.y + t.rightChild.y) / 2;
    }
  } // wrap node


  var wt = wrappedTreeFromNode(root); // get depth for drawing

  getDrawingDepth(wt); // get position

  position(wt); // get x, y

  convertBack(wt, root, options.isHorizontal);
  return root;
};

/***/ }),
/* 116 */
/***/ (function(module, exports, __webpack_require__) {

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; subClass.__proto__ = superClass; }

var TreeLayout = __webpack_require__(11);

var indentedTree = __webpack_require__(117);

var separateTree = __webpack_require__(30);

var util = __webpack_require__(7);

var VALID_DIRECTIONS = ['LR', // left to right
'RL', // right to left
'H' // horizontal
];
var DEFAULT_DIRECTION = VALID_DIRECTIONS[0];

var IndentedLayout = /*#__PURE__*/function (_TreeLayout) {
  _inheritsLoose(IndentedLayout, _TreeLayout);

  function IndentedLayout() {
    return _TreeLayout.apply(this, arguments) || this;
  }

  var _proto = IndentedLayout.prototype;

  _proto.execute = function execute() {
    var me = this;
    var options = me.options;
    var root = me.rootNode;
    options.isHorizontal = true; // default indent 20 and sink first children;

    var _options$indent = options.indent,
        indent = _options$indent === void 0 ? 20 : _options$indent,
        _options$dropCap = options.dropCap,
        dropCap = _options$dropCap === void 0 ? true : _options$dropCap;
    var direction = options.direction || DEFAULT_DIRECTION;

    if (direction && VALID_DIRECTIONS.indexOf(direction) === -1) {
      throw new TypeError("Invalid direction: " + direction);
    }

    if (direction === VALID_DIRECTIONS[0]) {
      // LR
      indentedTree(root, indent, dropCap);
    } else if (direction === VALID_DIRECTIONS[1]) {
      // RL
      indentedTree(root, indent, dropCap);
      root.right2left();
    } else if (direction === VALID_DIRECTIONS[2]) {
      // H
      // separate into left and right trees
      var _separateTree = separateTree(root, options),
          left = _separateTree.left,
          right = _separateTree.right;

      indentedTree(left, indent, dropCap);
      left.right2left();
      indentedTree(right, indent, dropCap);
      var bbox = left.getBoundingBox();
      right.translate(bbox.width, 0);
      root.x = right.x - root.width / 2;
    }

    return root;
  };

  return IndentedLayout;
}(TreeLayout);

var DEFAULT_OPTIONS = {};

function indentedLayout(root, options) {
  options = util.assign({}, DEFAULT_OPTIONS, options);
  return new IndentedLayout(root, options).execute();
}

module.exports = indentedLayout;

/***/ }),
/* 117 */
/***/ (function(module, exports) {

function positionNode(node, previousNode, dx, dropCap) {
  if (!dropCap) {
    try {
      if (node.id === node.parent.children[0].id) {
        node.x += dx * node.depth;
        node.y = previousNode ? previousNode.y : 0;
        return;
      }
    } catch (e) {// skip to normal when a node has no parent
    }
  }

  node.x += dx * node.depth;
  node.y = previousNode ? previousNode.y + previousNode.height : 0;
  return;
}

module.exports = function (root, indent, dropCap) {
  var previousNode = null;
  root.eachNode(function (node) {
    positionNode(node, previousNode, indent, dropCap);
    previousNode = node;
  });
};

/***/ }),
/* 118 */
/***/ (function(module, exports, __webpack_require__) {

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; subClass.__proto__ = superClass; }

var TreeLayout = __webpack_require__(11);

var mindmap = __webpack_require__(119);

var doTreeLayout = __webpack_require__(17);

var util = __webpack_require__(7);

var MindmapLayout = /*#__PURE__*/function (_TreeLayout) {
  _inheritsLoose(MindmapLayout, _TreeLayout);

  function MindmapLayout() {
    return _TreeLayout.apply(this, arguments) || this;
  }

  var _proto = MindmapLayout.prototype;

  _proto.execute = function execute() {
    var me = this;
    return doTreeLayout(me.rootNode, me.options, mindmap);
  };

  return MindmapLayout;
}(TreeLayout);

var DEFAULT_OPTIONS = {};

function mindmapLayout(root, options) {
  options = util.assign({}, DEFAULT_OPTIONS, options);
  return new MindmapLayout(root, options).execute();
}

module.exports = mindmapLayout;

/***/ }),
/* 119 */
/***/ (function(module, exports, __webpack_require__) {

var util = __webpack_require__(7);

function secondWalk(node, options) {
  var totalHeight = 0;

  if (!node.children.length) {
    totalHeight = node.height;
  } else {
    node.children.forEach(function (c) {
      totalHeight += secondWalk(c, options);
    });
  }

  node._subTreeSep = options.getSubTreeSep(node.data);
  node.totalHeight = Math.max(node.height, totalHeight) + 2 * node._subTreeSep;
  return node.totalHeight;
}

function thirdWalk(node) {
  var children = node.children;
  var len = children.length;

  if (len) {
    children.forEach(function (c) {
      thirdWalk(c);
    });
    var first = children[0];
    var last = children[len - 1];
    var childrenHeight = last.y - first.y + last.height;
    var childrenTotalHeight = 0;
    children.forEach(function (child) {
      childrenTotalHeight += child.totalHeight;
    });

    if (childrenHeight > node.height) {
      // 当子节点总高度大于父节点高度
      node.y = first.y + childrenHeight / 2 - node.height / 2;
    } else if (children.length !== 1 || node.height > childrenTotalHeight) {
      // 多于一个子节点或者父节点大于所有子节点的总高度
      var offset = node.y + (node.height - childrenHeight) / 2 - first.y;
      children.forEach(function (c) {
        c.translate(0, offset);
      });
    } else {
      // 只有一个子节点
      node.y = (first.y + first.height / 2 + last.y + last.height / 2) / 2 - node.height / 2;
    }
  }
}

var DEFAULT_OPTIONS = {
  getSubTreeSep: function getSubTreeSep() {
    return 0;
  }
};

module.exports = function (root, options) {
  if (options === void 0) {
    options = {};
  }

  options = util.assign({}, DEFAULT_OPTIONS, options);
  root.parent = {
    x: 0,
    width: 0,
    height: 0,
    y: 0
  }; // first walk

  root.BFTraverse(function (node) {
    node.x = node.parent.x + node.parent.width; // simply get x
  });
  root.parent = null; // second walk

  secondWalk(root, options); // assign sub tree totalHeight
  // adjusting
  // separating nodes

  root.startY = 0;
  root.y = root.totalHeight / 2 - root.height / 2;
  root.eachNode(function (node) {
    var children = node.children;
    var len = children.length;

    if (len) {
      var first = children[0];
      first.startY = node.startY + node._subTreeSep;

      if (len === 1) {
        first.y = node.y + node.height / 2 - first.height / 2;
      } else {
        first.y = first.startY + first.totalHeight / 2 - first.height / 2;

        for (var i = 1; i < len; i++) {
          var c = children[i];
          c.startY = children[i - 1].startY + children[i - 1].totalHeight;
          c.y = c.startY + c.totalHeight / 2 - c.height / 2;
        }
      }
    }
  }); // third walk

  thirdWalk(root);
};

/***/ })
/******/ ]);
});
//# sourceMappingURL=hierarchy.js.map