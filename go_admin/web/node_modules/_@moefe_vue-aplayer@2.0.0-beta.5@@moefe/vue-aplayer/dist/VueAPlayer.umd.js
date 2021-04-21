(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("vue"));
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["VueAPlayer"] = factory(require("vue"));
	else
		root["VueAPlayer"] = factory(root["Vue"]);
})((typeof self !== 'undefined' ? self : this), function(__WEBPACK_EXTERNAL_MODULE__8bbf__) {
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
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "fb15");
/******/ })
/************************************************************************/
/******/ ({

/***/ "01f9":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var LIBRARY = __webpack_require__("2d00");
var $export = __webpack_require__("5ca1");
var redefine = __webpack_require__("2aba");
var hide = __webpack_require__("32e9");
var Iterators = __webpack_require__("84f2");
var $iterCreate = __webpack_require__("41a0");
var setToStringTag = __webpack_require__("7f20");
var getPrototypeOf = __webpack_require__("38fd");
var ITERATOR = __webpack_require__("2b4c")('iterator');
var BUGGY = !([].keys && 'next' in [].keys()); // Safari has buggy iterators w/o `next`
var FF_ITERATOR = '@@iterator';
var KEYS = 'keys';
var VALUES = 'values';

var returnThis = function () { return this; };

module.exports = function (Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED) {
  $iterCreate(Constructor, NAME, next);
  var getMethod = function (kind) {
    if (!BUGGY && kind in proto) return proto[kind];
    switch (kind) {
      case KEYS: return function keys() { return new Constructor(this, kind); };
      case VALUES: return function values() { return new Constructor(this, kind); };
    } return function entries() { return new Constructor(this, kind); };
  };
  var TAG = NAME + ' Iterator';
  var DEF_VALUES = DEFAULT == VALUES;
  var VALUES_BUG = false;
  var proto = Base.prototype;
  var $native = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT];
  var $default = $native || getMethod(DEFAULT);
  var $entries = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined;
  var $anyNative = NAME == 'Array' ? proto.entries || $native : $native;
  var methods, key, IteratorPrototype;
  // Fix native
  if ($anyNative) {
    IteratorPrototype = getPrototypeOf($anyNative.call(new Base()));
    if (IteratorPrototype !== Object.prototype && IteratorPrototype.next) {
      // Set @@toStringTag to native iterators
      setToStringTag(IteratorPrototype, TAG, true);
      // fix for some old engines
      if (!LIBRARY && typeof IteratorPrototype[ITERATOR] != 'function') hide(IteratorPrototype, ITERATOR, returnThis);
    }
  }
  // fix Array#{values, @@iterator}.name in V8 / FF
  if (DEF_VALUES && $native && $native.name !== VALUES) {
    VALUES_BUG = true;
    $default = function values() { return $native.call(this); };
  }
  // Define iterator
  if ((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])) {
    hide(proto, ITERATOR, $default);
  }
  // Plug for library
  Iterators[NAME] = $default;
  Iterators[TAG] = returnThis;
  if (DEFAULT) {
    methods = {
      values: DEF_VALUES ? $default : getMethod(VALUES),
      keys: IS_SET ? $default : getMethod(KEYS),
      entries: $entries
    };
    if (FORCED) for (key in methods) {
      if (!(key in proto)) redefine(proto, key, methods[key]);
    } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
  }
  return methods;
};


/***/ }),

/***/ "0a49":
/***/ (function(module, exports, __webpack_require__) {

// 0 -> Array#forEach
// 1 -> Array#map
// 2 -> Array#filter
// 3 -> Array#some
// 4 -> Array#every
// 5 -> Array#find
// 6 -> Array#findIndex
var ctx = __webpack_require__("9b43");
var IObject = __webpack_require__("626a");
var toObject = __webpack_require__("4bf8");
var toLength = __webpack_require__("9def");
var asc = __webpack_require__("cd1c");
module.exports = function (TYPE, $create) {
  var IS_MAP = TYPE == 1;
  var IS_FILTER = TYPE == 2;
  var IS_SOME = TYPE == 3;
  var IS_EVERY = TYPE == 4;
  var IS_FIND_INDEX = TYPE == 6;
  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
  var create = $create || asc;
  return function ($this, callbackfn, that) {
    var O = toObject($this);
    var self = IObject(O);
    var f = ctx(callbackfn, that, 3);
    var length = toLength(self.length);
    var index = 0;
    var result = IS_MAP ? create($this, length) : IS_FILTER ? create($this, 0) : undefined;
    var val, res;
    for (;length > index; index++) if (NO_HOLES || index in self) {
      val = self[index];
      res = f(val, index, O);
      if (TYPE) {
        if (IS_MAP) result[index] = res;   // map
        else if (res) switch (TYPE) {
          case 3: return true;             // some
          case 5: return val;              // find
          case 6: return index;            // findIndex
          case 2: result.push(val);        // filter
        } else if (IS_EVERY) return false; // every
      }
    }
    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : result;
  };
};


/***/ }),

/***/ "0bfb":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 21.2.5.3 get RegExp.prototype.flags
var anObject = __webpack_require__("cb7c");
module.exports = function () {
  var that = anObject(this);
  var result = '';
  if (that.global) result += 'g';
  if (that.ignoreCase) result += 'i';
  if (that.multiline) result += 'm';
  if (that.unicode) result += 'u';
  if (that.sticky) result += 'y';
  return result;
};


/***/ }),

/***/ "0d58":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys = __webpack_require__("ce10");
var enumBugKeys = __webpack_require__("e11e");

module.exports = Object.keys || function keys(O) {
  return $keys(O, enumBugKeys);
};


/***/ }),

/***/ "1169":
/***/ (function(module, exports, __webpack_require__) {

// 7.2.2 IsArray(argument)
var cof = __webpack_require__("2d95");
module.exports = Array.isArray || function isArray(arg) {
  return cof(arg) == 'Array';
};


/***/ }),

/***/ "11e9":
/***/ (function(module, exports, __webpack_require__) {

var pIE = __webpack_require__("52a7");
var createDesc = __webpack_require__("4630");
var toIObject = __webpack_require__("6821");
var toPrimitive = __webpack_require__("6a99");
var has = __webpack_require__("69a8");
var IE8_DOM_DEFINE = __webpack_require__("c69a");
var gOPD = Object.getOwnPropertyDescriptor;

exports.f = __webpack_require__("9e1e") ? gOPD : function getOwnPropertyDescriptor(O, P) {
  O = toIObject(O);
  P = toPrimitive(P, true);
  if (IE8_DOM_DEFINE) try {
    return gOPD(O, P);
  } catch (e) { /* empty */ }
  if (has(O, P)) return createDesc(!pIE.f.call(O, P), O[P]);
};


/***/ }),

/***/ "1495":
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__("86cc");
var anObject = __webpack_require__("cb7c");
var getKeys = __webpack_require__("0d58");

module.exports = __webpack_require__("9e1e") ? Object.defineProperties : function defineProperties(O, Properties) {
  anObject(O);
  var keys = getKeys(Properties);
  var length = keys.length;
  var i = 0;
  var P;
  while (length > i) dP.f(O, P = keys[i++], Properties[P]);
  return O;
};


/***/ }),

/***/ "1991":
/***/ (function(module, exports, __webpack_require__) {

var ctx = __webpack_require__("9b43");
var invoke = __webpack_require__("31f4");
var html = __webpack_require__("fab2");
var cel = __webpack_require__("230e");
var global = __webpack_require__("7726");
var process = global.process;
var setTask = global.setImmediate;
var clearTask = global.clearImmediate;
var MessageChannel = global.MessageChannel;
var Dispatch = global.Dispatch;
var counter = 0;
var queue = {};
var ONREADYSTATECHANGE = 'onreadystatechange';
var defer, channel, port;
var run = function () {
  var id = +this;
  // eslint-disable-next-line no-prototype-builtins
  if (queue.hasOwnProperty(id)) {
    var fn = queue[id];
    delete queue[id];
    fn();
  }
};
var listener = function (event) {
  run.call(event.data);
};
// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
if (!setTask || !clearTask) {
  setTask = function setImmediate(fn) {
    var args = [];
    var i = 1;
    while (arguments.length > i) args.push(arguments[i++]);
    queue[++counter] = function () {
      // eslint-disable-next-line no-new-func
      invoke(typeof fn == 'function' ? fn : Function(fn), args);
    };
    defer(counter);
    return counter;
  };
  clearTask = function clearImmediate(id) {
    delete queue[id];
  };
  // Node.js 0.8-
  if (__webpack_require__("2d95")(process) == 'process') {
    defer = function (id) {
      process.nextTick(ctx(run, id, 1));
    };
  // Sphere (JS game engine) Dispatch API
  } else if (Dispatch && Dispatch.now) {
    defer = function (id) {
      Dispatch.now(ctx(run, id, 1));
    };
  // Browsers with MessageChannel, includes WebWorkers
  } else if (MessageChannel) {
    channel = new MessageChannel();
    port = channel.port2;
    channel.port1.onmessage = listener;
    defer = ctx(port.postMessage, port, 1);
  // Browsers with postMessage, skip WebWorkers
  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
  } else if (global.addEventListener && typeof postMessage == 'function' && !global.importScripts) {
    defer = function (id) {
      global.postMessage(id + '', '*');
    };
    global.addEventListener('message', listener, false);
  // IE8-
  } else if (ONREADYSTATECHANGE in cel('script')) {
    defer = function (id) {
      html.appendChild(cel('script'))[ONREADYSTATECHANGE] = function () {
        html.removeChild(this);
        run.call(id);
      };
    };
  // Rest old browsers
  } else {
    defer = function (id) {
      setTimeout(ctx(run, id, 1), 0);
    };
  }
}
module.exports = {
  set: setTask,
  clear: clearTask
};


/***/ }),

/***/ "1fa8":
/***/ (function(module, exports, __webpack_require__) {

// call something on iterator step with safe closing on error
var anObject = __webpack_require__("cb7c");
module.exports = function (iterator, fn, value, entries) {
  try {
    return entries ? fn(anObject(value)[0], value[1]) : fn(value);
  // 7.4.6 IteratorClose(iterator, completion)
  } catch (e) {
    var ret = iterator['return'];
    if (ret !== undefined) anObject(ret.call(iterator));
    throw e;
  }
};


/***/ }),

/***/ "20d6":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 22.1.3.9 Array.prototype.findIndex(predicate, thisArg = undefined)
var $export = __webpack_require__("5ca1");
var $find = __webpack_require__("0a49")(6);
var KEY = 'findIndex';
var forced = true;
// Shouldn't skip holes
if (KEY in []) Array(1)[KEY](function () { forced = false; });
$export($export.P + $export.F * forced, 'Array', {
  findIndex: function findIndex(callbackfn /* , that = undefined */) {
    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});
__webpack_require__("9c6c")(KEY);


/***/ }),

/***/ "214f":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var hide = __webpack_require__("32e9");
var redefine = __webpack_require__("2aba");
var fails = __webpack_require__("79e5");
var defined = __webpack_require__("be13");
var wks = __webpack_require__("2b4c");

module.exports = function (KEY, length, exec) {
  var SYMBOL = wks(KEY);
  var fns = exec(defined, SYMBOL, ''[KEY]);
  var strfn = fns[0];
  var rxfn = fns[1];
  if (fails(function () {
    var O = {};
    O[SYMBOL] = function () { return 7; };
    return ''[KEY](O) != 7;
  })) {
    redefine(String.prototype, KEY, strfn);
    hide(RegExp.prototype, SYMBOL, length == 2
      // 21.2.5.8 RegExp.prototype[@@replace](string, replaceValue)
      // 21.2.5.11 RegExp.prototype[@@split](string, limit)
      ? function (string, arg) { return rxfn.call(string, this, arg); }
      // 21.2.5.6 RegExp.prototype[@@match](string)
      // 21.2.5.9 RegExp.prototype[@@search](string)
      : function (string) { return rxfn.call(string, this); }
    );
  }
};


/***/ }),

/***/ "230e":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("d3f4");
var document = __webpack_require__("7726").document;
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),

/***/ "2350":
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function(useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if(item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function(modules, mediaQuery) {
		if(typeof modules === "string")
			modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for(var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if(typeof id === "number")
				alreadyImportedModules[id] = true;
		}
		for(i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if(typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if(mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if(mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};

function cssWithMappingToString(item, useSourceMap) {
	var content = item[1] || '';
	var cssMapping = item[3];
	if (!cssMapping) {
		return content;
	}

	if (useSourceMap && typeof btoa === 'function') {
		var sourceMapping = toComment(cssMapping);
		var sourceURLs = cssMapping.sources.map(function (source) {
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */'
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}


/***/ }),

/***/ "23c6":
/***/ (function(module, exports, __webpack_require__) {

// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = __webpack_require__("2d95");
var TAG = __webpack_require__("2b4c")('toStringTag');
// ES3 wrong here
var ARG = cof(function () { return arguments; }()) == 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function (it, key) {
  try {
    return it[key];
  } catch (e) { /* empty */ }
};

module.exports = function (it) {
  var O, T, B;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T
    // builtinTag case
    : ARG ? cof(O)
    // ES3 arguments fallback
    : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
};


/***/ }),

/***/ "2621":
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;


/***/ }),

/***/ "27ee":
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__("23c6");
var ITERATOR = __webpack_require__("2b4c")('iterator');
var Iterators = __webpack_require__("84f2");
module.exports = __webpack_require__("8378").getIteratorMethod = function (it) {
  if (it != undefined) return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};


/***/ }),

/***/ "28a5":
/***/ (function(module, exports, __webpack_require__) {

// @@split logic
__webpack_require__("214f")('split', 2, function (defined, SPLIT, $split) {
  'use strict';
  var isRegExp = __webpack_require__("aae3");
  var _split = $split;
  var $push = [].push;
  var $SPLIT = 'split';
  var LENGTH = 'length';
  var LAST_INDEX = 'lastIndex';
  if (
    'abbc'[$SPLIT](/(b)*/)[1] == 'c' ||
    'test'[$SPLIT](/(?:)/, -1)[LENGTH] != 4 ||
    'ab'[$SPLIT](/(?:ab)*/)[LENGTH] != 2 ||
    '.'[$SPLIT](/(.?)(.?)/)[LENGTH] != 4 ||
    '.'[$SPLIT](/()()/)[LENGTH] > 1 ||
    ''[$SPLIT](/.?/)[LENGTH]
  ) {
    var NPCG = /()??/.exec('')[1] === undefined; // nonparticipating capturing group
    // based on es5-shim implementation, need to rework it
    $split = function (separator, limit) {
      var string = String(this);
      if (separator === undefined && limit === 0) return [];
      // If `separator` is not a regex, use native split
      if (!isRegExp(separator)) return _split.call(string, separator, limit);
      var output = [];
      var flags = (separator.ignoreCase ? 'i' : '') +
                  (separator.multiline ? 'm' : '') +
                  (separator.unicode ? 'u' : '') +
                  (separator.sticky ? 'y' : '');
      var lastLastIndex = 0;
      var splitLimit = limit === undefined ? 4294967295 : limit >>> 0;
      // Make `global` and avoid `lastIndex` issues by working with a copy
      var separatorCopy = new RegExp(separator.source, flags + 'g');
      var separator2, match, lastIndex, lastLength, i;
      // Doesn't need flags gy, but they don't hurt
      if (!NPCG) separator2 = new RegExp('^' + separatorCopy.source + '$(?!\\s)', flags);
      while (match = separatorCopy.exec(string)) {
        // `separatorCopy.lastIndex` is not reliable cross-browser
        lastIndex = match.index + match[0][LENGTH];
        if (lastIndex > lastLastIndex) {
          output.push(string.slice(lastLastIndex, match.index));
          // Fix browsers whose `exec` methods don't consistently return `undefined` for NPCG
          // eslint-disable-next-line no-loop-func
          if (!NPCG && match[LENGTH] > 1) match[0].replace(separator2, function () {
            for (i = 1; i < arguments[LENGTH] - 2; i++) if (arguments[i] === undefined) match[i] = undefined;
          });
          if (match[LENGTH] > 1 && match.index < string[LENGTH]) $push.apply(output, match.slice(1));
          lastLength = match[0][LENGTH];
          lastLastIndex = lastIndex;
          if (output[LENGTH] >= splitLimit) break;
        }
        if (separatorCopy[LAST_INDEX] === match.index) separatorCopy[LAST_INDEX]++; // Avoid an infinite loop
      }
      if (lastLastIndex === string[LENGTH]) {
        if (lastLength || !separatorCopy.test('')) output.push('');
      } else output.push(string.slice(lastLastIndex));
      return output[LENGTH] > splitLimit ? output.slice(0, splitLimit) : output;
    };
  // Chakra, V8
  } else if ('0'[$SPLIT](undefined, 0)[LENGTH]) {
    $split = function (separator, limit) {
      return separator === undefined && limit === 0 ? [] : _split.call(this, separator, limit);
    };
  }
  // 21.1.3.17 String.prototype.split(separator, limit)
  return [function split(separator, limit) {
    var O = defined(this);
    var fn = separator == undefined ? undefined : separator[SPLIT];
    return fn !== undefined ? fn.call(separator, O, limit) : $split.call(String(O), separator, limit);
  }, $split];
});


/***/ }),

/***/ "2aba":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("7726");
var hide = __webpack_require__("32e9");
var has = __webpack_require__("69a8");
var SRC = __webpack_require__("ca5a")('src');
var TO_STRING = 'toString';
var $toString = Function[TO_STRING];
var TPL = ('' + $toString).split(TO_STRING);

__webpack_require__("8378").inspectSource = function (it) {
  return $toString.call(it);
};

(module.exports = function (O, key, val, safe) {
  var isFunction = typeof val == 'function';
  if (isFunction) has(val, 'name') || hide(val, 'name', key);
  if (O[key] === val) return;
  if (isFunction) has(val, SRC) || hide(val, SRC, O[key] ? '' + O[key] : TPL.join(String(key)));
  if (O === global) {
    O[key] = val;
  } else if (!safe) {
    delete O[key];
    hide(O, key, val);
  } else if (O[key]) {
    O[key] = val;
  } else {
    hide(O, key, val);
  }
// add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
})(Function.prototype, TO_STRING, function toString() {
  return typeof this == 'function' && this[SRC] || $toString.call(this);
});


/***/ }),

/***/ "2aeb":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
var anObject = __webpack_require__("cb7c");
var dPs = __webpack_require__("1495");
var enumBugKeys = __webpack_require__("e11e");
var IE_PROTO = __webpack_require__("613b")('IE_PROTO');
var Empty = function () { /* empty */ };
var PROTOTYPE = 'prototype';

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var createDict = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = __webpack_require__("230e")('iframe');
  var i = enumBugKeys.length;
  var lt = '<';
  var gt = '>';
  var iframeDocument;
  iframe.style.display = 'none';
  __webpack_require__("fab2").appendChild(iframe);
  iframe.src = 'javascript:'; // eslint-disable-line no-script-url
  // createDict = iframe.contentWindow.Object;
  // html.removeChild(iframe);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
  iframeDocument.close();
  createDict = iframeDocument.F;
  while (i--) delete createDict[PROTOTYPE][enumBugKeys[i]];
  return createDict();
};

module.exports = Object.create || function create(O, Properties) {
  var result;
  if (O !== null) {
    Empty[PROTOTYPE] = anObject(O);
    result = new Empty();
    Empty[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = createDict();
  return Properties === undefined ? result : dPs(result, Properties);
};


/***/ }),

/***/ "2b4c":
/***/ (function(module, exports, __webpack_require__) {

var store = __webpack_require__("5537")('wks');
var uid = __webpack_require__("ca5a");
var Symbol = __webpack_require__("7726").Symbol;
var USE_SYMBOL = typeof Symbol == 'function';

var $exports = module.exports = function (name) {
  return store[name] || (store[name] =
    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
};

$exports.store = store;


/***/ }),

/***/ "2d00":
/***/ (function(module, exports) {

module.exports = false;


/***/ }),

/***/ "2d95":
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};


/***/ }),

/***/ "2e08":
/***/ (function(module, exports, __webpack_require__) {

// https://github.com/tc39/proposal-string-pad-start-end
var toLength = __webpack_require__("9def");
var repeat = __webpack_require__("9744");
var defined = __webpack_require__("be13");

module.exports = function (that, maxLength, fillString, left) {
  var S = String(defined(that));
  var stringLength = S.length;
  var fillStr = fillString === undefined ? ' ' : String(fillString);
  var intMaxLength = toLength(maxLength);
  if (intMaxLength <= stringLength || fillStr == '') return S;
  var fillLen = intMaxLength - stringLength;
  var stringFiller = repeat.call(fillStr, Math.ceil(fillLen / fillStr.length));
  if (stringFiller.length > fillLen) stringFiller = stringFiller.slice(0, fillLen);
  return left ? stringFiller + S : S + stringFiller;
};


/***/ }),

/***/ "2f21":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var fails = __webpack_require__("79e5");

module.exports = function (method, arg) {
  return !!method && fails(function () {
    // eslint-disable-next-line no-useless-call
    arg ? method.call(null, function () { /* empty */ }, 1) : method.call(null);
  });
};


/***/ }),

/***/ "2fdb":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// 21.1.3.7 String.prototype.includes(searchString, position = 0)

var $export = __webpack_require__("5ca1");
var context = __webpack_require__("d2c8");
var INCLUDES = 'includes';

$export($export.P + $export.F * __webpack_require__("5147")(INCLUDES), 'String', {
  includes: function includes(searchString /* , position = 0 */) {
    return !!~context(this, searchString, INCLUDES)
      .indexOf(searchString, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ "31f4":
/***/ (function(module, exports) {

// fast apply, http://jsperf.lnkit.com/fast-apply/5
module.exports = function (fn, args, that) {
  var un = that === undefined;
  switch (args.length) {
    case 0: return un ? fn()
                      : fn.call(that);
    case 1: return un ? fn(args[0])
                      : fn.call(that, args[0]);
    case 2: return un ? fn(args[0], args[1])
                      : fn.call(that, args[0], args[1]);
    case 3: return un ? fn(args[0], args[1], args[2])
                      : fn.call(that, args[0], args[1], args[2]);
    case 4: return un ? fn(args[0], args[1], args[2], args[3])
                      : fn.call(that, args[0], args[1], args[2], args[3]);
  } return fn.apply(that, args);
};


/***/ }),

/***/ "32e9":
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__("86cc");
var createDesc = __webpack_require__("4630");
module.exports = __webpack_require__("9e1e") ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ "33a4":
/***/ (function(module, exports, __webpack_require__) {

// check on default Array iterator
var Iterators = __webpack_require__("84f2");
var ITERATOR = __webpack_require__("2b4c")('iterator');
var ArrayProto = Array.prototype;

module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayProto[ITERATOR] === it);
};


/***/ }),

/***/ "37e7":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 32 32"}},[_c('path',{attrs:{"d":"M25.468 6.947a1.004 1.004 0 0 0-1.03.057L18 11.384V7.831a1.001 1.001 0 0 0-1.562-.827l-12 8.164a1 1 0 0 0 0 1.654l12 8.168A.999.999 0 0 0 18 24.164v-3.556l6.438 4.382A.999.999 0 0 0 26 24.164V7.831c0-.371-.205-.71-.532-.884z"}})])
      }
    
      });
    

/***/ }),

/***/ "3846":
/***/ (function(module, exports, __webpack_require__) {

// 21.2.5.3 get RegExp.prototype.flags()
if (__webpack_require__("9e1e") && /./g.flags != 'g') __webpack_require__("86cc").f(RegExp.prototype, 'flags', {
  configurable: true,
  get: __webpack_require__("0bfb")
});


/***/ }),

/***/ "386b":
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__("5ca1");
var fails = __webpack_require__("79e5");
var defined = __webpack_require__("be13");
var quot = /"/g;
// B.2.3.2.1 CreateHTML(string, tag, attribute, value)
var createHTML = function (string, tag, attribute, value) {
  var S = String(defined(string));
  var p1 = '<' + tag;
  if (attribute !== '') p1 += ' ' + attribute + '="' + String(value).replace(quot, '&quot;') + '"';
  return p1 + '>' + S + '</' + tag + '>';
};
module.exports = function (NAME, exec) {
  var O = {};
  O[NAME] = exec(createHTML);
  $export($export.P + $export.F * fails(function () {
    var test = ''[NAME]('"');
    return test !== test.toLowerCase() || test.split('"').length > 3;
  }), 'String', O);
};


/***/ }),

/***/ "38fd":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
var has = __webpack_require__("69a8");
var toObject = __webpack_require__("4bf8");
var IE_PROTO = __webpack_require__("613b")('IE_PROTO');
var ObjectProto = Object.prototype;

module.exports = Object.getPrototypeOf || function (O) {
  O = toObject(O);
  if (has(O, IE_PROTO)) return O[IE_PROTO];
  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectProto : null;
};


/***/ }),

/***/ "41a0":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var create = __webpack_require__("2aeb");
var descriptor = __webpack_require__("4630");
var setToStringTag = __webpack_require__("7f20");
var IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
__webpack_require__("32e9")(IteratorPrototype, __webpack_require__("2b4c")('iterator'), function () { return this; });

module.exports = function (Constructor, NAME, next) {
  Constructor.prototype = create(IteratorPrototype, { next: descriptor(1, next) });
  setToStringTag(Constructor, NAME + ' Iterator');
};


/***/ }),

/***/ "456d":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 Object.keys(O)
var toObject = __webpack_require__("4bf8");
var $keys = __webpack_require__("0d58");

__webpack_require__("5eda")('keys', function () {
  return function keys(it) {
    return $keys(toObject(it));
  };
});


/***/ }),

/***/ "4588":
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil = Math.ceil;
var floor = Math.floor;
module.exports = function (it) {
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};


/***/ }),

/***/ "4630":
/***/ (function(module, exports) {

module.exports = function (bitmap, value) {
  return {
    enumerable: !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable: !(bitmap & 4),
    value: value
  };
};


/***/ }),

/***/ "4713":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 32 32"}},[_c('path',{attrs:{"d":"M26.667 5.333H5.334h-.001a2.667 2.667 0 0 0-2.666 2.666V24.001a2.667 2.667 0 0 0 2.666 2.666h21.335a2.667 2.667 0 0 0 2.666-2.666V8v-.001a2.667 2.667 0 0 0-2.666-2.666h-.001zM5.333 16h5.333v2.667H5.333V16zm13.334 8H5.334v-2.667h13.333V24zm8 0h-5.333v-2.667h5.333V24zm0-5.333H13.334V16h13.333v2.667z"}})])
      }
    
      });
    

/***/ }),

/***/ "475a":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 32 32"}},[_c('path',{attrs:{"d":"M.622 18.334h19.54v7.55l11.052-9.412-11.052-9.413v7.549H.622v3.725z"}})])
      }
    
      });
    

/***/ }),

/***/ "48d3":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

function __export(m) {
    for (var p in m) if (!exports.hasOwnProperty(p)) exports[p] = m[p];
}
Object.defineProperty(exports, "__esModule", { value: true });
__export(__webpack_require__("b349"));
var modifiers_1 = __webpack_require__("66a1");
exports.modifiers = modifiers_1.modifiers;
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiaW5kZXguanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlcyI6WyIuLi9zcmMvaW5kZXgudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7Ozs7QUFBQSwyQkFBc0I7QUFDdEIseUNBQXdDO0FBQS9CLGdDQUFBLFNBQVMsQ0FBQSJ9

/***/ }),

/***/ "4917":
/***/ (function(module, exports, __webpack_require__) {

// @@match logic
__webpack_require__("214f")('match', 1, function (defined, MATCH, $match) {
  // 21.1.3.11 String.prototype.match(regexp)
  return [function match(regexp) {
    'use strict';
    var O = defined(this);
    var fn = regexp == undefined ? undefined : regexp[MATCH];
    return fn !== undefined ? fn.call(regexp, O) : new RegExp(regexp)[MATCH](String(O));
  }, $match];
});


/***/ }),

/***/ "499e":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: ./node_modules/vue-style-loader/lib/listToStyles.js
/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */
function listToStyles (parentId, list) {
  var styles = []
  var newStyles = {}
  for (var i = 0; i < list.length; i++) {
    var item = list[i]
    var id = item[0]
    var css = item[1]
    var media = item[2]
    var sourceMap = item[3]
    var part = {
      id: parentId + ':' + i,
      css: css,
      media: media,
      sourceMap: sourceMap
    }
    if (!newStyles[id]) {
      styles.push(newStyles[id] = { id: id, parts: [part] })
    } else {
      newStyles[id].parts.push(part)
    }
  }
  return styles
}

// CONCATENATED MODULE: ./node_modules/vue-style-loader/lib/addStylesClient.js
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return addStylesClient; });
/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/



var hasDocument = typeof document !== 'undefined'

if (typeof DEBUG !== 'undefined' && DEBUG) {
  if (!hasDocument) {
    throw new Error(
    'vue-style-loader cannot be used in a non-browser environment. ' +
    "Use { target: 'node' } in your Webpack config to indicate a server-rendering environment."
  ) }
}

/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/

var stylesInDom = {/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/}

var head = hasDocument && (document.head || document.getElementsByTagName('head')[0])
var singletonElement = null
var singletonCounter = 0
var isProduction = false
var noop = function () {}
var options = null
var ssrIdKey = 'data-vue-ssr-id'

// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE = typeof navigator !== 'undefined' && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

function addStylesClient (parentId, list, _isProduction, _options) {
  isProduction = _isProduction

  options = _options || {}

  var styles = listToStyles(parentId, list)
  addStylesToDom(styles)

  return function update (newList) {
    var mayRemove = []
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i]
      var domStyle = stylesInDom[item.id]
      domStyle.refs--
      mayRemove.push(domStyle)
    }
    if (newList) {
      styles = listToStyles(parentId, newList)
      addStylesToDom(styles)
    } else {
      styles = []
    }
    for (var i = 0; i < mayRemove.length; i++) {
      var domStyle = mayRemove[i]
      if (domStyle.refs === 0) {
        for (var j = 0; j < domStyle.parts.length; j++) {
          domStyle.parts[j]()
        }
        delete stylesInDom[domStyle.id]
      }
    }
  }
}

function addStylesToDom (styles /* Array<StyleObject> */) {
  for (var i = 0; i < styles.length; i++) {
    var item = styles[i]
    var domStyle = stylesInDom[item.id]
    if (domStyle) {
      domStyle.refs++
      for (var j = 0; j < domStyle.parts.length; j++) {
        domStyle.parts[j](item.parts[j])
      }
      for (; j < item.parts.length; j++) {
        domStyle.parts.push(addStyle(item.parts[j]))
      }
      if (domStyle.parts.length > item.parts.length) {
        domStyle.parts.length = item.parts.length
      }
    } else {
      var parts = []
      for (var j = 0; j < item.parts.length; j++) {
        parts.push(addStyle(item.parts[j]))
      }
      stylesInDom[item.id] = { id: item.id, refs: 1, parts: parts }
    }
  }
}

function createStyleElement () {
  var styleElement = document.createElement('style')
  styleElement.type = 'text/css'
  head.appendChild(styleElement)
  return styleElement
}

function addStyle (obj /* StyleObjectPart */) {
  var update, remove
  var styleElement = document.querySelector('style[' + ssrIdKey + '~="' + obj.id + '"]')

  if (styleElement) {
    if (isProduction) {
      // has SSR styles and in production mode.
      // simply do nothing.
      return noop
    } else {
      // has SSR styles but in dev mode.
      // for some reason Chrome can't handle source map in server-rendered
      // style tags - source maps in <style> only works if the style tag is
      // created and inserted dynamically. So we remove the server rendered
      // styles and inject new ones.
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  if (isOldIE) {
    // use singleton mode for IE9.
    var styleIndex = singletonCounter++
    styleElement = singletonElement || (singletonElement = createStyleElement())
    update = applyToSingletonTag.bind(null, styleElement, styleIndex, false)
    remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true)
  } else {
    // use multi-style-tag mode in all other cases
    styleElement = createStyleElement()
    update = applyToTag.bind(null, styleElement)
    remove = function () {
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  update(obj)

  return function updateStyle (newObj /* StyleObjectPart */) {
    if (newObj) {
      if (newObj.css === obj.css &&
          newObj.media === obj.media &&
          newObj.sourceMap === obj.sourceMap) {
        return
      }
      update(obj = newObj)
    } else {
      remove()
    }
  }
}

var replaceText = (function () {
  var textStore = []

  return function (index, replacement) {
    textStore[index] = replacement
    return textStore.filter(Boolean).join('\n')
  }
})()

function applyToSingletonTag (styleElement, index, remove, obj) {
  var css = remove ? '' : obj.css

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = replaceText(index, css)
  } else {
    var cssNode = document.createTextNode(css)
    var childNodes = styleElement.childNodes
    if (childNodes[index]) styleElement.removeChild(childNodes[index])
    if (childNodes.length) {
      styleElement.insertBefore(cssNode, childNodes[index])
    } else {
      styleElement.appendChild(cssNode)
    }
  }
}

function applyToTag (styleElement, obj) {
  var css = obj.css
  var media = obj.media
  var sourceMap = obj.sourceMap

  if (media) {
    styleElement.setAttribute('media', media)
  }
  if (options.ssrId) {
    styleElement.setAttribute(ssrIdKey, obj.id)
  }

  if (sourceMap) {
    // https://developer.chrome.com/devtools/docs/javascript-debugging
    // this makes source maps inside style tags work properly in Chrome
    css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */'
    // http://stackoverflow.com/a/26603875
    css += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + ' */'
  }

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild)
    }
    styleElement.appendChild(document.createTextNode(css))
  }
}


/***/ }),

/***/ "4a59":
/***/ (function(module, exports, __webpack_require__) {

var ctx = __webpack_require__("9b43");
var call = __webpack_require__("1fa8");
var isArrayIter = __webpack_require__("33a4");
var anObject = __webpack_require__("cb7c");
var toLength = __webpack_require__("9def");
var getIterFn = __webpack_require__("27ee");
var BREAK = {};
var RETURN = {};
var exports = module.exports = function (iterable, entries, fn, that, ITERATOR) {
  var iterFn = ITERATOR ? function () { return iterable; } : getIterFn(iterable);
  var f = ctx(fn, that, entries ? 2 : 1);
  var index = 0;
  var length, step, iterator, result;
  if (typeof iterFn != 'function') throw TypeError(iterable + ' is not iterable!');
  // fast case for arrays with default iterator
  if (isArrayIter(iterFn)) for (length = toLength(iterable.length); length > index; index++) {
    result = entries ? f(anObject(step = iterable[index])[0], step[1]) : f(iterable[index]);
    if (result === BREAK || result === RETURN) return result;
  } else for (iterator = iterFn.call(iterable); !(step = iterator.next()).done;) {
    result = call(iterator, f, step.value, entries);
    if (result === BREAK || result === RETURN) return result;
  }
};
exports.BREAK = BREAK;
exports.RETURN = RETURN;


/***/ }),

/***/ "4b41":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 28 32"}},[_c('path',{attrs:{"d":"M13.728 6.272v19.456q0 .448-.352.8t-.8.32-.8-.32l-5.952-5.952H1.152q-.48 0-.8-.352t-.352-.8v-6.848q0-.48.352-.8t.8-.352h4.672l5.952-5.952q.32-.32.8-.32t.8.32.352.8zM20.576 16q0 1.344-.768 2.528t-2.016 1.664q-.16.096-.448.096-.448 0-.8-.32t-.32-.832q0-.384.192-.64t.544-.448.608-.384.512-.64.192-1.024-.192-1.024-.512-.64-.608-.384-.544-.448-.192-.64q0-.48.32-.832t.8-.32q.288 0 .448.096 1.248.48 2.016 1.664T20.576 16zm4.576 0q0 2.72-1.536 5.056t-4 3.36q-.256.096-.448.096-.48 0-.832-.352t-.32-.8q0-.704.672-1.056 1.024-.512 1.376-.8 1.312-.96 2.048-2.4T22.848 16t-.736-3.104-2.048-2.4q-.352-.288-1.376-.8-.672-.352-.672-1.056 0-.448.32-.8t.8-.352q.224 0 .48.096 2.496 1.056 4 3.36T25.152 16zm4.576 0q0 4.096-2.272 7.552t-6.048 5.056q-.224.096-.448.096-.48 0-.832-.352t-.32-.8q0-.64.704-1.056l.384-.192q.256-.128.416-.192.8-.448 1.44-.896 2.208-1.632 3.456-4.064T27.424 16t-1.216-5.152-3.456-4.064q-.64-.448-1.44-.896-.128-.096-.416-.192t-.384-.192q-.704-.416-.704-1.056 0-.448.32-.8t.832-.352q.224 0 .448.096 3.776 1.632 6.048 5.056T29.728 16z"}})])
      }
    
      });
    

/***/ }),

/***/ "4bf8":
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__("be13");
module.exports = function (it) {
  return Object(defined(it));
};


/***/ }),

/***/ "4d26":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2017 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames () {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg) && arg.length) {
				var inner = classNames.apply(null, arg);
				if (inner) {
					classes.push(inner);
				}
			} else if (argType === 'object') {
				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ "5147":
/***/ (function(module, exports, __webpack_require__) {

var MATCH = __webpack_require__("2b4c")('match');
module.exports = function (KEY) {
  var re = /./;
  try {
    '/./'[KEY](re);
  } catch (e) {
    try {
      re[MATCH] = false;
      return !'/./'[KEY](re);
    } catch (f) { /* empty */ }
  } return true;
};


/***/ }),

/***/ "52a7":
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;


/***/ }),

/***/ "52f0":
/***/ (function(module, exports, __webpack_require__) {

var map = {
	"./loading.svg": "885d",
	"./loop-all.svg": "f866",
	"./loop-none.svg": "c3ab",
	"./loop-one.svg": "5527",
	"./lrc.svg": "4713",
	"./menu.svg": "906b",
	"./order-list.svg": "475a",
	"./order-random.svg": "7a1a",
	"./pause.svg": "daf8",
	"./play.svg": "84d8",
	"./right.svg": "bf5c",
	"./skip.svg": "37e7",
	"./volume-down.svg": "bdba",
	"./volume-off.svg": "adec",
	"./volume-up.svg": "4b41"
};


function webpackContext(req) {
	var id = webpackContextResolve(req);
	return __webpack_require__(id);
}
function webpackContextResolve(req) {
	var id = map[req];
	if(!(id + 1)) { // check for number or string
		var e = new Error("Cannot find module '" + req + "'");
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	}
	return id;
}
webpackContext.keys = function webpackContextKeys() {
	return Object.keys(map);
};
webpackContext.resolve = webpackContextResolve;
module.exports = webpackContext;
webpackContext.id = "52f0";

/***/ }),

/***/ "551c":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var LIBRARY = __webpack_require__("2d00");
var global = __webpack_require__("7726");
var ctx = __webpack_require__("9b43");
var classof = __webpack_require__("23c6");
var $export = __webpack_require__("5ca1");
var isObject = __webpack_require__("d3f4");
var aFunction = __webpack_require__("d8e8");
var anInstance = __webpack_require__("f605");
var forOf = __webpack_require__("4a59");
var speciesConstructor = __webpack_require__("ebd6");
var task = __webpack_require__("1991").set;
var microtask = __webpack_require__("8079")();
var newPromiseCapabilityModule = __webpack_require__("a5b8");
var perform = __webpack_require__("9c80");
var userAgent = __webpack_require__("a25f");
var promiseResolve = __webpack_require__("bcaa");
var PROMISE = 'Promise';
var TypeError = global.TypeError;
var process = global.process;
var versions = process && process.versions;
var v8 = versions && versions.v8 || '';
var $Promise = global[PROMISE];
var isNode = classof(process) == 'process';
var empty = function () { /* empty */ };
var Internal, newGenericPromiseCapability, OwnPromiseCapability, Wrapper;
var newPromiseCapability = newGenericPromiseCapability = newPromiseCapabilityModule.f;

var USE_NATIVE = !!function () {
  try {
    // correct subclassing with @@species support
    var promise = $Promise.resolve(1);
    var FakePromise = (promise.constructor = {})[__webpack_require__("2b4c")('species')] = function (exec) {
      exec(empty, empty);
    };
    // unhandled rejections tracking support, NodeJS Promise without it fails @@species test
    return (isNode || typeof PromiseRejectionEvent == 'function')
      && promise.then(empty) instanceof FakePromise
      // v8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
      // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
      // we can't detect it synchronously, so just check versions
      && v8.indexOf('6.6') !== 0
      && userAgent.indexOf('Chrome/66') === -1;
  } catch (e) { /* empty */ }
}();

// helpers
var isThenable = function (it) {
  var then;
  return isObject(it) && typeof (then = it.then) == 'function' ? then : false;
};
var notify = function (promise, isReject) {
  if (promise._n) return;
  promise._n = true;
  var chain = promise._c;
  microtask(function () {
    var value = promise._v;
    var ok = promise._s == 1;
    var i = 0;
    var run = function (reaction) {
      var handler = ok ? reaction.ok : reaction.fail;
      var resolve = reaction.resolve;
      var reject = reaction.reject;
      var domain = reaction.domain;
      var result, then, exited;
      try {
        if (handler) {
          if (!ok) {
            if (promise._h == 2) onHandleUnhandled(promise);
            promise._h = 1;
          }
          if (handler === true) result = value;
          else {
            if (domain) domain.enter();
            result = handler(value); // may throw
            if (domain) {
              domain.exit();
              exited = true;
            }
          }
          if (result === reaction.promise) {
            reject(TypeError('Promise-chain cycle'));
          } else if (then = isThenable(result)) {
            then.call(result, resolve, reject);
          } else resolve(result);
        } else reject(value);
      } catch (e) {
        if (domain && !exited) domain.exit();
        reject(e);
      }
    };
    while (chain.length > i) run(chain[i++]); // variable length - can't use forEach
    promise._c = [];
    promise._n = false;
    if (isReject && !promise._h) onUnhandled(promise);
  });
};
var onUnhandled = function (promise) {
  task.call(global, function () {
    var value = promise._v;
    var unhandled = isUnhandled(promise);
    var result, handler, console;
    if (unhandled) {
      result = perform(function () {
        if (isNode) {
          process.emit('unhandledRejection', value, promise);
        } else if (handler = global.onunhandledrejection) {
          handler({ promise: promise, reason: value });
        } else if ((console = global.console) && console.error) {
          console.error('Unhandled promise rejection', value);
        }
      });
      // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
      promise._h = isNode || isUnhandled(promise) ? 2 : 1;
    } promise._a = undefined;
    if (unhandled && result.e) throw result.v;
  });
};
var isUnhandled = function (promise) {
  return promise._h !== 1 && (promise._a || promise._c).length === 0;
};
var onHandleUnhandled = function (promise) {
  task.call(global, function () {
    var handler;
    if (isNode) {
      process.emit('rejectionHandled', promise);
    } else if (handler = global.onrejectionhandled) {
      handler({ promise: promise, reason: promise._v });
    }
  });
};
var $reject = function (value) {
  var promise = this;
  if (promise._d) return;
  promise._d = true;
  promise = promise._w || promise; // unwrap
  promise._v = value;
  promise._s = 2;
  if (!promise._a) promise._a = promise._c.slice();
  notify(promise, true);
};
var $resolve = function (value) {
  var promise = this;
  var then;
  if (promise._d) return;
  promise._d = true;
  promise = promise._w || promise; // unwrap
  try {
    if (promise === value) throw TypeError("Promise can't be resolved itself");
    if (then = isThenable(value)) {
      microtask(function () {
        var wrapper = { _w: promise, _d: false }; // wrap
        try {
          then.call(value, ctx($resolve, wrapper, 1), ctx($reject, wrapper, 1));
        } catch (e) {
          $reject.call(wrapper, e);
        }
      });
    } else {
      promise._v = value;
      promise._s = 1;
      notify(promise, false);
    }
  } catch (e) {
    $reject.call({ _w: promise, _d: false }, e); // wrap
  }
};

// constructor polyfill
if (!USE_NATIVE) {
  // 25.4.3.1 Promise(executor)
  $Promise = function Promise(executor) {
    anInstance(this, $Promise, PROMISE, '_h');
    aFunction(executor);
    Internal.call(this);
    try {
      executor(ctx($resolve, this, 1), ctx($reject, this, 1));
    } catch (err) {
      $reject.call(this, err);
    }
  };
  // eslint-disable-next-line no-unused-vars
  Internal = function Promise(executor) {
    this._c = [];             // <- awaiting reactions
    this._a = undefined;      // <- checked in isUnhandled reactions
    this._s = 0;              // <- state
    this._d = false;          // <- done
    this._v = undefined;      // <- value
    this._h = 0;              // <- rejection state, 0 - default, 1 - handled, 2 - unhandled
    this._n = false;          // <- notify
  };
  Internal.prototype = __webpack_require__("dcbc")($Promise.prototype, {
    // 25.4.5.3 Promise.prototype.then(onFulfilled, onRejected)
    then: function then(onFulfilled, onRejected) {
      var reaction = newPromiseCapability(speciesConstructor(this, $Promise));
      reaction.ok = typeof onFulfilled == 'function' ? onFulfilled : true;
      reaction.fail = typeof onRejected == 'function' && onRejected;
      reaction.domain = isNode ? process.domain : undefined;
      this._c.push(reaction);
      if (this._a) this._a.push(reaction);
      if (this._s) notify(this, false);
      return reaction.promise;
    },
    // 25.4.5.1 Promise.prototype.catch(onRejected)
    'catch': function (onRejected) {
      return this.then(undefined, onRejected);
    }
  });
  OwnPromiseCapability = function () {
    var promise = new Internal();
    this.promise = promise;
    this.resolve = ctx($resolve, promise, 1);
    this.reject = ctx($reject, promise, 1);
  };
  newPromiseCapabilityModule.f = newPromiseCapability = function (C) {
    return C === $Promise || C === Wrapper
      ? new OwnPromiseCapability(C)
      : newGenericPromiseCapability(C);
  };
}

$export($export.G + $export.W + $export.F * !USE_NATIVE, { Promise: $Promise });
__webpack_require__("7f20")($Promise, PROMISE);
__webpack_require__("7a56")(PROMISE);
Wrapper = __webpack_require__("8378")[PROMISE];

// statics
$export($export.S + $export.F * !USE_NATIVE, PROMISE, {
  // 25.4.4.5 Promise.reject(r)
  reject: function reject(r) {
    var capability = newPromiseCapability(this);
    var $$reject = capability.reject;
    $$reject(r);
    return capability.promise;
  }
});
$export($export.S + $export.F * (LIBRARY || !USE_NATIVE), PROMISE, {
  // 25.4.4.6 Promise.resolve(x)
  resolve: function resolve(x) {
    return promiseResolve(LIBRARY && this === Wrapper ? $Promise : this, x);
  }
});
$export($export.S + $export.F * !(USE_NATIVE && __webpack_require__("5cc5")(function (iter) {
  $Promise.all(iter)['catch'](empty);
})), PROMISE, {
  // 25.4.4.1 Promise.all(iterable)
  all: function all(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var resolve = capability.resolve;
    var reject = capability.reject;
    var result = perform(function () {
      var values = [];
      var index = 0;
      var remaining = 1;
      forOf(iterable, false, function (promise) {
        var $index = index++;
        var alreadyCalled = false;
        values.push(undefined);
        remaining++;
        C.resolve(promise).then(function (value) {
          if (alreadyCalled) return;
          alreadyCalled = true;
          values[$index] = value;
          --remaining || resolve(values);
        }, reject);
      });
      --remaining || resolve(values);
    });
    if (result.e) reject(result.v);
    return capability.promise;
  },
  // 25.4.4.4 Promise.race(iterable)
  race: function race(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var reject = capability.reject;
    var result = perform(function () {
      forOf(iterable, false, function (promise) {
        C.resolve(promise).then(capability.resolve, reject);
      });
    });
    if (result.e) reject(result.v);
    return capability.promise;
  }
});


/***/ }),

/***/ "5527":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 33 32"}},[_c('path',{attrs:{"d":"M9.333 9.333h13.333v4L27.999 8l-5.333-5.333v4h-16v8h2.667V9.334zm13.334 13.334H9.334v-4L4.001 24l5.333 5.333v-4h16v-8h-2.667v5.333zM17.333 20v-8H16l-2.667 1.333v1.333h2v5.333h2z"}})])
      }
    
      });
    

/***/ }),

/***/ "5537":
/***/ (function(module, exports, __webpack_require__) {

var core = __webpack_require__("8378");
var global = __webpack_require__("7726");
var SHARED = '__core-js_shared__';
var store = global[SHARED] || (global[SHARED] = {});

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: core.version,
  mode: __webpack_require__("2d00") ? 'pure' : 'global',
  copyright: '© 2018 Denis Pushkarev (zloirock.ru)'
});


/***/ }),

/***/ "55dd":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__("5ca1");
var aFunction = __webpack_require__("d8e8");
var toObject = __webpack_require__("4bf8");
var fails = __webpack_require__("79e5");
var $sort = [].sort;
var test = [1, 2, 3];

$export($export.P + $export.F * (fails(function () {
  // IE8-
  test.sort(undefined);
}) || !fails(function () {
  // V8 bug
  test.sort(null);
  // Old WebKit
}) || !__webpack_require__("2f21")($sort)), 'Array', {
  // 22.1.3.25 Array.prototype.sort(comparefn)
  sort: function sort(comparefn) {
    return comparefn === undefined
      ? $sort.call(toObject(this))
      : $sort.call(toObject(this), aFunction(comparefn));
  }
});


/***/ }),

/***/ "5ca1":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("7726");
var core = __webpack_require__("8378");
var hide = __webpack_require__("32e9");
var redefine = __webpack_require__("2aba");
var ctx = __webpack_require__("9b43");
var PROTOTYPE = 'prototype';

var $export = function (type, name, source) {
  var IS_FORCED = type & $export.F;
  var IS_GLOBAL = type & $export.G;
  var IS_STATIC = type & $export.S;
  var IS_PROTO = type & $export.P;
  var IS_BIND = type & $export.B;
  var target = IS_GLOBAL ? global : IS_STATIC ? global[name] || (global[name] = {}) : (global[name] || {})[PROTOTYPE];
  var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
  var expProto = exports[PROTOTYPE] || (exports[PROTOTYPE] = {});
  var key, own, out, exp;
  if (IS_GLOBAL) source = name;
  for (key in source) {
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    // export native or passed
    out = (own ? target : source)[key];
    // bind timers to global for call from export context
    exp = IS_BIND && own ? ctx(out, global) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // extend global
    if (target) redefine(target, key, out, type & $export.U);
    // export
    if (exports[key] != out) hide(exports, key, exp);
    if (IS_PROTO && expProto[key] != out) expProto[key] = out;
  }
};
global.core = core;
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library`
module.exports = $export;


/***/ }),

/***/ "5cc5":
/***/ (function(module, exports, __webpack_require__) {

var ITERATOR = __webpack_require__("2b4c")('iterator');
var SAFE_CLOSING = false;

try {
  var riter = [7][ITERATOR]();
  riter['return'] = function () { SAFE_CLOSING = true; };
  // eslint-disable-next-line no-throw-literal
  Array.from(riter, function () { throw 2; });
} catch (e) { /* empty */ }

module.exports = function (exec, skipClosing) {
  if (!skipClosing && !SAFE_CLOSING) return false;
  var safe = false;
  try {
    var arr = [7];
    var iter = arr[ITERATOR]();
    iter.next = function () { return { done: safe = true }; };
    arr[ITERATOR] = function () { return iter; };
    exec(arr);
  } catch (e) { /* empty */ }
  return safe;
};


/***/ }),

/***/ "5dbc":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("d3f4");
var setPrototypeOf = __webpack_require__("8b97").set;
module.exports = function (that, target, C) {
  var S = target.constructor;
  var P;
  if (S !== C && typeof S == 'function' && (P = S.prototype) !== C.prototype && isObject(P) && setPrototypeOf) {
    setPrototypeOf(that, P);
  } return that;
};


/***/ }),

/***/ "5eda":
/***/ (function(module, exports, __webpack_require__) {

// most Object methods by ES6 should accept primitives
var $export = __webpack_require__("5ca1");
var core = __webpack_require__("8378");
var fails = __webpack_require__("79e5");
module.exports = function (KEY, exec) {
  var fn = (core.Object || {})[KEY] || Object[KEY];
  var exp = {};
  exp[KEY] = exec(fn);
  $export($export.S + $export.F * fails(function () { fn(1); }), 'Object', exp);
};


/***/ }),

/***/ "610a":
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__("ab57");
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var add = __webpack_require__("499e").default
var update = add("4573c8b0", content, true, {"sourceMap":false,"shadowMode":false});

/***/ }),

/***/ "613b":
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__("5537")('keys');
var uid = __webpack_require__("ca5a");
module.exports = function (key) {
  return shared[key] || (shared[key] = uid(key));
};


/***/ }),

/***/ "626a":
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__("2d95");
// eslint-disable-next-line no-prototype-builtins
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
  return cof(it) == 'String' ? it.split('') : Object(it);
};


/***/ }),

/***/ "65d9":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
  * vue-class-component v6.3.2
  * (c) 2015-present Evan You
  * @license MIT
  */


Object.defineProperty(exports, '__esModule', { value: true });

function _interopDefault (ex) { return (ex && (typeof ex === 'object') && 'default' in ex) ? ex['default'] : ex; }

var Vue = _interopDefault(__webpack_require__("8bbf"));

var reflectionIsSupported = typeof Reflect !== 'undefined' && Reflect.defineMetadata;
function copyReflectionMetadata(to, from) {
    forwardMetadata(to, from);
    Object.getOwnPropertyNames(from.prototype).forEach(function (key) {
        forwardMetadata(to.prototype, from.prototype, key);
    });
    Object.getOwnPropertyNames(from).forEach(function (key) {
        forwardMetadata(to, from, key);
    });
}
function forwardMetadata(to, from, propertyKey) {
    var metaKeys = propertyKey
        ? Reflect.getOwnMetadataKeys(from, propertyKey)
        : Reflect.getOwnMetadataKeys(from);
    metaKeys.forEach(function (metaKey) {
        var metadata = propertyKey
            ? Reflect.getOwnMetadata(metaKey, from, propertyKey)
            : Reflect.getOwnMetadata(metaKey, from);
        if (propertyKey) {
            Reflect.defineMetadata(metaKey, metadata, to, propertyKey);
        }
        else {
            Reflect.defineMetadata(metaKey, metadata, to);
        }
    });
}

var fakeArray = { __proto__: [] };
var hasProto = fakeArray instanceof Array;
function createDecorator(factory) {
    return function (target, key, index) {
        var Ctor = typeof target === 'function'
            ? target
            : target.constructor;
        if (!Ctor.__decorators__) {
            Ctor.__decorators__ = [];
        }
        if (typeof index !== 'number') {
            index = undefined;
        }
        Ctor.__decorators__.push(function (options) { return factory(options, key, index); });
    };
}
function mixins() {
    var Ctors = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        Ctors[_i] = arguments[_i];
    }
    return Vue.extend({ mixins: Ctors });
}
function isPrimitive(value) {
    var type = typeof value;
    return value == null || (type !== 'object' && type !== 'function');
}
function warn(message) {
    if (typeof console !== 'undefined') {
        console.warn('[vue-class-component] ' + message);
    }
}

function collectDataFromConstructor(vm, Component) {
    // override _init to prevent to init as Vue instance
    var originalInit = Component.prototype._init;
    Component.prototype._init = function () {
        var _this = this;
        // proxy to actual vm
        var keys = Object.getOwnPropertyNames(vm);
        // 2.2.0 compat (props are no longer exposed as self properties)
        if (vm.$options.props) {
            for (var key in vm.$options.props) {
                if (!vm.hasOwnProperty(key)) {
                    keys.push(key);
                }
            }
        }
        keys.forEach(function (key) {
            if (key.charAt(0) !== '_') {
                Object.defineProperty(_this, key, {
                    get: function () { return vm[key]; },
                    set: function (value) { vm[key] = value; },
                    configurable: true
                });
            }
        });
    };
    // should be acquired class property values
    var data = new Component();
    // restore original _init to avoid memory leak (#209)
    Component.prototype._init = originalInit;
    // create plain data object
    var plainData = {};
    Object.keys(data).forEach(function (key) {
        if (data[key] !== undefined) {
            plainData[key] = data[key];
        }
    });
    if (false) {}
    return plainData;
}

var $internalHooks = [
    'data',
    'beforeCreate',
    'created',
    'beforeMount',
    'mounted',
    'beforeDestroy',
    'destroyed',
    'beforeUpdate',
    'updated',
    'activated',
    'deactivated',
    'render',
    'errorCaptured' // 2.5
];
function componentFactory(Component, options) {
    if (options === void 0) { options = {}; }
    options.name = options.name || Component._componentTag || Component.name;
    // prototype props.
    var proto = Component.prototype;
    Object.getOwnPropertyNames(proto).forEach(function (key) {
        if (key === 'constructor') {
            return;
        }
        // hooks
        if ($internalHooks.indexOf(key) > -1) {
            options[key] = proto[key];
            return;
        }
        var descriptor = Object.getOwnPropertyDescriptor(proto, key);
        if (descriptor.value !== void 0) {
            // methods
            if (typeof descriptor.value === 'function') {
                (options.methods || (options.methods = {}))[key] = descriptor.value;
            }
            else {
                // typescript decorated data
                (options.mixins || (options.mixins = [])).push({
                    data: function () {
                        var _a;
                        return _a = {}, _a[key] = descriptor.value, _a;
                    }
                });
            }
        }
        else if (descriptor.get || descriptor.set) {
            // computed properties
            (options.computed || (options.computed = {}))[key] = {
                get: descriptor.get,
                set: descriptor.set
            };
        }
    });
    (options.mixins || (options.mixins = [])).push({
        data: function () {
            return collectDataFromConstructor(this, Component);
        }
    });
    // decorate options
    var decorators = Component.__decorators__;
    if (decorators) {
        decorators.forEach(function (fn) { return fn(options); });
        delete Component.__decorators__;
    }
    // find super
    var superProto = Object.getPrototypeOf(Component.prototype);
    var Super = superProto instanceof Vue
        ? superProto.constructor
        : Vue;
    var Extended = Super.extend(options);
    forwardStaticMembers(Extended, Component, Super);
    if (reflectionIsSupported) {
        copyReflectionMetadata(Extended, Component);
    }
    return Extended;
}
var reservedPropertyNames = [
    // Unique id
    'cid',
    // Super Vue constructor
    'super',
    // Component options that will be used by the component
    'options',
    'superOptions',
    'extendOptions',
    'sealedOptions',
    // Private assets
    'component',
    'directive',
    'filter'
];
function forwardStaticMembers(Extended, Original, Super) {
    // We have to use getOwnPropertyNames since Babel registers methods as non-enumerable
    Object.getOwnPropertyNames(Original).forEach(function (key) {
        // `prototype` should not be overwritten
        if (key === 'prototype') {
            return;
        }
        // Some browsers does not allow reconfigure built-in properties
        var extendedDescriptor = Object.getOwnPropertyDescriptor(Extended, key);
        if (extendedDescriptor && !extendedDescriptor.configurable) {
            return;
        }
        var descriptor = Object.getOwnPropertyDescriptor(Original, key);
        // If the user agent does not support `__proto__` or its family (IE <= 10),
        // the sub class properties may be inherited properties from the super class in TypeScript.
        // We need to exclude such properties to prevent to overwrite
        // the component options object which stored on the extended constructor (See #192).
        // If the value is a referenced value (object or function),
        // we can check equality of them and exclude it if they have the same reference.
        // If it is a primitive value, it will be forwarded for safety.
        if (!hasProto) {
            // Only `cid` is explicitly exluded from property forwarding
            // because we cannot detect whether it is a inherited property or not
            // on the no `__proto__` environment even though the property is reserved.
            if (key === 'cid') {
                return;
            }
            var superDescriptor = Object.getOwnPropertyDescriptor(Super, key);
            if (!isPrimitive(descriptor.value) &&
                superDescriptor &&
                superDescriptor.value === descriptor.value) {
                return;
            }
        }
        // Warn if the users manually declare reserved properties
        if (false) {}
        Object.defineProperty(Extended, key, descriptor);
    });
}

function Component(options) {
    if (typeof options === 'function') {
        return componentFactory(options);
    }
    return function (Component) {
        return componentFactory(Component, options);
    };
}
Component.registerHooks = function registerHooks(keys) {
    $internalHooks.push.apply($internalHooks, keys);
};

exports.default = Component;
exports.createDecorator = createDecorator;
exports.mixins = mixins;


/***/ }),

/***/ "66a1":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __assign = (this && this.__assign) || Object.assign || function(t) {
    for (var s, i = 1, n = arguments.length; i < n; i++) {
        s = arguments[i];
        for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
            t[p] = s[p];
    }
    return t;
};
Object.defineProperty(exports, "__esModule", { value: true });
function handleEvent(event, filters, handler) {
    for (var _i = 0, filters_1 = filters; _i < filters_1.length; _i++) {
        var filter = filters_1[_i];
        if (!filter(event)) {
            return;
        }
    }
    if (handler) {
        handler(event);
    }
}
var keyCodes = {
    esc: 27,
    tab: 9,
    enter: 13,
    space: 32,
    up: 38,
    down: 40,
    del: [8, 46],
    left: 37,
    right: 39
};
function createKeyFilter(keys) {
    var codes = [];
    for (var _i = 0, keys_1 = keys; _i < keys_1.length; _i++) {
        var key = keys_1[_i];
        if (typeof key === "number") {
            codes.push(key);
        }
        else {
            var code = keyCodes[key];
            if (typeof code === "number") {
                codes.push(code);
            }
            else {
                codes.push.apply(codes, code);
            }
        }
    }
    switch (codes.length) {
        case 0:
            return function (_) { return false; };
        case 1:
            var code_1 = codes[0];
            return function (e) { return e.keyCode === code_1; };
        default:
            return function (e) { return codes.indexOf(e.keyCode) >= 0; };
    }
}
function defineChildModifier(target, currentFilters, name, filter, children) {
    Object.defineProperty(target, name, {
        get: function () {
            // call this getter at most once.
            // reuse created instance after next time.
            var ret = createModifier(currentFilters.concat([filter]), children);
            Object.defineProperty(target, name, {
                value: ret,
                enumerable: true
            });
            return ret;
        },
        enumerable: true,
        configurable: true
    });
}
function defineKeyCodeModifiers(target, filters, children) {
    var _loop_1 = function (name_1) {
        var keyName = name_1;
        if (keyName === "left" || keyName === "right") {
            return "continue";
        }
        var code = keyCodes[keyName];
        if (typeof code === "number") {
            defineChildModifier(target, filters, keyName, function (e) { return e.keyCode === code; }, children);
        }
        else {
            var c1_1 = code[0], c2_1 = code[1];
            defineChildModifier(target, filters, keyName, function (e) { return e.keyCode === c1_1 || e.keyCode === c2_1; }, children);
        }
    };
    for (var name_1 in keyCodes) {
        _loop_1(name_1);
    }
}
function defineKeys(target, filters, children) {
    Object.defineProperty(target, "keys", {
        get: function () {
            var _this = this;
            var keysFunction = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                var propName = "keys:" + args.toString();
                var modifier = _this[propName];
                if (modifier !== undefined) {
                    return modifier;
                }
                var filter = createKeyFilter(args);
                defineChildModifier(_this, filters, propName, filter, children);
                return _this[propName];
            };
            Object.defineProperty(this, "keys", {
                value: keysFunction,
                enumerable: true
            });
            return keysFunction;
        },
        enumerable: true,
        configurable: true
    });
}
function defineExact(target, filters, children) {
    Object.defineProperty(target, "exact", {
        get: function () {
            var _this = this;
            var exactFunction = function () {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) {
                    args[_i] = arguments[_i];
                }
                var propName = "exact:" + args.toString();
                var modifier = _this[propName];
                if (modifier !== undefined) {
                    return modifier;
                }
                var expected = {
                    ctrl: false,
                    shift: false,
                    alt: false,
                    meta: false
                };
                args.forEach(function (arg) { return (expected[arg] = true); });
                var filter = function (e) {
                    return !!e.ctrlKey === expected.ctrl &&
                        !!e.shiftKey === expected.shift &&
                        !!e.altKey === expected.alt &&
                        !!e.metaKey === expected.meta;
                };
                defineChildModifier(_this, filters, propName, filter, children);
                return _this[propName];
            };
            Object.defineProperty(this, "exact", {
                value: exactFunction,
                enumerable: true
            });
            return exactFunction;
        },
        enumerable: true,
        configurable: true
    });
}
function createModifier(filters, children) {
    function m(arg) {
        if (arg instanceof Function) {
            // EventHandler => EventHandler
            return function (event) { return handleEvent(event, filters, arg); };
        }
        else {
            // Event => void
            handleEvent(arg, filters);
            return;
        }
    }
    if (children.keyboard || children.mouse) {
        var nextChildren = __assign({}, children, { keyboard: false, mouse: false });
        if (children.keyboard) {
            defineKeyCodeModifiers(m, filters, nextChildren);
            defineKeys(m, filters, nextChildren);
        }
        if (children.mouse) {
            defineChildModifier(m, filters, "middle", function (e) { return e.button === 1; }, nextChildren);
        }
        defineChildModifier(m, filters, "left", function (e) { return e.keyCode === 37 || e.button === 0; }, nextChildren);
        defineChildModifier(m, filters, "right", function (e) { return e.keyCode === 39 || e.button === 2; }, nextChildren);
    }
    if (children.exact) {
        var nextChildren = __assign({}, children, { exact: false, modkey: false });
        defineExact(m, filters, nextChildren);
    }
    if (children.modkey) {
        var nextChildren = __assign({}, children, { exact: false });
        defineChildModifier(m, filters, "ctrl", function (e) { return e.ctrlKey; }, nextChildren);
        defineChildModifier(m, filters, "shift", function (e) { return e.shiftKey; }, nextChildren);
        defineChildModifier(m, filters, "alt", function (e) { return e.altKey; }, nextChildren);
        defineChildModifier(m, filters, "meta", function (e) { return e.metaKey; }, nextChildren);
        defineChildModifier(m, filters, "noctrl", function (e) { return !e.ctrlKey; }, nextChildren);
        defineChildModifier(m, filters, "noshift", function (e) { return !e.shiftKey; }, nextChildren);
        defineChildModifier(m, filters, "noalt", function (e) { return !e.altKey; }, nextChildren);
        defineChildModifier(m, filters, "nometa", function (e) { return !e.metaKey; }, nextChildren);
    }
    defineChildModifier(m, filters, "stop", function (e) { return e.stopPropagation() || true; }, children);
    defineChildModifier(m, filters, "prevent", function (e) { return e.preventDefault() || true; }, children);
    defineChildModifier(m, filters, "self", function (e) { return e.target === e.currentTarget; }, children);
    return m;
}
exports.modifiers = createModifier([], {
    keyboard: true,
    mouse: true,
    modkey: true,
    exact: true
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoibW9kaWZpZXJzLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiLi4vc3JjL21vZGlmaWVycy50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7O0FBb0RBLHFCQUNFLEtBQVksRUFDWixPQUFzQixFQUN0QixPQUE2QjtJQUU3QixLQUFtQixVQUFPLEVBQVAsbUJBQU8sRUFBUCxxQkFBTyxFQUFQLElBQU87UUFBckIsSUFBSSxNQUFNLGdCQUFBO1FBQ2IsSUFBSSxDQUFDLE1BQU0sQ0FBQyxLQUFLLENBQUMsRUFBRTtZQUNsQixPQUFPO1NBQ1I7S0FDRjtJQUNELElBQUksT0FBTyxFQUFFO1FBQ1gsT0FBTyxDQUFDLEtBQUssQ0FBQyxDQUFDO0tBQ2hCO0FBQ0gsQ0FBQztBQUVELElBQU0sUUFBUSxHQUEwRDtJQUN0RSxHQUFHLEVBQUUsRUFBRTtJQUNQLEdBQUcsRUFBRSxDQUFDO0lBQ04sS0FBSyxFQUFFLEVBQUU7SUFDVCxLQUFLLEVBQUUsRUFBRTtJQUNULEVBQUUsRUFBRSxFQUFFO0lBQ04sSUFBSSxFQUFFLEVBQUU7SUFDUixHQUFHLEVBQUUsQ0FBQyxDQUFDLEVBQUUsRUFBRSxDQUFDO0lBQ1osSUFBSSxFQUFFLEVBQUU7SUFDUixLQUFLLEVBQUUsRUFBRTtDQUNWLENBQUM7QUFFRix5QkFBeUIsSUFBa0M7SUFDekQsSUFBTSxLQUFLLEdBQUcsRUFBYyxDQUFDO0lBQzdCLEtBQWtCLFVBQUksRUFBSixhQUFJLEVBQUosa0JBQUksRUFBSixJQUFJO1FBQWpCLElBQU0sR0FBRyxhQUFBO1FBQ1osSUFBSSxPQUFPLEdBQUcsS0FBSyxRQUFRLEVBQUU7WUFDM0IsS0FBSyxDQUFDLElBQUksQ0FBQyxHQUFHLENBQUMsQ0FBQztTQUNqQjthQUFNO1lBQ0wsSUFBTSxJQUFJLEdBQUcsUUFBUSxDQUFDLEdBQUcsQ0FBQyxDQUFDO1lBQzNCLElBQUksT0FBTyxJQUFJLEtBQUssUUFBUSxFQUFFO2dCQUM1QixLQUFLLENBQUMsSUFBSSxDQUFDLElBQUksQ0FBQyxDQUFDO2FBQ2xCO2lCQUFNO2dCQUNMLEtBQUssQ0FBQyxJQUFJLE9BQVYsS0FBSyxFQUFTLElBQUksRUFBRTthQUNyQjtTQUNGO0tBQ0Y7SUFDRCxRQUFRLEtBQUssQ0FBQyxNQUFNLEVBQUU7UUFDcEIsS0FBSyxDQUFDO1lBQ0osT0FBTyxVQUFBLENBQUMsSUFBSSxPQUFBLEtBQUssRUFBTCxDQUFLLENBQUM7UUFDcEIsS0FBSyxDQUFDO1lBQ0osSUFBTSxNQUFJLEdBQUcsS0FBSyxDQUFDLENBQUMsQ0FBQyxDQUFDO1lBQ3RCLE9BQU8sVUFBQyxDQUFNLElBQUssT0FBQSxDQUFDLENBQUMsT0FBTyxLQUFLLE1BQUksRUFBbEIsQ0FBa0IsQ0FBQztRQUN4QztZQUNFLE9BQU8sVUFBQyxDQUFNLElBQUssT0FBQSxLQUFLLENBQUMsT0FBTyxDQUFDLENBQUMsQ0FBQyxPQUFPLENBQUMsSUFBSSxDQUFDLEVBQTdCLENBQTZCLENBQUM7S0FDcEQ7QUFDSCxDQUFDO0FBU0QsNkJBQ0UsTUFBZ0IsRUFDaEIsY0FBNkIsRUFDN0IsSUFBWSxFQUNaLE1BQW1CLEVBQ25CLFFBQTZCO0lBRTdCLE1BQU0sQ0FBQyxjQUFjLENBQUMsTUFBTSxFQUFFLElBQUksRUFBRTtRQUNsQyxHQUFHLEVBQUU7WUFDSCxpQ0FBaUM7WUFDakMsMENBQTBDO1lBQzFDLElBQU0sR0FBRyxHQUFHLGNBQWMsQ0FBSyxjQUFjLFNBQUUsTUFBTSxJQUFHLFFBQVEsQ0FBQyxDQUFDO1lBQ2xFLE1BQU0sQ0FBQyxjQUFjLENBQUMsTUFBTSxFQUFFLElBQUksRUFBRTtnQkFDbEMsS0FBSyxFQUFFLEdBQUc7Z0JBQ1YsVUFBVSxFQUFFLElBQUk7YUFDakIsQ0FBQyxDQUFDO1lBQ0gsT0FBTyxHQUFHLENBQUM7UUFDYixDQUFDO1FBQ0QsVUFBVSxFQUFFLElBQUk7UUFDaEIsWUFBWSxFQUFFLElBQUk7S0FDbkIsQ0FBQyxDQUFDO0FBQ0wsQ0FBQztBQUVELGdDQUNFLE1BQWdCLEVBQ2hCLE9BQXNCLEVBQ3RCLFFBQTZCOzRCQUVsQixNQUFJO1FBQ2IsSUFBTSxPQUFPLEdBQUcsTUFBdUIsQ0FBQztRQUN4QyxJQUFJLE9BQU8sS0FBSyxNQUFNLElBQUksT0FBTyxLQUFLLE9BQU8sRUFBRTs7U0FFOUM7UUFDRCxJQUFNLElBQUksR0FBRyxRQUFRLENBQUMsT0FBTyxDQUFDLENBQUM7UUFDL0IsSUFBSSxPQUFPLElBQUksS0FBSyxRQUFRLEVBQUU7WUFDNUIsbUJBQW1CLENBQ2pCLE1BQU0sRUFDTixPQUFPLEVBQ1AsT0FBTyxFQUNQLFVBQUMsQ0FBTSxJQUFLLE9BQUEsQ0FBQyxDQUFDLE9BQU8sS0FBSyxJQUFJLEVBQWxCLENBQWtCLEVBQzlCLFFBQVEsQ0FDVCxDQUFDO1NBQ0g7YUFBTTtZQUNFLElBQUEsY0FBRSxFQUFFLGNBQUUsQ0FBUztZQUN0QixtQkFBbUIsQ0FDakIsTUFBTSxFQUNOLE9BQU8sRUFDUCxPQUFPLEVBQ1AsVUFBQyxDQUFNLElBQUssT0FBQSxDQUFDLENBQUMsT0FBTyxLQUFLLElBQUUsSUFBSSxDQUFDLENBQUMsT0FBTyxLQUFLLElBQUUsRUFBcEMsQ0FBb0MsRUFDaEQsUUFBUSxDQUNULENBQUM7U0FDSDtJQUNILENBQUM7SUF4QkQsS0FBSyxJQUFNLE1BQUksSUFBSSxRQUFRO2dCQUFoQixNQUFJO0tBd0JkO0FBQ0gsQ0FBQztBQUVELG9CQUNFLE1BQWdCLEVBQ2hCLE9BQXNCLEVBQ3RCLFFBQTZCO0lBRTdCLE1BQU0sQ0FBQyxjQUFjLENBQUMsTUFBTSxFQUFFLE1BQU0sRUFBRTtRQUNwQyxHQUFHO1lBQUgsaUJBZ0JDO1lBZkMsSUFBTSxZQUFZLEdBQUc7Z0JBQUMsY0FBcUM7cUJBQXJDLFVBQXFDLEVBQXJDLHFCQUFxQyxFQUFyQyxJQUFxQztvQkFBckMseUJBQXFDOztnQkFDekQsSUFBTSxRQUFRLEdBQUcsT0FBTyxHQUFHLElBQUksQ0FBQyxRQUFRLEVBQUUsQ0FBQztnQkFDM0MsSUFBTSxRQUFRLEdBQUcsS0FBSSxDQUFDLFFBQVEsQ0FBQyxDQUFDO2dCQUNoQyxJQUFJLFFBQVEsS0FBSyxTQUFTLEVBQUU7b0JBQzFCLE9BQU8sUUFBUSxDQUFDO2lCQUNqQjtnQkFDRCxJQUFNLE1BQU0sR0FBRyxlQUFlLENBQUMsSUFBSSxDQUFDLENBQUM7Z0JBQ3JDLG1CQUFtQixDQUFDLEtBQUksRUFBRSxPQUFPLEVBQUUsUUFBUSxFQUFFLE1BQU0sRUFBRSxRQUFRLENBQUMsQ0FBQztnQkFDL0QsT0FBTyxLQUFJLENBQUMsUUFBUSxDQUFDLENBQUM7WUFDeEIsQ0FBQyxDQUFDO1lBQ0YsTUFBTSxDQUFDLGNBQWMsQ0FBQyxJQUFJLEVBQUUsTUFBTSxFQUFFO2dCQUNsQyxLQUFLLEVBQUUsWUFBWTtnQkFDbkIsVUFBVSxFQUFFLElBQUk7YUFDakIsQ0FBQyxDQUFDO1lBQ0gsT0FBTyxZQUFZLENBQUM7UUFDdEIsQ0FBQztRQUNELFVBQVUsRUFBRSxJQUFJO1FBQ2hCLFlBQVksRUFBRSxJQUFJO0tBQ25CLENBQUMsQ0FBQztBQUNMLENBQUM7QUFFRCxxQkFDRSxNQUFnQixFQUNoQixPQUFzQixFQUN0QixRQUE2QjtJQUU3QixNQUFNLENBQUMsY0FBYyxDQUFDLE1BQU0sRUFBRSxPQUFPLEVBQUU7UUFDckMsR0FBRztZQUFILGlCQTJCQztZQTFCQyxJQUFNLGFBQWEsR0FBRztnQkFBQyxjQUFpQjtxQkFBakIsVUFBaUIsRUFBakIscUJBQWlCLEVBQWpCLElBQWlCO29CQUFqQix5QkFBaUI7O2dCQUN0QyxJQUFNLFFBQVEsR0FBRyxRQUFRLEdBQUcsSUFBSSxDQUFDLFFBQVEsRUFBRSxDQUFDO2dCQUM1QyxJQUFNLFFBQVEsR0FBRyxLQUFJLENBQUMsUUFBUSxDQUFDLENBQUM7Z0JBQ2hDLElBQUksUUFBUSxLQUFLLFNBQVMsRUFBRTtvQkFDMUIsT0FBTyxRQUFRLENBQUM7aUJBQ2pCO2dCQUNELElBQU0sUUFBUSxHQUFHO29CQUNmLElBQUksRUFBRSxLQUFLO29CQUNYLEtBQUssRUFBRSxLQUFLO29CQUNaLEdBQUcsRUFBRSxLQUFLO29CQUNWLElBQUksRUFBRSxLQUFLO2lCQUNaLENBQUM7Z0JBQ0YsSUFBSSxDQUFDLE9BQU8sQ0FBQyxVQUFBLEdBQUcsSUFBSSxPQUFBLENBQUMsUUFBUSxDQUFDLEdBQUcsQ0FBQyxHQUFHLElBQUksQ0FBQyxFQUF0QixDQUFzQixDQUFDLENBQUM7Z0JBQzVDLElBQU0sTUFBTSxHQUFHLFVBQUMsQ0FBTTtvQkFDcEIsT0FBQSxDQUFDLENBQUMsQ0FBQyxDQUFDLE9BQU8sS0FBSyxRQUFRLENBQUMsSUFBSTt3QkFDN0IsQ0FBQyxDQUFDLENBQUMsQ0FBQyxRQUFRLEtBQUssUUFBUSxDQUFDLEtBQUs7d0JBQy9CLENBQUMsQ0FBQyxDQUFDLENBQUMsTUFBTSxLQUFLLFFBQVEsQ0FBQyxHQUFHO3dCQUMzQixDQUFDLENBQUMsQ0FBQyxDQUFDLE9BQU8sS0FBSyxRQUFRLENBQUMsSUFBSTtnQkFIN0IsQ0FHNkIsQ0FBQztnQkFDaEMsbUJBQW1CLENBQUMsS0FBSSxFQUFFLE9BQU8sRUFBRSxRQUFRLEVBQUUsTUFBTSxFQUFFLFFBQVEsQ0FBQyxDQUFDO2dCQUMvRCxPQUFPLEtBQUksQ0FBQyxRQUFRLENBQUMsQ0FBQztZQUN4QixDQUFDLENBQUM7WUFDRixNQUFNLENBQUMsY0FBYyxDQUFDLElBQUksRUFBRSxPQUFPLEVBQUU7Z0JBQ25DLEtBQUssRUFBRSxhQUFhO2dCQUNwQixVQUFVLEVBQUUsSUFBSTthQUNqQixDQUFDLENBQUM7WUFDSCxPQUFPLGFBQWEsQ0FBQztRQUN2QixDQUFDO1FBQ0QsVUFBVSxFQUFFLElBQUk7UUFDaEIsWUFBWSxFQUFFLElBQUk7S0FDbkIsQ0FBQyxDQUFDO0FBQ0wsQ0FBQztBQUVELHdCQUNFLE9BQXNCLEVBQ3RCLFFBQTZCO0lBRTdCLFdBQVcsR0FBUTtRQUNqQixJQUFJLEdBQUcsWUFBWSxRQUFRLEVBQUU7WUFDM0IsK0JBQStCO1lBQy9CLE9BQU8sVUFBQyxLQUFZLElBQUssT0FBQSxXQUFXLENBQUMsS0FBSyxFQUFFLE9BQU8sRUFBRSxHQUFHLENBQUMsRUFBaEMsQ0FBZ0MsQ0FBQztTQUMzRDthQUFNO1lBQ0wsZ0JBQWdCO1lBQ2hCLFdBQVcsQ0FBQyxHQUFHLEVBQUUsT0FBTyxDQUFDLENBQUM7WUFDMUIsT0FBTztTQUNSO0lBQ0gsQ0FBQztJQUNELElBQUksUUFBUSxDQUFDLFFBQVEsSUFBSSxRQUFRLENBQUMsS0FBSyxFQUFFO1FBQ3ZDLElBQU0sWUFBWSxnQkFBUSxRQUFRLElBQUUsUUFBUSxFQUFFLEtBQUssRUFBRSxLQUFLLEVBQUUsS0FBSyxHQUFFLENBQUM7UUFDcEUsSUFBSSxRQUFRLENBQUMsUUFBUSxFQUFFO1lBQ3JCLHNCQUFzQixDQUFDLENBQUMsRUFBRSxPQUFPLEVBQUUsWUFBWSxDQUFDLENBQUM7WUFDakQsVUFBVSxDQUFDLENBQUMsRUFBRSxPQUFPLEVBQUUsWUFBWSxDQUFDLENBQUM7U0FDdEM7UUFDRCxJQUFJLFFBQVEsQ0FBQyxLQUFLLEVBQUU7WUFDbEIsbUJBQW1CLENBQ2pCLENBQUMsRUFDRCxPQUFPLEVBQ1AsUUFBUSxFQUNSLFVBQUMsQ0FBTSxJQUFLLE9BQUEsQ0FBQyxDQUFDLE1BQU0sS0FBSyxDQUFDLEVBQWQsQ0FBYyxFQUMxQixZQUFZLENBQ2IsQ0FBQztTQUNIO1FBQ0QsbUJBQW1CLENBQ2pCLENBQUMsRUFDRCxPQUFPLEVBQ1AsTUFBTSxFQUNOLFVBQUMsQ0FBTSxJQUFLLE9BQUEsQ0FBQyxDQUFDLE9BQU8sS0FBSyxFQUFFLElBQUksQ0FBQyxDQUFDLE1BQU0sS0FBSyxDQUFDLEVBQWxDLENBQWtDLEVBQzlDLFlBQVksQ0FDYixDQUFDO1FBQ0YsbUJBQW1CLENBQ2pCLENBQUMsRUFDRCxPQUFPLEVBQ1AsT0FBTyxFQUNQLFVBQUMsQ0FBTSxJQUFLLE9BQUEsQ0FBQyxDQUFDLE9BQU8sS0FBSyxFQUFFLElBQUksQ0FBQyxDQUFDLE1BQU0sS0FBSyxDQUFDLEVBQWxDLENBQWtDLEVBQzlDLFlBQVksQ0FDYixDQUFDO0tBQ0g7SUFDRCxJQUFJLFFBQVEsQ0FBQyxLQUFLLEVBQUU7UUFDbEIsSUFBTSxZQUFZLGdCQUFRLFFBQVEsSUFBRSxLQUFLLEVBQUUsS0FBSyxFQUFFLE1BQU0sRUFBRSxLQUFLLEdBQUUsQ0FBQztRQUNsRSxXQUFXLENBQUMsQ0FBQyxFQUFFLE9BQU8sRUFBRSxZQUFZLENBQUMsQ0FBQztLQUN2QztJQUNELElBQUksUUFBUSxDQUFDLE1BQU0sRUFBRTtRQUNuQixJQUFNLFlBQVksZ0JBQVEsUUFBUSxJQUFFLEtBQUssRUFBRSxLQUFLLEdBQUUsQ0FBQztRQUNuRCxtQkFBbUIsQ0FDakIsQ0FBQyxFQUNELE9BQU8sRUFDUCxNQUFNLEVBQ04sVUFBQyxDQUFNLElBQUssT0FBQSxDQUFDLENBQUMsT0FBTyxFQUFULENBQVMsRUFDckIsWUFBWSxDQUNiLENBQUM7UUFDRixtQkFBbUIsQ0FDakIsQ0FBQyxFQUNELE9BQU8sRUFDUCxPQUFPLEVBQ1AsVUFBQyxDQUFNLElBQUssT0FBQSxDQUFDLENBQUMsUUFBUSxFQUFWLENBQVUsRUFDdEIsWUFBWSxDQUNiLENBQUM7UUFDRixtQkFBbUIsQ0FBQyxDQUFDLEVBQUUsT0FBTyxFQUFFLEtBQUssRUFBRSxVQUFDLENBQU0sSUFBSyxPQUFBLENBQUMsQ0FBQyxNQUFNLEVBQVIsQ0FBUSxFQUFFLFlBQVksQ0FBQyxDQUFDO1FBQzNFLG1CQUFtQixDQUNqQixDQUFDLEVBQ0QsT0FBTyxFQUNQLE1BQU0sRUFDTixVQUFDLENBQU0sSUFBSyxPQUFBLENBQUMsQ0FBQyxPQUFPLEVBQVQsQ0FBUyxFQUNyQixZQUFZLENBQ2IsQ0FBQztRQUVGLG1CQUFtQixDQUNqQixDQUFDLEVBQ0QsT0FBTyxFQUNQLFFBQVEsRUFDUixVQUFDLENBQU0sSUFBSyxPQUFBLENBQUMsQ0FBQyxDQUFDLE9BQU8sRUFBVixDQUFVLEVBQ3RCLFlBQVksQ0FDYixDQUFDO1FBQ0YsbUJBQW1CLENBQ2pCLENBQUMsRUFDRCxPQUFPLEVBQ1AsU0FBUyxFQUNULFVBQUMsQ0FBTSxJQUFLLE9BQUEsQ0FBQyxDQUFDLENBQUMsUUFBUSxFQUFYLENBQVcsRUFDdkIsWUFBWSxDQUNiLENBQUM7UUFDRixtQkFBbUIsQ0FDakIsQ0FBQyxFQUNELE9BQU8sRUFDUCxPQUFPLEVBQ1AsVUFBQyxDQUFNLElBQUssT0FBQSxDQUFDLENBQUMsQ0FBQyxNQUFNLEVBQVQsQ0FBUyxFQUNyQixZQUFZLENBQ2IsQ0FBQztRQUNGLG1CQUFtQixDQUNqQixDQUFDLEVBQ0QsT0FBTyxFQUNQLFFBQVEsRUFDUixVQUFDLENBQU0sSUFBSyxPQUFBLENBQUMsQ0FBQyxDQUFDLE9BQU8sRUFBVixDQUFVLEVBQ3RCLFlBQVksQ0FDYixDQUFDO0tBQ0g7SUFDRCxtQkFBbUIsQ0FDakIsQ0FBQyxFQUNELE9BQU8sRUFDUCxNQUFNLEVBQ04sVUFBQSxDQUFDLElBQUksT0FBQSxDQUFDLENBQUMsZUFBZSxFQUFFLElBQUksSUFBSSxFQUEzQixDQUEyQixFQUNoQyxRQUFRLENBQ1QsQ0FBQztJQUNGLG1CQUFtQixDQUNqQixDQUFDLEVBQ0QsT0FBTyxFQUNQLFNBQVMsRUFDVCxVQUFBLENBQUMsSUFBSSxPQUFBLENBQUMsQ0FBQyxjQUFjLEVBQUUsSUFBSSxJQUFJLEVBQTFCLENBQTBCLEVBQy9CLFFBQVEsQ0FDVCxDQUFDO0lBQ0YsbUJBQW1CLENBQ2pCLENBQUMsRUFDRCxPQUFPLEVBQ1AsTUFBTSxFQUNOLFVBQUEsQ0FBQyxJQUFJLE9BQUEsQ0FBQyxDQUFDLE1BQU0sS0FBSyxDQUFDLENBQUMsYUFBYSxFQUE1QixDQUE0QixFQUNqQyxRQUFRLENBQ1QsQ0FBQztJQUNGLE9BQU8sQ0FBMkIsQ0FBQztBQUNyQyxDQUFDO0FBRVksUUFBQSxTQUFTLEdBQUcsY0FBYyxDQUFDLEVBQUUsRUFBRTtJQUMxQyxRQUFRLEVBQUUsSUFBSTtJQUNkLEtBQUssRUFBRSxJQUFJO0lBQ1gsTUFBTSxFQUFFLElBQUk7SUFDWixLQUFLLEVBQUUsSUFBSTtDQUNaLENBQUMsQ0FBQyJ9

/***/ }),

/***/ "6762":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// https://github.com/tc39/Array.prototype.includes
var $export = __webpack_require__("5ca1");
var $includes = __webpack_require__("c366")(true);

$export($export.P, 'Array', {
  includes: function includes(el /* , fromIndex = 0 */) {
    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
  }
});

__webpack_require__("9c6c")('includes');


/***/ }),

/***/ "6821":
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__("626a");
var defined = __webpack_require__("be13");
module.exports = function (it) {
  return IObject(defined(it));
};


/***/ }),

/***/ "69a8":
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),

/***/ "6a99":
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__("d3f4");
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function (it, S) {
  if (!isObject(it)) return it;
  var fn, val;
  if (S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  if (typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it))) return val;
  if (!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  throw TypeError("Can't convert object to primitive value");
};


/***/ }),

/***/ "6b54":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

__webpack_require__("3846");
var anObject = __webpack_require__("cb7c");
var $flags = __webpack_require__("0bfb");
var DESCRIPTORS = __webpack_require__("9e1e");
var TO_STRING = 'toString';
var $toString = /./[TO_STRING];

var define = function (fn) {
  __webpack_require__("2aba")(RegExp.prototype, TO_STRING, fn, true);
};

// 21.2.5.14 RegExp.prototype.toString()
if (__webpack_require__("79e5")(function () { return $toString.call({ source: 'a', flags: 'b' }) != '/a/b'; })) {
  define(function toString() {
    var R = anObject(this);
    return '/'.concat(R.source, '/',
      'flags' in R ? R.flags : !DESCRIPTORS && R instanceof RegExp ? $flags.call(R) : undefined);
  });
// FF44- RegExp#toString has a wrong name
} else if ($toString.name != TO_STRING) {
  define(function toString() {
    return $toString.call(this);
  });
}


/***/ }),

/***/ "7333":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 19.1.2.1 Object.assign(target, source, ...)
var getKeys = __webpack_require__("0d58");
var gOPS = __webpack_require__("2621");
var pIE = __webpack_require__("52a7");
var toObject = __webpack_require__("4bf8");
var IObject = __webpack_require__("626a");
var $assign = Object.assign;

// should work with symbols and should have deterministic property order (V8 bug)
module.exports = !$assign || __webpack_require__("79e5")(function () {
  var A = {};
  var B = {};
  // eslint-disable-next-line no-undef
  var S = Symbol();
  var K = 'abcdefghijklmnopqrst';
  A[S] = 7;
  K.split('').forEach(function (k) { B[k] = k; });
  return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
}) ? function assign(target, source) { // eslint-disable-line no-unused-vars
  var T = toObject(target);
  var aLen = arguments.length;
  var index = 1;
  var getSymbols = gOPS.f;
  var isEnum = pIE.f;
  while (aLen > index) {
    var S = IObject(arguments[index++]);
    var keys = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S);
    var length = keys.length;
    var j = 0;
    var key;
    while (length > j) if (isEnum.call(S, key = keys[j++])) T[key] = S[key];
  } return T;
} : $assign;


/***/ }),

/***/ "7514":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 22.1.3.8 Array.prototype.find(predicate, thisArg = undefined)
var $export = __webpack_require__("5ca1");
var $find = __webpack_require__("0a49")(5);
var KEY = 'find';
var forced = true;
// Shouldn't skip holes
if (KEY in []) Array(1)[KEY](function () { forced = false; });
$export($export.P + $export.F * forced, 'Array', {
  find: function find(callbackfn /* , that = undefined */) {
    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});
__webpack_require__("9c6c")(KEY);


/***/ }),

/***/ "7726":
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),

/***/ "77f1":
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__("4588");
var max = Math.max;
var min = Math.min;
module.exports = function (index, length) {
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};


/***/ }),

/***/ "79e5":
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (e) {
    return true;
  }
};


/***/ }),

/***/ "7a1a":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 32 32"}},[_c('path',{attrs:{"d":"M22.667 4l7 6-7 6 7 6-7 6v-4h-3.653l-3.76-3.76 2.827-2.827L20.668 20h2v-8h-2l-12 12h-6v-4h4.347l12-12h3.653V4zm-20 4h6l3.76 3.76L9.6 14.587 7.013 12H2.666V8z"}})])
      }
    
      });
    

/***/ }),

/***/ "7a56":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var global = __webpack_require__("7726");
var dP = __webpack_require__("86cc");
var DESCRIPTORS = __webpack_require__("9e1e");
var SPECIES = __webpack_require__("2b4c")('species');

module.exports = function (KEY) {
  var C = global[KEY];
  if (DESCRIPTORS && C && !C[SPECIES]) dP.f(C, SPECIES, {
    configurable: true,
    get: function () { return this; }
  });
};


/***/ }),

/***/ "7f20":
/***/ (function(module, exports, __webpack_require__) {

var def = __webpack_require__("86cc").f;
var has = __webpack_require__("69a8");
var TAG = __webpack_require__("2b4c")('toStringTag');

module.exports = function (it, tag, stat) {
  if (it && !has(it = stat ? it : it.prototype, TAG)) def(it, TAG, { configurable: true, value: tag });
};


/***/ }),

/***/ "7f7f":
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__("86cc").f;
var FProto = Function.prototype;
var nameRE = /^\s*function ([^ (]*)/;
var NAME = 'name';

// 19.2.4.2 name
NAME in FProto || __webpack_require__("9e1e") && dP(FProto, NAME, {
  configurable: true,
  get: function () {
    try {
      return ('' + this).match(nameRE)[1];
    } catch (e) {
      return '';
    }
  }
});


/***/ }),

/***/ "8079":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("7726");
var macrotask = __webpack_require__("1991").set;
var Observer = global.MutationObserver || global.WebKitMutationObserver;
var process = global.process;
var Promise = global.Promise;
var isNode = __webpack_require__("2d95")(process) == 'process';

module.exports = function () {
  var head, last, notify;

  var flush = function () {
    var parent, fn;
    if (isNode && (parent = process.domain)) parent.exit();
    while (head) {
      fn = head.fn;
      head = head.next;
      try {
        fn();
      } catch (e) {
        if (head) notify();
        else last = undefined;
        throw e;
      }
    } last = undefined;
    if (parent) parent.enter();
  };

  // Node.js
  if (isNode) {
    notify = function () {
      process.nextTick(flush);
    };
  // browsers with MutationObserver, except iOS Safari - https://github.com/zloirock/core-js/issues/339
  } else if (Observer && !(global.navigator && global.navigator.standalone)) {
    var toggle = true;
    var node = document.createTextNode('');
    new Observer(flush).observe(node, { characterData: true }); // eslint-disable-line no-new
    notify = function () {
      node.data = toggle = !toggle;
    };
  // environments with maybe non-completely correct, but existent Promise
  } else if (Promise && Promise.resolve) {
    // Promise.resolve without an argument throws an error in LG WebOS 2
    var promise = Promise.resolve(undefined);
    notify = function () {
      promise.then(flush);
    };
  // for other environments - macrotask based on:
  // - setImmediate
  // - MessageChannel
  // - window.postMessag
  // - onreadystatechange
  // - setTimeout
  } else {
    notify = function () {
      // strange IE + webpack dev server bug - use .call(global)
      macrotask.call(global, flush);
    };
  }

  return function (fn) {
    var task = { fn: fn, next: undefined };
    if (last) last.next = task;
    if (!head) {
      head = task;
      notify();
    } last = task;
  };
};


/***/ }),

/***/ "8378":
/***/ (function(module, exports) {

var core = module.exports = { version: '2.5.7' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),

/***/ "84d8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 16 31"}},[_c('path',{attrs:{"d":"M15.552 15.168q.448.32.448.832 0 .448-.448.768L1.856 25.28q-.768.512-1.312.192T0 24.192V7.744q0-.96.544-1.28t1.312.192z"}})])
      }
    
      });
    

/***/ }),

/***/ "84f2":
/***/ (function(module, exports) {

module.exports = {};


/***/ }),

/***/ "86cc":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("cb7c");
var IE8_DOM_DEFINE = __webpack_require__("c69a");
var toPrimitive = __webpack_require__("6a99");
var dP = Object.defineProperty;

exports.f = __webpack_require__("9e1e") ? Object.defineProperty : function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if (IE8_DOM_DEFINE) try {
    return dP(O, P, Attributes);
  } catch (e) { /* empty */ }
  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported!');
  if ('value' in Attributes) O[P] = Attributes.value;
  return O;
};


/***/ }),

/***/ "885d":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 32 32"}},[_c('path',{attrs:{"d":"M4 16C4 9.4 9.4 4 16 4s12 5.4 12 12c0 1.2-.8 2-2 2s-2-.8-2-2c0-4.4-3.6-8-8-8s-8 3.6-8 8 3.6 8 8 8c1.2 0 2 .8 2 2s-.8 2-2 2C9.4 28 4 22.6 4 16z"}})])
      }
    
      });
    

/***/ }),

/***/ "8b97":
/***/ (function(module, exports, __webpack_require__) {

// Works with __proto__ only. Old v8 can't work with null proto objects.
/* eslint-disable no-proto */
var isObject = __webpack_require__("d3f4");
var anObject = __webpack_require__("cb7c");
var check = function (O, proto) {
  anObject(O);
  if (!isObject(proto) && proto !== null) throw TypeError(proto + ": can't set as prototype!");
};
module.exports = {
  set: Object.setPrototypeOf || ('__proto__' in {} ? // eslint-disable-line
    function (test, buggy, set) {
      try {
        set = __webpack_require__("9b43")(Function.call, __webpack_require__("11e9").f(Object.prototype, '__proto__').set, 2);
        set(test, []);
        buggy = !(test instanceof Array);
      } catch (e) { buggy = true; }
      return function setPrototypeOf(O, proto) {
        check(O, proto);
        if (buggy) O.__proto__ = proto;
        else set(O, proto);
        return O;
      };
    }({}, false) : undefined),
  check: check
};


/***/ }),

/***/ "8bbf":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE__8bbf__;

/***/ }),

/***/ "906b":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 22 32"}},[_c('path',{attrs:{"d":"M20.8 14.4q.704 0 1.152.48T22.4 16t-.48 1.12-1.12.48H1.6q-.64 0-1.12-.48T0 16t.448-1.12T1.6 14.4h19.2zM1.6 11.2q-.64 0-1.12-.48T0 9.6t.448-1.12T1.6 8h19.2q.704 0 1.152.48T22.4 9.6t-.48 1.12-1.12.48H1.6zm19.2 9.6q.704 0 1.152.48t.448 1.12-.48 1.12-1.12.48H1.6q-.64 0-1.12-.48T0 22.4t.448-1.12T1.6 20.8h19.2z"}})])
      }
    
      });
    

/***/ }),

/***/ "9093":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.7 / 15.2.3.4 Object.getOwnPropertyNames(O)
var $keys = __webpack_require__("ce10");
var hiddenKeys = __webpack_require__("e11e").concat('length', 'prototype');

exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
  return $keys(O, hiddenKeys);
};


/***/ }),

/***/ "96cf":
/***/ (function(module, exports) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

!(function(global) {
  "use strict";

  var Op = Object.prototype;
  var hasOwn = Op.hasOwnProperty;
  var undefined; // More compressible than void 0.
  var $Symbol = typeof Symbol === "function" ? Symbol : {};
  var iteratorSymbol = $Symbol.iterator || "@@iterator";
  var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
  var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  var inModule = typeof module === "object";
  var runtime = global.regeneratorRuntime;
  if (runtime) {
    if (inModule) {
      // If regeneratorRuntime is defined globally and we're in a module,
      // make the exports object identical to regeneratorRuntime.
      module.exports = runtime;
    }
    // Don't bother evaluating the rest of this file if the runtime was
    // already defined globally.
    return;
  }

  // Define the runtime globally (as expected by generated code) as either
  // module.exports (if we're in a module) or a new, empty object.
  runtime = global.regeneratorRuntime = inModule ? module.exports : {};

  function wrap(innerFn, outerFn, self, tryLocsList) {
    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
    var generator = Object.create(protoGenerator.prototype);
    var context = new Context(tryLocsList || []);

    // The ._invoke method unifies the implementations of the .next,
    // .throw, and .return methods.
    generator._invoke = makeInvokeMethod(innerFn, self, context);

    return generator;
  }
  runtime.wrap = wrap;

  // Try/catch helper to minimize deoptimizations. Returns a completion
  // record like context.tryEntries[i].completion. This interface could
  // have been (and was previously) designed to take a closure to be
  // invoked without arguments, but in all the cases we care about we
  // already have an existing method we want to call, so there's no need
  // to create a new function object. We can even get away with assuming
  // the method takes exactly one argument, since that happens to be true
  // in every case, so we don't have to touch the arguments object. The
  // only additional allocation required is the completion record, which
  // has a stable shape and so hopefully should be cheap to allocate.
  function tryCatch(fn, obj, arg) {
    try {
      return { type: "normal", arg: fn.call(obj, arg) };
    } catch (err) {
      return { type: "throw", arg: err };
    }
  }

  var GenStateSuspendedStart = "suspendedStart";
  var GenStateSuspendedYield = "suspendedYield";
  var GenStateExecuting = "executing";
  var GenStateCompleted = "completed";

  // Returning this object from the innerFn has the same effect as
  // breaking out of the dispatch switch statement.
  var ContinueSentinel = {};

  // Dummy constructor functions that we use as the .constructor and
  // .constructor.prototype properties for functions that return Generator
  // objects. For full spec compliance, you may wish to configure your
  // minifier not to mangle the names of these two functions.
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}

  // This is a polyfill for %IteratorPrototype% for environments that
  // don't natively support it.
  var IteratorPrototype = {};
  IteratorPrototype[iteratorSymbol] = function () {
    return this;
  };

  var getProto = Object.getPrototypeOf;
  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  if (NativeIteratorPrototype &&
      NativeIteratorPrototype !== Op &&
      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {
    // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
  }

  var Gp = GeneratorFunctionPrototype.prototype =
    Generator.prototype = Object.create(IteratorPrototype);
  GeneratorFunction.prototype = Gp.constructor = GeneratorFunctionPrototype;
  GeneratorFunctionPrototype.constructor = GeneratorFunction;
  GeneratorFunctionPrototype[toStringTagSymbol] =
    GeneratorFunction.displayName = "GeneratorFunction";

  // Helper for defining the .next, .throw, and .return methods of the
  // Iterator interface in terms of a single ._invoke method.
  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function(method) {
      prototype[method] = function(arg) {
        return this._invoke(method, arg);
      };
    });
  }

  runtime.isGeneratorFunction = function(genFun) {
    var ctor = typeof genFun === "function" && genFun.constructor;
    return ctor
      ? ctor === GeneratorFunction ||
        // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction"
      : false;
  };

  runtime.mark = function(genFun) {
    if (Object.setPrototypeOf) {
      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
    } else {
      genFun.__proto__ = GeneratorFunctionPrototype;
      if (!(toStringTagSymbol in genFun)) {
        genFun[toStringTagSymbol] = "GeneratorFunction";
      }
    }
    genFun.prototype = Object.create(Gp);
    return genFun;
  };

  // Within the body of any async function, `await x` is transformed to
  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
  // `hasOwn.call(value, "__await")` to determine if the yielded value is
  // meant to be awaited.
  runtime.awrap = function(arg) {
    return { __await: arg };
  };

  function AsyncIterator(generator) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);
      if (record.type === "throw") {
        reject(record.arg);
      } else {
        var result = record.arg;
        var value = result.value;
        if (value &&
            typeof value === "object" &&
            hasOwn.call(value, "__await")) {
          return Promise.resolve(value.__await).then(function(value) {
            invoke("next", value, resolve, reject);
          }, function(err) {
            invoke("throw", err, resolve, reject);
          });
        }

        return Promise.resolve(value).then(function(unwrapped) {
          // When a yielded Promise is resolved, its final value becomes
          // the .value of the Promise<{value,done}> result for the
          // current iteration. If the Promise is rejected, however, the
          // result for this iteration will be rejected with the same
          // reason. Note that rejections of yielded Promises are not
          // thrown back into the generator function, as is the case
          // when an awaited Promise is rejected. This difference in
          // behavior between yield and await is important, because it
          // allows the consumer to decide what to do with the yielded
          // rejection (swallow it and continue, manually .throw it back
          // into the generator, abandon iteration, whatever). With
          // await, by contrast, there is no opportunity to examine the
          // rejection reason outside the generator function, so the
          // only option is to throw it from the await expression, and
          // let the generator function handle the exception.
          result.value = unwrapped;
          resolve(result);
        }, reject);
      }
    }

    var previousPromise;

    function enqueue(method, arg) {
      function callInvokeWithMethodAndArg() {
        return new Promise(function(resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise =
        // If enqueue has been called before, then we want to wait until
        // all previous Promises have been resolved before calling invoke,
        // so that results are always delivered in the correct order. If
        // enqueue has not been called before, then it is important to
        // call invoke immediately, without waiting on a callback to fire,
        // so that the async generator function has the opportunity to do
        // any necessary setup in a predictable way. This predictability
        // is why the Promise constructor synchronously invokes its
        // executor callback, and why async functions synchronously
        // execute code before the first await. Since we implement simple
        // async functions in terms of async generators, it is especially
        // important to get this right, even though it requires care.
        previousPromise ? previousPromise.then(
          callInvokeWithMethodAndArg,
          // Avoid propagating failures to Promises returned by later
          // invocations of the iterator.
          callInvokeWithMethodAndArg
        ) : callInvokeWithMethodAndArg();
    }

    // Define the unified helper method that is used to implement .next,
    // .throw, and .return (see defineIteratorMethods).
    this._invoke = enqueue;
  }

  defineIteratorMethods(AsyncIterator.prototype);
  AsyncIterator.prototype[asyncIteratorSymbol] = function () {
    return this;
  };
  runtime.AsyncIterator = AsyncIterator;

  // Note that simple async functions are implemented on top of
  // AsyncIterator objects; they just return a Promise for the value of
  // the final result produced by the iterator.
  runtime.async = function(innerFn, outerFn, self, tryLocsList) {
    var iter = new AsyncIterator(
      wrap(innerFn, outerFn, self, tryLocsList)
    );

    return runtime.isGeneratorFunction(outerFn)
      ? iter // If outerFn is a generator, return the full iterator.
      : iter.next().then(function(result) {
          return result.done ? result.value : iter.next();
        });
  };

  function makeInvokeMethod(innerFn, self, context) {
    var state = GenStateSuspendedStart;

    return function invoke(method, arg) {
      if (state === GenStateExecuting) {
        throw new Error("Generator is already running");
      }

      if (state === GenStateCompleted) {
        if (method === "throw") {
          throw arg;
        }

        // Be forgiving, per 25.3.3.3.3 of the spec:
        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
        return doneResult();
      }

      context.method = method;
      context.arg = arg;

      while (true) {
        var delegate = context.delegate;
        if (delegate) {
          var delegateResult = maybeInvokeDelegate(delegate, context);
          if (delegateResult) {
            if (delegateResult === ContinueSentinel) continue;
            return delegateResult;
          }
        }

        if (context.method === "next") {
          // Setting context._sent for legacy support of Babel's
          // function.sent implementation.
          context.sent = context._sent = context.arg;

        } else if (context.method === "throw") {
          if (state === GenStateSuspendedStart) {
            state = GenStateCompleted;
            throw context.arg;
          }

          context.dispatchException(context.arg);

        } else if (context.method === "return") {
          context.abrupt("return", context.arg);
        }

        state = GenStateExecuting;

        var record = tryCatch(innerFn, self, context);
        if (record.type === "normal") {
          // If an exception is thrown from innerFn, we leave state ===
          // GenStateExecuting and loop back for another invocation.
          state = context.done
            ? GenStateCompleted
            : GenStateSuspendedYield;

          if (record.arg === ContinueSentinel) {
            continue;
          }

          return {
            value: record.arg,
            done: context.done
          };

        } else if (record.type === "throw") {
          state = GenStateCompleted;
          // Dispatch the exception by looping back around to the
          // context.dispatchException(context.arg) call above.
          context.method = "throw";
          context.arg = record.arg;
        }
      }
    };
  }

  // Call delegate.iterator[context.method](context.arg) and handle the
  // result, either by returning a { value, done } result from the
  // delegate iterator, or by modifying context.method and context.arg,
  // setting context.delegate to null, and returning the ContinueSentinel.
  function maybeInvokeDelegate(delegate, context) {
    var method = delegate.iterator[context.method];
    if (method === undefined) {
      // A .throw or .return when the delegate iterator has no .throw
      // method always terminates the yield* loop.
      context.delegate = null;

      if (context.method === "throw") {
        if (delegate.iterator.return) {
          // If the delegate iterator has a return method, give it a
          // chance to clean up.
          context.method = "return";
          context.arg = undefined;
          maybeInvokeDelegate(delegate, context);

          if (context.method === "throw") {
            // If maybeInvokeDelegate(context) changed context.method from
            // "return" to "throw", let that override the TypeError below.
            return ContinueSentinel;
          }
        }

        context.method = "throw";
        context.arg = new TypeError(
          "The iterator does not provide a 'throw' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);

    if (record.type === "throw") {
      context.method = "throw";
      context.arg = record.arg;
      context.delegate = null;
      return ContinueSentinel;
    }

    var info = record.arg;

    if (! info) {
      context.method = "throw";
      context.arg = new TypeError("iterator result is not an object");
      context.delegate = null;
      return ContinueSentinel;
    }

    if (info.done) {
      // Assign the result of the finished delegate to the temporary
      // variable specified by delegate.resultName (see delegateYield).
      context[delegate.resultName] = info.value;

      // Resume execution at the desired location (see delegateYield).
      context.next = delegate.nextLoc;

      // If context.method was "throw" but the delegate handled the
      // exception, let the outer generator proceed normally. If
      // context.method was "next", forget context.arg since it has been
      // "consumed" by the delegate iterator. If context.method was
      // "return", allow the original .return call to continue in the
      // outer generator.
      if (context.method !== "return") {
        context.method = "next";
        context.arg = undefined;
      }

    } else {
      // Re-yield the result returned by the delegate method.
      return info;
    }

    // The delegate iterator is finished, so forget it and continue with
    // the outer generator.
    context.delegate = null;
    return ContinueSentinel;
  }

  // Define Generator.prototype.{next,throw,return} in terms of the
  // unified ._invoke helper method.
  defineIteratorMethods(Gp);

  Gp[toStringTagSymbol] = "Generator";

  // A Generator should always return itself as the iterator object when the
  // @@iterator function is called on it. Some browsers' implementations of the
  // iterator prototype chain incorrectly implement this, causing the Generator
  // object to not be returned from this call. This ensures that doesn't happen.
  // See https://github.com/facebook/regenerator/issues/274 for more details.
  Gp[iteratorSymbol] = function() {
    return this;
  };

  Gp.toString = function() {
    return "[object Generator]";
  };

  function pushTryEntry(locs) {
    var entry = { tryLoc: locs[0] };

    if (1 in locs) {
      entry.catchLoc = locs[1];
    }

    if (2 in locs) {
      entry.finallyLoc = locs[2];
      entry.afterLoc = locs[3];
    }

    this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal";
    delete record.arg;
    entry.completion = record;
  }

  function Context(tryLocsList) {
    // The root entry object (effectively a try statement without a catch
    // or a finally block) gives us a place to store values thrown from
    // locations where there is no enclosing try statement.
    this.tryEntries = [{ tryLoc: "root" }];
    tryLocsList.forEach(pushTryEntry, this);
    this.reset(true);
  }

  runtime.keys = function(object) {
    var keys = [];
    for (var key in object) {
      keys.push(key);
    }
    keys.reverse();

    // Rather than returning an object with a next method, we keep
    // things simple and return the next function itself.
    return function next() {
      while (keys.length) {
        var key = keys.pop();
        if (key in object) {
          next.value = key;
          next.done = false;
          return next;
        }
      }

      // To avoid creating an additional object, we just hang the .value
      // and .done properties off the next function object itself. This
      // also ensures that the minifier will not anonymize the function.
      next.done = true;
      return next;
    };
  };

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) {
        return iteratorMethod.call(iterable);
      }

      if (typeof iterable.next === "function") {
        return iterable;
      }

      if (!isNaN(iterable.length)) {
        var i = -1, next = function next() {
          while (++i < iterable.length) {
            if (hasOwn.call(iterable, i)) {
              next.value = iterable[i];
              next.done = false;
              return next;
            }
          }

          next.value = undefined;
          next.done = true;

          return next;
        };

        return next.next = next;
      }
    }

    // Return an iterator with no values.
    return { next: doneResult };
  }
  runtime.values = values;

  function doneResult() {
    return { value: undefined, done: true };
  }

  Context.prototype = {
    constructor: Context,

    reset: function(skipTempReset) {
      this.prev = 0;
      this.next = 0;
      // Resetting context._sent for legacy support of Babel's
      // function.sent implementation.
      this.sent = this._sent = undefined;
      this.done = false;
      this.delegate = null;

      this.method = "next";
      this.arg = undefined;

      this.tryEntries.forEach(resetTryEntry);

      if (!skipTempReset) {
        for (var name in this) {
          // Not sure about the optimal order of these conditions:
          if (name.charAt(0) === "t" &&
              hasOwn.call(this, name) &&
              !isNaN(+name.slice(1))) {
            this[name] = undefined;
          }
        }
      }
    },

    stop: function() {
      this.done = true;

      var rootEntry = this.tryEntries[0];
      var rootRecord = rootEntry.completion;
      if (rootRecord.type === "throw") {
        throw rootRecord.arg;
      }

      return this.rval;
    },

    dispatchException: function(exception) {
      if (this.done) {
        throw exception;
      }

      var context = this;
      function handle(loc, caught) {
        record.type = "throw";
        record.arg = exception;
        context.next = loc;

        if (caught) {
          // If the dispatched exception was caught by a catch block,
          // then let that catch block handle the exception normally.
          context.method = "next";
          context.arg = undefined;
        }

        return !! caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        var record = entry.completion;

        if (entry.tryLoc === "root") {
          // Exception thrown outside of any try block that could handle
          // it, so set the completion value of the entire function to
          // throw the exception.
          return handle("end");
        }

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc");
          var hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            } else if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            }

          } else if (hasFinally) {
            if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else {
            throw new Error("try statement without catch or finally");
          }
        }
      }
    },

    abrupt: function(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc <= this.prev &&
            hasOwn.call(entry, "finallyLoc") &&
            this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      if (finallyEntry &&
          (type === "break" ||
           type === "continue") &&
          finallyEntry.tryLoc <= arg &&
          arg <= finallyEntry.finallyLoc) {
        // Ignore the finally entry if control is not jumping to a
        // location outside the try/catch block.
        finallyEntry = null;
      }

      var record = finallyEntry ? finallyEntry.completion : {};
      record.type = type;
      record.arg = arg;

      if (finallyEntry) {
        this.method = "next";
        this.next = finallyEntry.finallyLoc;
        return ContinueSentinel;
      }

      return this.complete(record);
    },

    complete: function(record, afterLoc) {
      if (record.type === "throw") {
        throw record.arg;
      }

      if (record.type === "break" ||
          record.type === "continue") {
        this.next = record.arg;
      } else if (record.type === "return") {
        this.rval = this.arg = record.arg;
        this.method = "return";
        this.next = "end";
      } else if (record.type === "normal" && afterLoc) {
        this.next = afterLoc;
      }

      return ContinueSentinel;
    },

    finish: function(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) {
          this.complete(entry.completion, entry.afterLoc);
          resetTryEntry(entry);
          return ContinueSentinel;
        }
      }
    },

    "catch": function(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;
          if (record.type === "throw") {
            var thrown = record.arg;
            resetTryEntry(entry);
          }
          return thrown;
        }
      }

      // The context.catch method must only be called with a location
      // argument that corresponds to a known catch block.
      throw new Error("illegal catch attempt");
    },

    delegateYield: function(iterable, resultName, nextLoc) {
      this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      };

      if (this.method === "next") {
        // Deliberately forget the last sent value so that we don't
        // accidentally pass it on to the delegate.
        this.arg = undefined;
      }

      return ContinueSentinel;
    }
  };
})(
  // In sloppy mode, unbound `this` refers to the global object, fallback to
  // Function constructor if we're in global strict mode. That is sadly a form
  // of indirect eval which violates Content Security Policy.
  (function() { return this })() || Function("return this")()
);


/***/ }),

/***/ "9744":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var toInteger = __webpack_require__("4588");
var defined = __webpack_require__("be13");

module.exports = function repeat(count) {
  var str = String(defined(this));
  var res = '';
  var n = toInteger(count);
  if (n < 0 || n == Infinity) throw RangeError("Count can't be negative");
  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) res += str;
  return res;
};


/***/ }),

/***/ "9b43":
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__("d8e8");
module.exports = function (fn, that, length) {
  aFunction(fn);
  if (that === undefined) return fn;
  switch (length) {
    case 1: return function (a) {
      return fn.call(that, a);
    };
    case 2: return function (a, b) {
      return fn.call(that, a, b);
    };
    case 3: return function (a, b, c) {
      return fn.call(that, a, b, c);
    };
  }
  return function (/* ...args */) {
    return fn.apply(that, arguments);
  };
};


/***/ }),

/***/ "9c6c":
/***/ (function(module, exports, __webpack_require__) {

// 22.1.3.31 Array.prototype[@@unscopables]
var UNSCOPABLES = __webpack_require__("2b4c")('unscopables');
var ArrayProto = Array.prototype;
if (ArrayProto[UNSCOPABLES] == undefined) __webpack_require__("32e9")(ArrayProto, UNSCOPABLES, {});
module.exports = function (key) {
  ArrayProto[UNSCOPABLES][key] = true;
};


/***/ }),

/***/ "9c80":
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return { e: false, v: exec() };
  } catch (e) {
    return { e: true, v: e };
  }
};


/***/ }),

/***/ "9def":
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__("4588");
var min = Math.min;
module.exports = function (it) {
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};


/***/ }),

/***/ "9e1e":
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__("79e5")(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ "a25f":
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__("7726");
var navigator = global.navigator;

module.exports = navigator && navigator.userAgent || '';


/***/ }),

/***/ "a481":
/***/ (function(module, exports, __webpack_require__) {

// @@replace logic
__webpack_require__("214f")('replace', 2, function (defined, REPLACE, $replace) {
  // 21.1.3.14 String.prototype.replace(searchValue, replaceValue)
  return [function replace(searchValue, replaceValue) {
    'use strict';
    var O = defined(this);
    var fn = searchValue == undefined ? undefined : searchValue[REPLACE];
    return fn !== undefined
      ? fn.call(searchValue, O, replaceValue)
      : $replace.call(String(O), searchValue, replaceValue);
  }, $replace];
});


/***/ }),

/***/ "a5b8":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 25.4.1.5 NewPromiseCapability(C)
var aFunction = __webpack_require__("d8e8");

function PromiseCapability(C) {
  var resolve, reject;
  this.promise = new C(function ($$resolve, $$reject) {
    if (resolve !== undefined || reject !== undefined) throw TypeError('Bad Promise constructor');
    resolve = $$resolve;
    reject = $$reject;
  });
  this.resolve = aFunction(resolve);
  this.reject = aFunction(reject);
}

module.exports.f = function (C) {
  return new PromiseCapability(C);
};


/***/ }),

/***/ "aa77":
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__("5ca1");
var defined = __webpack_require__("be13");
var fails = __webpack_require__("79e5");
var spaces = __webpack_require__("fdef");
var space = '[' + spaces + ']';
var non = '\u200b\u0085';
var ltrim = RegExp('^' + space + space + '*');
var rtrim = RegExp(space + space + '*$');

var exporter = function (KEY, exec, ALIAS) {
  var exp = {};
  var FORCE = fails(function () {
    return !!spaces[KEY]() || non[KEY]() != non;
  });
  var fn = exp[KEY] = FORCE ? exec(trim) : spaces[KEY];
  if (ALIAS) exp[ALIAS] = fn;
  $export($export.P + $export.F * FORCE, 'String', exp);
};

// 1 -> String#trimLeft
// 2 -> String#trimRight
// 3 -> String#trim
var trim = exporter.trim = function (string, TYPE) {
  string = String(defined(string));
  if (TYPE & 1) string = string.replace(ltrim, '');
  if (TYPE & 2) string = string.replace(rtrim, '');
  return string;
};

module.exports = exporter;


/***/ }),

/***/ "aae3":
/***/ (function(module, exports, __webpack_require__) {

// 7.2.8 IsRegExp(argument)
var isObject = __webpack_require__("d3f4");
var cof = __webpack_require__("2d95");
var MATCH = __webpack_require__("2b4c")('match');
module.exports = function (it) {
  var isRegExp;
  return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : cof(it) == 'RegExp');
};


/***/ }),

/***/ "ab57":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("2350")(false);
// imports


// module
exports.push([module.i, ".aplayer{background:#fff;font-family:Arial,Helvetica,sans-serif;margin:5px;-webkit-box-shadow:0 2px 2px 0 rgba(0,0,0,.07),0 1px 5px 0 rgba(0,0,0,.1);box-shadow:0 2px 2px 0 rgba(0,0,0,.07),0 1px 5px 0 rgba(0,0,0,.1);border-radius:2px;overflow:hidden;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;line-height:normal;position:relative}.aplayer *{-webkit-box-sizing:content-box;box-sizing:content-box}.aplayer svg{width:100%;height:100%}.aplayer svg circle,.aplayer svg path{fill:#fff}.aplayer.aplayer-withlist .aplayer-info{border-bottom:1px solid #e9e9e9}.aplayer.aplayer-withlist .aplayer-list{display:block;width:100%}.aplayer.aplayer-withlist .aplayer-icon-order,.aplayer.aplayer-withlist .aplayer-info .aplayer-controller .aplayer-time .aplayer-icon.aplayer-icon-menu{display:inline}.aplayer.aplayer-withlrc .aplayer-pic{height:90px;width:90px}.aplayer.aplayer-withlrc .aplayer-info{margin-left:90px;height:90px;padding:10px 7px 0 7px}.aplayer.aplayer-withlrc .aplayer-lrc{display:block}.aplayer.aplayer-narrow{width:66px}.aplayer.aplayer-narrow .aplayer-info,.aplayer.aplayer-narrow .aplayer-list{display:none}.aplayer.aplayer-narrow .aplayer-body,.aplayer.aplayer-narrow .aplayer-pic{height:66px;width:66px}.aplayer.aplayer-fixed{position:fixed;bottom:0;left:0;right:0;margin:0;z-index:99;overflow:visible;max-width:400px;-webkit-box-shadow:none;box-shadow:none}.aplayer.aplayer-fixed .aplayer-list{margin-bottom:65px;border:1px solid #eee;border-bottom:none;-webkit-box-sizing:border-box;box-sizing:border-box}.aplayer.aplayer-fixed .aplayer-body{position:fixed;bottom:0;left:0;right:0;margin:0;z-index:99;background:#fff;padding-right:18px;-webkit-transition:width .3s ease;transition:width .3s ease;max-width:400px;width:calc(100% - 18px)}.aplayer.aplayer-fixed .aplayer-lrc{display:block;position:fixed;bottom:10px;left:0;right:0;margin:0;z-index:98;pointer-events:none;text-shadow:-1px -1px 0 #fff}.aplayer.aplayer-fixed .aplayer-lrc:after,.aplayer.aplayer-fixed .aplayer-lrc:before{display:none}.aplayer.aplayer-fixed .aplayer-info{-webkit-transform:scaleX(1);transform:scaleX(1);-webkit-transform-origin:0 0;transform-origin:0 0;-webkit-transition:all .3s ease;transition:all .3s ease;border-bottom:none;border-top:1px solid #e9e9e9}.aplayer.aplayer-fixed .aplayer-info .aplayer-music{width:calc(100% - 105px)}.aplayer.aplayer-fixed .aplayer-miniswitcher{display:block}.aplayer.aplayer-fixed.aplayer-narrow .aplayer-info{display:block;-webkit-transform:scaleX(0);transform:scaleX(0)}.aplayer.aplayer-fixed.aplayer-narrow .aplayer-body{width:66px!important}.aplayer.aplayer-fixed.aplayer-narrow .aplayer-miniswitcher .aplayer-icon{-webkit-transform:rotateY(0);transform:rotateY(0)}.aplayer.aplayer-fixed .aplayer-icon-back,.aplayer.aplayer-fixed .aplayer-icon-forward,.aplayer.aplayer-fixed .aplayer-icon-lrc,.aplayer.aplayer-fixed .aplayer-icon-play{display:inline-block}.aplayer.aplayer-fixed .aplayer-icon-back,.aplayer.aplayer-fixed .aplayer-icon-forward,.aplayer.aplayer-fixed .aplayer-icon-menu,.aplayer.aplayer-fixed .aplayer-icon-play{position:absolute;bottom:27px;width:20px;height:20px}.aplayer.aplayer-fixed .aplayer-icon-back{right:75px}.aplayer.aplayer-fixed .aplayer-icon-play{right:50px}.aplayer.aplayer-fixed .aplayer-icon-forward{right:25px}.aplayer.aplayer-fixed .aplayer-icon-menu{right:0}.aplayer.aplayer-arrow .aplayer-icon-loop,.aplayer.aplayer-arrow .aplayer-icon-order,.aplayer.aplayer-mobile .aplayer-icon-volume-down,.aplayer.aplayer-mobile .aplayer-icon-volume-up{display:none}.aplayer.aplayer-loading .aplayer-info .aplayer-controller .aplayer-loading-icon{display:block}.aplayer.aplayer-loading .aplayer-info .aplayer-controller .aplayer-bar-wrap .aplayer-bar .aplayer-played .aplayer-thumb{-webkit-transform:scale(1);transform:scale(1)}.aplayer .aplayer-body{position:relative}.aplayer .aplayer-icon{width:15px;height:15px;border:none;background-color:transparent;outline:none;cursor:pointer;opacity:.8;vertical-align:middle;padding:0;font-size:12px;margin:0;display:inline-block}.aplayer .aplayer-icon path{-webkit-transition:all .2s ease-in-out;transition:all .2s ease-in-out}.aplayer .aplayer-icon-back,.aplayer .aplayer-icon-forward,.aplayer .aplayer-icon-lrc,.aplayer .aplayer-icon-order,.aplayer .aplayer-icon-play{display:none}.aplayer .aplayer-icon-lrc-inactivity svg{opacity:.4}.aplayer .aplayer-icon-forward{-webkit-transform:rotate(180deg);transform:rotate(180deg)}.aplayer .aplayer-lrc-content{display:none}.aplayer .aplayer-pic{position:relative;float:left;height:66px;width:66px;background-size:cover;background-position:50%;-webkit-transition:all .3s ease;transition:all .3s ease;cursor:pointer}.aplayer .aplayer-pic:hover .aplayer-button{opacity:1}.aplayer .aplayer-pic .aplayer-button{position:absolute;border-radius:50%;opacity:.8;text-shadow:0 1px 1px rgba(0,0,0,.2);-webkit-box-shadow:0 1px 1px rgba(0,0,0,.2);box-shadow:0 1px 1px rgba(0,0,0,.2);background:rgba(0,0,0,.2);-webkit-transition:all .1s ease;transition:all .1s ease}.aplayer .aplayer-pic .aplayer-button path{fill:#fff}.aplayer .aplayer-pic .aplayer-hide{display:none}.aplayer .aplayer-pic .aplayer-play{width:26px;height:26px;border:2px solid #fff;bottom:50%;right:50%;margin:0 -15px -15px 0}.aplayer .aplayer-pic .aplayer-play svg{position:absolute;top:3px;left:4px;height:20px;width:20px}.aplayer .aplayer-pic .aplayer-pause{width:16px;height:16px;border:2px solid #fff;bottom:4px;right:4px}.aplayer .aplayer-pic .aplayer-pause svg{position:absolute;top:2px;left:2px;height:12px;width:12px}.aplayer .aplayer-info{margin-left:66px;padding:14px 7px 0 10px;height:66px;-webkit-box-sizing:border-box;box-sizing:border-box}.aplayer .aplayer-info .aplayer-music{overflow:hidden;white-space:nowrap;text-overflow:ellipsis;margin:0 0 13px 5px;-webkit-user-select:text;-moz-user-select:text;-ms-user-select:text;user-select:text;cursor:default;padding-bottom:2px;height:20px}.aplayer .aplayer-info .aplayer-music .aplayer-title{font-size:14px}.aplayer .aplayer-info .aplayer-music .aplayer-author{font-size:12px;color:#666}.aplayer .aplayer-info .aplayer-controller{position:relative;display:-webkit-box;display:-ms-flexbox;display:flex}.aplayer .aplayer-info .aplayer-controller .aplayer-bar-wrap{margin:0 0 0 5px;padding:4px 0;cursor:pointer!important;-webkit-box-flex:1;-ms-flex:1;flex:1}.aplayer .aplayer-info .aplayer-controller .aplayer-bar-wrap:hover .aplayer-bar .aplayer-played .aplayer-thumb{-webkit-transform:scale(1);transform:scale(1)}.aplayer .aplayer-info .aplayer-controller .aplayer-bar-wrap .aplayer-bar{position:relative;height:2px;width:100%;background:#cdcdcd}.aplayer .aplayer-info .aplayer-controller .aplayer-bar-wrap .aplayer-bar .aplayer-loaded{position:absolute;left:0;top:0;bottom:0;background:#aaa;height:2px;-webkit-transition:all .5s ease;transition:all .5s ease}.aplayer .aplayer-info .aplayer-controller .aplayer-bar-wrap .aplayer-bar .aplayer-played{position:absolute;left:0;top:0;bottom:0;height:2px;-webkit-transition:background-color .3s ease;transition:background-color .3s ease}.aplayer .aplayer-info .aplayer-controller .aplayer-bar-wrap .aplayer-bar .aplayer-played .aplayer-thumb{position:absolute;top:0;right:5px;margin-top:-4px;margin-right:-10px;height:10px;width:10px;border-radius:50%;cursor:pointer;-webkit-transition:all .3s ease-in-out;transition:all .3s ease-in-out;-webkit-transform:scale(0);transform:scale(0)}.aplayer .aplayer-info .aplayer-controller .aplayer-time{position:relative;right:0;bottom:4px;height:17px;color:#999;font-size:11px;padding-left:7px}.aplayer .aplayer-info .aplayer-controller .aplayer-time .aplayer-time-inner{vertical-align:middle}.aplayer .aplayer-info .aplayer-controller .aplayer-time .aplayer-icon{cursor:pointer;-webkit-transition:all .2s ease;transition:all .2s ease}.aplayer .aplayer-info .aplayer-controller .aplayer-time .aplayer-icon path{fill:#666}.aplayer .aplayer-info .aplayer-controller .aplayer-time .aplayer-icon.aplayer-icon-loop{margin-right:2px}.aplayer .aplayer-info .aplayer-controller .aplayer-time .aplayer-icon:hover path{fill:#000}.aplayer .aplayer-info .aplayer-controller .aplayer-time .aplayer-icon.aplayer-icon-menu,.aplayer .aplayer-info .aplayer-controller .aplayer-time.aplayer-time-narrow .aplayer-icon-menu,.aplayer .aplayer-info .aplayer-controller .aplayer-time.aplayer-time-narrow .aplayer-icon-mode{display:none}.aplayer .aplayer-info .aplayer-controller .aplayer-volume-wrap{position:relative;display:inline-block;margin-left:3px;cursor:pointer!important}.aplayer .aplayer-info .aplayer-controller .aplayer-volume-wrap:hover .aplayer-volume-bar-wrap{height:40px}.aplayer .aplayer-info .aplayer-controller .aplayer-volume-wrap .aplayer-volume-bar-wrap{position:absolute;bottom:15px;right:-3px;width:25px;height:0;z-index:99;overflow:hidden;-webkit-transition:all .2s ease-in-out;transition:all .2s ease-in-out}.aplayer .aplayer-info .aplayer-controller .aplayer-volume-wrap .aplayer-volume-bar-wrap.aplayer-volume-bar-wrap-active{height:40px}.aplayer .aplayer-info .aplayer-controller .aplayer-volume-wrap .aplayer-volume-bar-wrap .aplayer-volume-bar{position:absolute;bottom:0;right:10px;width:5px;height:35px;background:#aaa;border-radius:2.5px;overflow:hidden}.aplayer .aplayer-info .aplayer-controller .aplayer-volume-wrap .aplayer-volume-bar-wrap .aplayer-volume-bar .aplayer-volume{position:absolute;bottom:0;right:0;width:5px;-webkit-transition:all .1s ease;transition:all .1s ease}.aplayer .aplayer-info .aplayer-controller .aplayer-loading-icon{display:none}.aplayer .aplayer-info .aplayer-controller .aplayer-loading-icon svg{position:absolute;-webkit-animation:rotate 1s linear infinite;animation:rotate 1s linear infinite}.aplayer .aplayer-lrc{display:none;position:relative;height:30px;text-align:center;overflow:hidden;margin:-10px 0 7px}.aplayer .aplayer-lrc:before{top:0;height:10%;background:-webkit-gradient(linear,left top,left bottom,from(#fff),to(hsla(0,0%,100%,0)));background:linear-gradient(180deg,#fff 0,hsla(0,0%,100%,0));filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\"#ffffff\",endColorstr=\"#00ffffff\",GradientType=0)}.aplayer .aplayer-lrc:after,.aplayer .aplayer-lrc:before{position:absolute;z-index:1;display:block;overflow:hidden;width:100%;content:\" \"}.aplayer .aplayer-lrc:after{bottom:0;height:33%;background:-webkit-gradient(linear,left top,left bottom,from(hsla(0,0%,100%,0)),to(hsla(0,0%,100%,.8)));background:linear-gradient(180deg,hsla(0,0%,100%,0) 0,hsla(0,0%,100%,.8));filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\"#00ffffff\",endColorstr=\"#ccffffff\",GradientType=0)}.aplayer .aplayer-lrc p{font-size:12px;color:#666;line-height:16px!important;height:16px!important;padding:0!important;margin:0!important;-webkit-transition-property:font-size,color,opacity;transition-property:font-size,color,opacity;-webkit-transition-timing-function:ease-out;transition-timing-function:ease-out;-webkit-transition-duration:.5s;transition-duration:.5s;opacity:.4;overflow:hidden}.aplayer .aplayer-lrc p.aplayer-lrc-current{opacity:1;overflow:visible;height:auto!important;min-height:16px}.aplayer .aplayer-lrc.aplayer-lrc-hide{display:none}.aplayer .aplayer-lrc .aplayer-lrc-contents{width:100%;-webkit-transition:all .5s ease-out;transition:all .5s ease-out;-webkit-user-select:text;-moz-user-select:text;-ms-user-select:text;user-select:text;cursor:default}.aplayer .aplayer-list{overflow:auto;-webkit-transition:all .5s ease;transition:all .5s ease;will-change:height;display:none;overflow:hidden;list-style-type:none;margin:0;padding:0;overflow-y:auto}.aplayer .aplayer-list::-webkit-scrollbar{width:5px}.aplayer .aplayer-list::-webkit-scrollbar-thumb{border-radius:3px;background-color:#eee}.aplayer .aplayer-list::-webkit-scrollbar-thumb:hover{background-color:#ccc}.aplayer .aplayer-list li{position:relative;height:32px;line-height:32px;padding:0 15px;font-size:12px;border-top:1px solid #e9e9e9;cursor:pointer;-webkit-transition:all .2s ease;transition:all .2s ease;overflow:hidden;margin:0}.aplayer .aplayer-list li:first-child{border-top:none}.aplayer .aplayer-list li:hover{background:#efefef}.aplayer .aplayer-list li.aplayer-list-light{background:#e9e9e9}.aplayer .aplayer-list li.aplayer-list-light .aplayer-list-cur{display:inline-block}.aplayer .aplayer-list li .aplayer-list-cur{display:none;width:3px;height:22px;position:absolute;left:0;top:5px;-webkit-transition:background-color .3s ease;transition:background-color .3s ease;cursor:pointer}.aplayer .aplayer-list li .aplayer-list-index{color:#666;margin-right:12px;cursor:pointer}.aplayer .aplayer-list li .aplayer-list-author{color:#666;float:right;cursor:pointer}.aplayer .aplayer-notice{opacity:0;position:absolute;z-index:1;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);font-size:12px;border-radius:4px;padding:5px 10px;-webkit-transition:all .3s ease-in-out;transition:all .3s ease-in-out;overflow:hidden;color:#fff;pointer-events:none;background-color:#f4f4f5;color:#909399}.aplayer .aplayer-miniswitcher{display:none;position:absolute;top:0;right:0;bottom:0;height:100%;background:#e6e6e6;width:18px;border-radius:0 2px 2px 0}.aplayer .aplayer-miniswitcher .aplayer-icon{height:100%;width:100%;-webkit-transform:rotateY(180deg);transform:rotateY(180deg);-webkit-transition:all .3s ease;transition:all .3s ease}.aplayer .aplayer-miniswitcher .aplayer-icon path{fill:#666}.aplayer .aplayer-miniswitcher .aplayer-icon:hover path{fill:#000}@-webkit-keyframes aplayer-roll{0%{left:0}to{left:-100%}}@keyframes aplayer-roll{0%{left:0}to{left:-100%}}@-webkit-keyframes rotate{0%{-webkit-transform:rotate(0);transform:rotate(0)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@keyframes rotate{0%{-webkit-transform:rotate(0);transform:rotate(0)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}", ""]);

// exports


/***/ }),

/***/ "ac6a":
/***/ (function(module, exports, __webpack_require__) {

var $iterators = __webpack_require__("cadf");
var getKeys = __webpack_require__("0d58");
var redefine = __webpack_require__("2aba");
var global = __webpack_require__("7726");
var hide = __webpack_require__("32e9");
var Iterators = __webpack_require__("84f2");
var wks = __webpack_require__("2b4c");
var ITERATOR = wks('iterator');
var TO_STRING_TAG = wks('toStringTag');
var ArrayValues = Iterators.Array;

var DOMIterables = {
  CSSRuleList: true, // TODO: Not spec compliant, should be false.
  CSSStyleDeclaration: false,
  CSSValueList: false,
  ClientRectList: false,
  DOMRectList: false,
  DOMStringList: false,
  DOMTokenList: true,
  DataTransferItemList: false,
  FileList: false,
  HTMLAllCollection: false,
  HTMLCollection: false,
  HTMLFormElement: false,
  HTMLSelectElement: false,
  MediaList: true, // TODO: Not spec compliant, should be false.
  MimeTypeArray: false,
  NamedNodeMap: false,
  NodeList: true,
  PaintRequestList: false,
  Plugin: false,
  PluginArray: false,
  SVGLengthList: false,
  SVGNumberList: false,
  SVGPathSegList: false,
  SVGPointList: false,
  SVGStringList: false,
  SVGTransformList: false,
  SourceBufferList: false,
  StyleSheetList: true, // TODO: Not spec compliant, should be false.
  TextTrackCueList: false,
  TextTrackList: false,
  TouchList: false
};

for (var collections = getKeys(DOMIterables), i = 0; i < collections.length; i++) {
  var NAME = collections[i];
  var explicit = DOMIterables[NAME];
  var Collection = global[NAME];
  var proto = Collection && Collection.prototype;
  var key;
  if (proto) {
    if (!proto[ITERATOR]) hide(proto, ITERATOR, ArrayValues);
    if (!proto[TO_STRING_TAG]) hide(proto, TO_STRING_TAG, NAME);
    Iterators[NAME] = ArrayValues;
    if (explicit) for (key in $iterators) if (!proto[key]) redefine(proto, key, $iterators[key], true);
  }
}


/***/ }),

/***/ "adec":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 28 32"}},[_c('path',{attrs:{"d":"M13.728 6.272v19.456q0 .448-.352.8t-.8.32-.8-.32l-5.952-5.952H1.152q-.48 0-.8-.352t-.352-.8v-6.848q0-.48.352-.8t.8-.352h4.672l5.952-5.952q.32-.32.8-.32t.8.32.352.8z"}})])
      }
    
      });
    

/***/ }),

/***/ "b349":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __assign = (this && this.__assign) || Object.assign || function(t) {
    for (var s, i = 1, n = arguments.length; i < n; i++) {
        s = arguments[i];
        for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
            t[p] = s[p];
    }
    return t;
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var vue_1 = __importDefault(__webpack_require__("8bbf"));
var Component = /** @class */ (function (_super) {
    __extends(Component, _super);
    function Component() {
        var _this = _super !== null && _super.apply(this, arguments) || this;
        _this._tsxattrs = undefined;
        _this.$scopedSlots = undefined;
        return _this;
    }
    return Component;
}(vue_1.default));
exports.Component = Component;
/**
 * Create component from component options (Compatible with Vue.extend)
 */
function createComponent(opts) {
    return vue_1.default.extend(opts);
}
exports.createComponent = createComponent;
var factoryImpl = {
    convert: function (c) { return c; },
    extendFrom: function (c) { return c; }
};
/**
 * Specify Props and Event types of component
 *
 * Usage:
 *  // Get TSX-supported component with props(`name`, `value`) and event(`onInput`)
 *  const NewComponent = tsx.ofType<{ name: string, value: string }, { onInput: string }>.convert(Component);
 */
function ofType() {
    return factoryImpl;
}
exports.ofType = ofType;
function withNativeOn(componentType) {
    return componentType;
}
exports.withNativeOn = withNativeOn;
function withHtmlAttrs(componentType) {
    return componentType;
}
exports.withHtmlAttrs = withHtmlAttrs;
function withUnknownProps(componentType) {
    return componentType;
}
exports.withUnknownProps = withUnknownProps;
function createComponentFactory(base, mixins) {
    return {
        create: function (options) {
            var mergedMixins = options.mixins
                ? options.mixins.concat(mixins) : mixins;
            return base.extend(__assign({}, options, { mixins: mergedMixins }));
        },
        mixin: function (mixinObject) {
            return createComponentFactory(base, mixins.concat([mixinObject]));
        }
    };
}
function createExtendableComponentFactory() {
    return {
        create: function (options) {
            return vue_1.default.extend(options);
        },
        extendFrom: function (base) {
            return createComponentFactory(base, []);
        },
        mixin: function (mixinObject) {
            return createComponentFactory(vue_1.default, [mixinObject]);
        }
    };
}
exports.componentFactory = createExtendableComponentFactory();
function componentFactoryOf() {
    return exports.componentFactory;
}
exports.componentFactoryOf = componentFactoryOf;
/**
 * Shorthand of `componentFactory.create`
 */
exports.component = exports.componentFactory.create;
exports.extendFrom = exports.componentFactory.extendFrom;
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBpLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXMiOlsiLi4vc3JjL2FwaS50cyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQUFBLDRDQUlhO0FBK0JiO0lBSVUsNkJBQUc7SUFKYjtRQUFBLHFFQVdDO1FBTkMsZUFBUyxHQUlMLFNBQWdCLENBQUM7UUFDckIsa0JBQVksR0FBZ0MsU0FBZ0IsQ0FBQzs7SUFDL0QsQ0FBQztJQUFELGdCQUFDO0FBQUQsQ0FBQyxBQVhELENBSVUsYUFBRyxHQU9aO0FBWFksOEJBQVM7QUFhdEI7O0dBRUc7QUFDSCx5QkFDRSxJQUF3RDtJQUV4RCxPQUFPLGFBQUcsQ0FBQyxNQUFNLENBQUMsSUFBVyxDQUFRLENBQUM7QUFDeEMsQ0FBQztBQUpELDBDQUlDO0FBV0QsSUFBTSxXQUFXLEdBQUc7SUFDbEIsT0FBTyxFQUFFLFVBQUMsQ0FBTSxJQUFLLE9BQUEsQ0FBQyxFQUFELENBQUM7SUFDdEIsVUFBVSxFQUFFLFVBQUMsQ0FBTSxJQUFLLE9BQUEsQ0FBQyxFQUFELENBQUM7Q0FDMUIsQ0FBQztBQUVGOzs7Ozs7R0FNRztBQUNIO0lBS0UsT0FBTyxXQUFXLENBQUM7QUFDckIsQ0FBQztBQU5ELHdCQU1DO0FBRUQsc0JBQ0UsYUFBaUI7SUFFakIsT0FBTyxhQUFvQixDQUFDO0FBQzlCLENBQUM7QUFKRCxvQ0FJQztBQUVELHVCQUNFLGFBQWlCO0lBRWpCLE9BQU8sYUFBb0IsQ0FBQztBQUM5QixDQUFDO0FBSkQsc0NBSUM7QUFFRCwwQkFDRSxhQUFpQjtJQUVqQixPQUFPLGFBQW9CLENBQUM7QUFDOUIsQ0FBQztBQUpELDRDQUlDO0FBeUhELGdDQUNFLElBQWdCLEVBQ2hCLE1BQWE7SUFFYixPQUFPO1FBQ0wsTUFBTSxFQUFOLFVBQU8sT0FBWTtZQUNqQixJQUFNLFlBQVksR0FBRyxPQUFPLENBQUMsTUFBTTtnQkFDakMsQ0FBQyxDQUFLLE9BQU8sQ0FBQyxNQUFNLFFBQUssTUFBTSxFQUMvQixDQUFDLENBQUMsTUFBTSxDQUFDO1lBQ1gsT0FBTyxJQUFJLENBQUMsTUFBTSxjQUFNLE9BQU8sSUFBRSxNQUFNLEVBQUUsWUFBWSxJQUFHLENBQUM7UUFDM0QsQ0FBQztRQUNELEtBQUssRUFBTCxVQUFNLFdBQWdCO1lBQ3BCLE9BQU8sc0JBQXNCLENBQUMsSUFBSSxFQUFNLE1BQU0sU0FBRSxXQUFXLEdBQUUsQ0FBQztRQUNoRSxDQUFDO0tBQ0YsQ0FBQztBQUNKLENBQUM7QUFFRDtJQU9FLE9BQU87UUFDTCxNQUFNLEVBQU4sVUFBTyxPQUFZO1lBQ2pCLE9BQU8sYUFBRyxDQUFDLE1BQU0sQ0FBQyxPQUFPLENBQUMsQ0FBQztRQUM3QixDQUFDO1FBQ0QsVUFBVSxFQUFWLFVBQVcsSUFBZ0I7WUFDekIsT0FBTyxzQkFBc0IsQ0FBQyxJQUFJLEVBQUUsRUFBRSxDQUFDLENBQUM7UUFDMUMsQ0FBQztRQUNELEtBQUssRUFBTCxVQUFNLFdBQWdCO1lBQ3BCLE9BQU8sc0JBQXNCLENBQUMsYUFBRyxFQUFFLENBQUMsV0FBVyxDQUFDLENBQUMsQ0FBQztRQUNwRCxDQUFDO0tBQ0YsQ0FBQztBQUNKLENBQUM7QUFFWSxRQUFBLGdCQUFnQixHQU16QixnQ0FBZ0MsRUFBRSxDQUFDO0FBRXZDO0lBVUUsT0FBTyx3QkFBdUIsQ0FBQztBQUNqQyxDQUFDO0FBWEQsZ0RBV0M7QUFFRDs7R0FFRztBQUNVLFFBQUEsU0FBUyxHQUFHLHdCQUFnQixDQUFDLE1BQU0sQ0FBQztBQUNwQyxRQUFBLFVBQVUsR0FBRyx3QkFBZ0IsQ0FBQyxVQUFVLENBQUMifQ==

/***/ }),

/***/ "bcaa":
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__("cb7c");
var isObject = __webpack_require__("d3f4");
var newPromiseCapability = __webpack_require__("a5b8");

module.exports = function (C, x) {
  anObject(C);
  if (isObject(x) && x.constructor === C) return x;
  var promiseCapability = newPromiseCapability.f(C);
  var resolve = promiseCapability.resolve;
  resolve(x);
  return promiseCapability.promise;
};


/***/ }),

/***/ "bdba":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 28 32"}},[_c('path',{attrs:{"d":"M13.728 6.272v19.456q0 .448-.352.8t-.8.32-.8-.32l-5.952-5.952H1.152q-.48 0-.8-.352t-.352-.8v-6.848q0-.48.352-.8t.8-.352h4.672l5.952-5.952q.32-.32.8-.32t.8.32.352.8zM20.576 16q0 1.344-.768 2.528t-2.016 1.664q-.16.096-.448.096-.448 0-.8-.32t-.32-.832q0-.384.192-.64t.544-.448.608-.384.512-.64.192-1.024-.192-1.024-.512-.64-.608-.384-.544-.448-.192-.64q0-.48.32-.832t.8-.32q.288 0 .448.096 1.248.48 2.016 1.664T20.576 16z"}})])
      }
    
      });
    

/***/ }),

/***/ "be13":
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on  " + it);
  return it;
};


/***/ }),

/***/ "bf5c":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 32 32"}},[_c('path',{attrs:{"d":"M22 16L11.895 5.4 10 7.387 18.211 16 10 24.612l1.895 1.988 8.211-8.613z"}})])
      }
    
      });
    

/***/ }),

/***/ "c366":
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__("6821");
var toLength = __webpack_require__("9def");
var toAbsoluteIndex = __webpack_require__("77f1");
module.exports = function (IS_INCLUDES) {
  return function ($this, el, fromIndex) {
    var O = toIObject($this);
    var length = toLength(O.length);
    var index = toAbsoluteIndex(fromIndex, length);
    var value;
    // Array#includes uses SameValueZero equality algorithm
    // eslint-disable-next-line no-self-compare
    if (IS_INCLUDES && el != el) while (length > index) {
      value = O[index++];
      // eslint-disable-next-line no-self-compare
      if (value != value) return true;
    // Array#indexOf ignores holes, Array#includes - not
    } else for (;length > index; index++) if (IS_INCLUDES || index in O) {
      if (O[index] === el) return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};


/***/ }),

/***/ "c3ab":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 29 32"}},[_c('path',{attrs:{"d":"M2.667 7.027l1.707-1.693 22.293 22.293-1.693 1.707-4-4H9.334v4l-5.333-5.333 5.333-5.333v4h8.973l-8.973-8.973v.973H6.667v-3.64l-4-4zm20 10.306h2.667v5.573l-2.667-2.667v-2.907zm0-10.666v-4L28 8l-5.333 5.333v-4H11.76L9.093 6.666h13.573z"}})])
      }
    
      });
    

/***/ }),

/***/ "c5f6":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var global = __webpack_require__("7726");
var has = __webpack_require__("69a8");
var cof = __webpack_require__("2d95");
var inheritIfRequired = __webpack_require__("5dbc");
var toPrimitive = __webpack_require__("6a99");
var fails = __webpack_require__("79e5");
var gOPN = __webpack_require__("9093").f;
var gOPD = __webpack_require__("11e9").f;
var dP = __webpack_require__("86cc").f;
var $trim = __webpack_require__("aa77").trim;
var NUMBER = 'Number';
var $Number = global[NUMBER];
var Base = $Number;
var proto = $Number.prototype;
// Opera ~12 has broken Object#toString
var BROKEN_COF = cof(__webpack_require__("2aeb")(proto)) == NUMBER;
var TRIM = 'trim' in String.prototype;

// 7.1.3 ToNumber(argument)
var toNumber = function (argument) {
  var it = toPrimitive(argument, false);
  if (typeof it == 'string' && it.length > 2) {
    it = TRIM ? it.trim() : $trim(it, 3);
    var first = it.charCodeAt(0);
    var third, radix, maxCode;
    if (first === 43 || first === 45) {
      third = it.charCodeAt(2);
      if (third === 88 || third === 120) return NaN; // Number('+0x1') should be NaN, old V8 fix
    } else if (first === 48) {
      switch (it.charCodeAt(1)) {
        case 66: case 98: radix = 2; maxCode = 49; break; // fast equal /^0b[01]+$/i
        case 79: case 111: radix = 8; maxCode = 55; break; // fast equal /^0o[0-7]+$/i
        default: return +it;
      }
      for (var digits = it.slice(2), i = 0, l = digits.length, code; i < l; i++) {
        code = digits.charCodeAt(i);
        // parseInt parses a string to a first unavailable symbol
        // but ToNumber should return NaN if a string contains unavailable symbols
        if (code < 48 || code > maxCode) return NaN;
      } return parseInt(digits, radix);
    }
  } return +it;
};

if (!$Number(' 0o1') || !$Number('0b1') || $Number('+0x1')) {
  $Number = function Number(value) {
    var it = arguments.length < 1 ? 0 : value;
    var that = this;
    return that instanceof $Number
      // check on 1..constructor(foo) case
      && (BROKEN_COF ? fails(function () { proto.valueOf.call(that); }) : cof(that) != NUMBER)
        ? inheritIfRequired(new Base(toNumber(it)), that, $Number) : toNumber(it);
  };
  for (var keys = __webpack_require__("9e1e") ? gOPN(Base) : (
    // ES3:
    'MAX_VALUE,MIN_VALUE,NaN,NEGATIVE_INFINITY,POSITIVE_INFINITY,' +
    // ES6 (in case, if modules with ES6 Number statics required before):
    'EPSILON,isFinite,isInteger,isNaN,isSafeInteger,MAX_SAFE_INTEGER,' +
    'MIN_SAFE_INTEGER,parseFloat,parseInt,isInteger'
  ).split(','), j = 0, key; keys.length > j; j++) {
    if (has(Base, key = keys[j]) && !has($Number, key)) {
      dP($Number, key, gOPD(Base, key));
    }
  }
  $Number.prototype = proto;
  proto.constructor = $Number;
  __webpack_require__("2aba")(global, NUMBER, $Number);
}


/***/ }),

/***/ "c69a":
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__("9e1e") && !__webpack_require__("79e5")(function () {
  return Object.defineProperty(__webpack_require__("230e")('div'), 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ "ca5a":
/***/ (function(module, exports) {

var id = 0;
var px = Math.random();
module.exports = function (key) {
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};


/***/ }),

/***/ "cadf":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var addToUnscopables = __webpack_require__("9c6c");
var step = __webpack_require__("d53b");
var Iterators = __webpack_require__("84f2");
var toIObject = __webpack_require__("6821");

// 22.1.3.4 Array.prototype.entries()
// 22.1.3.13 Array.prototype.keys()
// 22.1.3.29 Array.prototype.values()
// 22.1.3.30 Array.prototype[@@iterator]()
module.exports = __webpack_require__("01f9")(Array, 'Array', function (iterated, kind) {
  this._t = toIObject(iterated); // target
  this._i = 0;                   // next index
  this._k = kind;                // kind
// 22.1.5.2.1 %ArrayIteratorPrototype%.next()
}, function () {
  var O = this._t;
  var kind = this._k;
  var index = this._i++;
  if (!O || index >= O.length) {
    this._t = undefined;
    return step(1);
  }
  if (kind == 'keys') return step(0, index);
  if (kind == 'values') return step(0, O[index]);
  return step(0, [index, O[index]]);
}, 'values');

// argumentsList[@@iterator] is %ArrayProto_values% (9.4.4.6, 9.4.4.7)
Iterators.Arguments = Iterators.Array;

addToUnscopables('keys');
addToUnscopables('values');
addToUnscopables('entries');


/***/ }),

/***/ "cb7c":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("d3f4");
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};


/***/ }),

/***/ "cd1c":
/***/ (function(module, exports, __webpack_require__) {

// 9.4.2.3 ArraySpeciesCreate(originalArray, length)
var speciesConstructor = __webpack_require__("e853");

module.exports = function (original, length) {
  return new (speciesConstructor(original))(length);
};


/***/ }),

/***/ "ce10":
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__("69a8");
var toIObject = __webpack_require__("6821");
var arrayIndexOf = __webpack_require__("c366")(false);
var IE_PROTO = __webpack_require__("613b")('IE_PROTO');

module.exports = function (object, names) {
  var O = toIObject(object);
  var i = 0;
  var result = [];
  var key;
  for (key in O) if (key != IE_PROTO) has(O, key) && result.push(key);
  // Don't enum bug & hidden keys
  while (names.length > i) if (has(O, key = names[i++])) {
    ~arrayIndexOf(result, key) || result.push(key);
  }
  return result;
};


/***/ }),

/***/ "d263":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// B.2.3.6 String.prototype.fixed()
__webpack_require__("386b")('fixed', function (createHTML) {
  return function fixed() {
    return createHTML(this, 'tt', '', '');
  };
});


/***/ }),

/***/ "d2c8":
/***/ (function(module, exports, __webpack_require__) {

// helper for String#{startsWith, endsWith, includes}
var isRegExp = __webpack_require__("aae3");
var defined = __webpack_require__("be13");

module.exports = function (that, searchString, NAME) {
  if (isRegExp(searchString)) throw TypeError('String#' + NAME + " doesn't accept regex!");
  return String(defined(that));
};


/***/ }),

/***/ "d3f4":
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),

/***/ "d53b":
/***/ (function(module, exports) {

module.exports = function (done, value) {
  return { value: value, done: !!done };
};


/***/ }),

/***/ "d8e8":
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};


/***/ }),

/***/ "daf8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 17 32"}},[_c('path',{attrs:{"d":"M14.08 4.8q2.88 0 2.88 2.048v18.24q0 2.112-2.88 2.112t-2.88-2.112V6.848q0-2.048 2.88-2.048zm-11.2 0q2.88 0 2.88 2.048v18.24q0 2.112-2.88 2.112T0 25.088V6.848Q0 4.8 2.88 4.8z"}})])
      }
    
      });
    

/***/ }),

/***/ "dcbc":
/***/ (function(module, exports, __webpack_require__) {

var redefine = __webpack_require__("2aba");
module.exports = function (target, src, safe) {
  for (var key in src) redefine(target, key, src[key], safe);
  return target;
};


/***/ }),

/***/ "e11e":
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');


/***/ }),

/***/ "e853":
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__("d3f4");
var isArray = __webpack_require__("1169");
var SPECIES = __webpack_require__("2b4c")('species');

module.exports = function (original) {
  var C;
  if (isArray(original)) {
    C = original.constructor;
    // cross-realm fallback
    if (typeof C == 'function' && (C === Array || isArray(C.prototype))) C = undefined;
    if (isObject(C)) {
      C = C[SPECIES];
      if (C === null) C = undefined;
    }
  } return C === undefined ? Array : C;
};


/***/ }),

/***/ "ebd6":
/***/ (function(module, exports, __webpack_require__) {

// 7.3.20 SpeciesConstructor(O, defaultConstructor)
var anObject = __webpack_require__("cb7c");
var aFunction = __webpack_require__("d8e8");
var SPECIES = __webpack_require__("2b4c")('species');
module.exports = function (O, D) {
  var C = anObject(O).constructor;
  var S;
  return C === undefined || (S = anObject(C)[SPECIES]) == undefined ? D : aFunction(S);
};


/***/ }),

/***/ "f559":
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// 21.1.3.18 String.prototype.startsWith(searchString [, position ])

var $export = __webpack_require__("5ca1");
var toLength = __webpack_require__("9def");
var context = __webpack_require__("d2c8");
var STARTS_WITH = 'startsWith';
var $startsWith = ''[STARTS_WITH];

$export($export.P + $export.F * __webpack_require__("5147")(STARTS_WITH), 'String', {
  startsWith: function startsWith(searchString /* , position = 0 */) {
    var that = context(this, searchString, STARTS_WITH);
    var index = toLength(Math.min(arguments.length > 1 ? arguments[1] : undefined, that.length));
    var search = String(searchString);
    return $startsWith
      ? $startsWith.call(that, search, index)
      : that.slice(index, index + search.length) === search;
  }
});


/***/ }),

/***/ "f576":
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// https://github.com/tc39/proposal-string-pad-start-end
var $export = __webpack_require__("5ca1");
var $pad = __webpack_require__("2e08");
var userAgent = __webpack_require__("a25f");

// https://github.com/zloirock/core-js/issues/280
$export($export.P + $export.F * /Version\/10\.\d+(\.\d+)? Safari\//.test(userAgent), 'String', {
  padStart: function padStart(maxLength /* , fillString = ' ' */) {
    return $pad(this, maxLength, arguments.length > 1 ? arguments[1] : undefined, true);
  }
});


/***/ }),

/***/ "f605":
/***/ (function(module, exports) {

module.exports = function (it, Constructor, name, forbiddenField) {
  if (!(it instanceof Constructor) || (forbiddenField !== undefined && forbiddenField in it)) {
    throw TypeError(name + ': incorrect invocation!');
  } return it;
};


/***/ }),

/***/ "f751":
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __webpack_require__("5ca1");

$export($export.S + $export.F, 'Object', { assign: __webpack_require__("7333") });


/***/ }),

/***/ "f866":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

      /* harmony default export */ __webpack_exports__["default"] = ({
        functional: true,
        render: 
      function render(_h, _vm) {
        var _c=_vm._c;return _c('svg',{class:[_vm.data.class, _vm.data.staticClass],style:([_vm.data.style, _vm.data.staticStyle]),attrs:{"xmlns":"http://www.w3.org/2000/svg","viewBox":"0 0 29 32"}},[_c('path',{attrs:{"d":"M9.333 9.333h13.333v4L27.999 8l-5.333-5.333v4h-16v8h2.667V9.334zm13.334 13.334H9.334v-4L4.001 24l5.333 5.333v-4h16v-8h-2.667v5.333z"}})])
      }
    
      });
    

/***/ }),

/***/ "fab2":
/***/ (function(module, exports, __webpack_require__) {

var document = __webpack_require__("7726").document;
module.exports = document && document.documentElement;


/***/ }),

/***/ "fb15":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  var setPublicPath_i
  if ((setPublicPath_i = window.document.currentScript) && (setPublicPath_i = setPublicPath_i.src.match(/(.+\/)[^/]+\.js(\?.*)?$/))) {
    __webpack_require__.p = setPublicPath_i[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.object.assign.js
var es6_object_assign = __webpack_require__("f751");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      _defineProperty(target, key, source[key]);
    });
  }

  return target;
}
// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.number.constructor.js
var es6_number_constructor = __webpack_require__("c5f6");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = _objectWithoutPropertiesLoose(source, excluded);
  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js
function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  }
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
function _iterableToArray(iter) {
  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js



function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread();
}
// EXTERNAL MODULE: ./node_modules/core-js/modules/web.dom.iterable.js
var web_dom_iterable = __webpack_require__("ac6a");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.promise.js
var es6_promise = __webpack_require__("551c");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es7.array.includes.js
var es7_array_includes = __webpack_require__("6762");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.includes.js
var es6_string_includes = __webpack_require__("2fdb");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.function.name.js
var es6_function_name = __webpack_require__("7f7f");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.array.find.js
var es6_array_find = __webpack_require__("7514");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.regexp.to-string.js
var es6_regexp_to_string = __webpack_require__("6b54");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
  var _arr = [];
  var _n = true;
  var _d = false;
  var _e = undefined;

  try {
    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);

      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }

  return _arr;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js



function _slicedToArray(arr, i) {
  return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest();
}
// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.array.find-index.js
var es6_array_find_index = __webpack_require__("20d6");

// EXTERNAL MODULE: ./node_modules/regenerator-runtime/runtime.js
var runtime = __webpack_require__("96cf");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}
// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.fixed.js
var es6_string_fixed = __webpack_require__("d263");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/typeof.js
function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js


function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return _assertThisInitialized(self);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) _setPrototypeOf(subClass, superClass);
}
// EXTERNAL MODULE: ./node_modules/vue-tsx-support/lib/index.js
var lib = __webpack_require__("48d3");

// EXTERNAL MODULE: ./node_modules/vue-class-component/dist/vue-class-component.common.js
var vue_class_component_common = __webpack_require__("65d9");
var vue_class_component_common_default = /*#__PURE__*/__webpack_require__.n(vue_class_component_common);

// EXTERNAL MODULE: external {"commonjs":"vue","commonjs2":"vue","root":"Vue"}
var external_commonjs_vue_commonjs2_vue_root_Vue_ = __webpack_require__("8bbf");
var external_commonjs_vue_commonjs2_vue_root_Vue_default = /*#__PURE__*/__webpack_require__.n(external_commonjs_vue_commonjs2_vue_root_Vue_);

// CONCATENATED MODULE: ./node_modules/vue-property-decorator/lib/vue-property-decorator.js
/** vue-property-decorator verson 7.2.0 MIT LICENSE copyright 2018 kaorun343 */




/**
 * decorator of an inject
 * @param from key
 * @return PropertyDecorator
 */
function Inject(options) {
    return Object(vue_class_component_common["createDecorator"])(function (componentOptions, key) {
        if (typeof componentOptions.inject === 'undefined') {
            componentOptions.inject = {};
        }
        if (!Array.isArray(componentOptions.inject)) {
            componentOptions.inject[key] = options || key;
        }
    });
}
/**
 * decorator of a provide
 * @param key key
 * @return PropertyDecorator | void
 */
function Provide(key) {
    return Object(vue_class_component_common["createDecorator"])(function (componentOptions, k) {
        var provide = componentOptions.provide;
        if (typeof provide !== 'function' || !provide.managed) {
            var original_1 = componentOptions.provide;
            provide = componentOptions.provide = function () {
                var rv = Object.create((typeof original_1 === 'function' ? original_1.call(this) : original_1) || null);
                for (var i in provide.managed)
                    rv[provide.managed[i]] = this[i];
                return rv;
            };
            provide.managed = {};
        }
        provide.managed[k] = key || k;
    });
}
/**
 * decorator of model
 * @param  event event name
 * @param options options
 * @return PropertyDecorator
 */
function Model(event, options) {
    if (options === void 0) { options = {}; }
    return Object(vue_class_component_common["createDecorator"])(function (componentOptions, k) {
        (componentOptions.props || (componentOptions.props = {}))[k] = options;
        componentOptions.model = { prop: k, event: event || k };
    });
}
/**
 * decorator of a prop
 * @param  options the options for the prop
 * @return PropertyDecorator | void
 */
function Prop(options) {
    if (options === void 0) { options = {}; }
    return Object(vue_class_component_common["createDecorator"])(function (componentOptions, k) {
        (componentOptions.props || (componentOptions.props = {}))[k] = options;
    });
}
/**
 * decorator of a watch function
 * @param  path the path or the expression to observe
 * @param  WatchOption
 * @return MethodDecorator
 */
function Watch(path, options) {
    if (options === void 0) { options = {}; }
    var _a = options.deep, deep = _a === void 0 ? false : _a, _b = options.immediate, immediate = _b === void 0 ? false : _b;
    return Object(vue_class_component_common["createDecorator"])(function (componentOptions, handler) {
        if (typeof componentOptions.watch !== 'object') {
            componentOptions.watch = Object.create(null);
        }
        componentOptions.watch[path] = { handler: handler, deep: deep, immediate: immediate };
    });
}
// Code copied from Vue/src/shared/util.js
var hyphenateRE = /\B([A-Z])/g;
var hyphenate = function (str) { return str.replace(hyphenateRE, '-$1').toLowerCase(); };
/**
 * decorator of an event-emitter function
 * @param  event The name of the event
 * @return MethodDecorator
 */
function Emit(event) {
    return function (_target, key, descriptor) {
        key = hyphenate(key);
        var original = descriptor.value;
        descriptor.value = function emitter() {
            var _this = this;
            var args = [];
            for (var _i = 0; _i < arguments.length; _i++) {
                args[_i] = arguments[_i];
            }
            var emit = function (returnValue) {
                if (returnValue !== undefined)
                    args.unshift(returnValue);
                _this.$emit.apply(_this, [event || key].concat(args));
            };
            var returnValue = original.apply(this, args);
            if (isPromise(returnValue)) {
                returnValue.then(function (returnValue) {
                    emit(returnValue);
                });
            }
            else {
                emit(returnValue);
            }
        };
    };
}
function isPromise(obj) {
    return obj instanceof Promise || (obj && typeof obj.then === 'function');
}

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("4d26");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.array.iterator.js
var es6_array_iterator = __webpack_require__("cadf");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.object.keys.js
var es6_object_keys = __webpack_require__("456d");

// CONCATENATED MODULE: ./utils/index.ts

function sleep() {
  var delay = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
  return new Promise(function (resolve) {
    return setTimeout(resolve, delay);
  });
}
function eventLoop(target) {
  var timeout = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 3000;
  return new Promise(function (resolve, reject) {
    var startTime = new Date().getTime();
    var timerId = setInterval(function () {
      if (!target()) {
        if (timeout > 0 && new Date().getTime() - startTime > timeout) {
          reject();
          clearInterval(timerId);
        }

        return;
      }

      resolve();
      clearInterval(timerId);
    }, 100);
  });
}
// CONCATENATED MODULE: ./packages/@moefe/vue-audio/events.ts
/* harmony default export */ var events = (['abort', 'canplay', 'canplaythrough', 'durationchange', 'emptied', 'ended', 'error', 'loadeddata', 'loadedmetadata', 'loadstart', 'pause', 'play', 'playing', 'progress', 'ratechange', 'readystatechange', 'seeked', 'seeking', 'stalled', 'suspend', 'timeupdate', 'volumechange', 'waiting']);
// CONCATENATED MODULE: ./packages/@moefe/vue-audio/index.ts










var __decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var __metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};






var ReadyState;

(function (ReadyState) {
  /** 没有关于音频是否就绪的信息 */
  ReadyState[ReadyState["HAVE_NOTHING"] = 0] = "HAVE_NOTHING";
  /** 关于音频就绪的元数据 */

  ReadyState[ReadyState["HAVE_METADATA"] = 1] = "HAVE_METADATA";
  /** 关于当前播放位置的数据是可用的，但没有足够的数据来播放下一帧/毫秒 */

  ReadyState[ReadyState["HAVE_CURRENT_DATA"] = 2] = "HAVE_CURRENT_DATA";
  /** 当前及至少下一帧的数据是可用的 */

  ReadyState[ReadyState["HAVE_FUTURE_DATA"] = 3] = "HAVE_FUTURE_DATA";
  /** 可用数据足以开始播放 */

  ReadyState[ReadyState["HAVE_ENOUGH_DATA"] = 4] = "HAVE_ENOUGH_DATA";
})(ReadyState || (ReadyState = {}));

var vue_audio_VueAudio =
/*#__PURE__*/
function (_Vue) {
  _inherits(VueAudio, _Vue);

  function VueAudio() {
    var _this;

    _classCallCheck(this, VueAudio);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(VueAudio).call(this));
    _this.audio = new Audio();
    _this.audioTracks = _this.audio.audioTracks;
    _this.autoplay = _this.audio.autoplay;
    _this.buffered = _this.audio.buffered;
    _this.controls = _this.audio.controls;
    _this.crossOrigin = _this.audio.crossOrigin;
    _this.currentSrc = _this.audio.currentSrc;
    _this.currentTime = _this.audio.currentTime;
    _this.defaultMuted = _this.audio.defaultMuted;
    _this.defaultPlaybackRate = _this.audio.defaultPlaybackRate;
    _this.duration = _this.audio.duration;
    _this.ended = _this.audio.ended;
    _this.error = _this.audio.error;
    _this.loop = _this.audio.loop;
    _this.mediaKeys = _this.audio.mediaKeys;
    _this.muted = _this.audio.muted;
    _this.networkState = _this.audio.networkState;
    _this.paused = _this.audio.paused;
    _this.playbackRate = _this.audio.playbackRate;
    _this.played = _this.audio.played;
    _this.preload = _this.audio.preload;
    _this.readyState = _this.audio.readyState;
    _this.seekable = _this.audio.seekable;
    _this.seeking = _this.audio.seeking;
    _this.src = _this.audio.src;
    _this.textTracks = _this.audio.textTracks;
    _this.volume = _this.audio.volume;
    events.forEach(function (event) {
      _this.audio.addEventListener(event, function (e) {
        _this.sync();
      });
    });
    return _this;
  }

  _createClass(VueAudio, [{
    key: "sync",
    value: function sync() {
      var _this2 = this;

      Object.keys(this.$data).forEach(function (key) {
        if (key === 'audio') return;
        _this2[key] = _this2.audio[key];
      });
    }
  }, {
    key: "loaded",
    value: function loaded() {
      var _this3 = this;

      return eventLoop(function () {
        return _this3.readyState >= ReadyState.HAVE_FUTURE_DATA;
      }, 0);
    }
  }, {
    key: "srcLoaded",
    value: function srcLoaded() {
      var _this4 = this;

      return eventLoop(function () {
        return _this4.src;
      }, 0);
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return VueAudio;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

vue_audio_VueAudio = __decorate([vue_class_component_common_default.a, __metadata("design:paramtypes", [])], vue_audio_VueAudio);
/* harmony default export */ var vue_audio = (vue_audio_VueAudio);
// CONCATENATED MODULE: ./packages/@moefe/vue-store/index.ts







var vue_store_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};




var vue_store_VueStore =
/*#__PURE__*/
function (_Vue) {
  _inherits(VueStore, _Vue);

  function VueStore() {
    var _this;

    _classCallCheck(this, VueStore);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(VueStore).apply(this, arguments));
    _this.key = 'aplayer-setting';
    _this.store = _this.get(_this.key);
    return _this;
  } // eslint-disable-next-line class-methods-use-this


  _createClass(VueStore, [{
    key: "get",
    value: function get(key) {
      return JSON.parse(localStorage.getItem(key) || '[]');
    }
  }, {
    key: "set",
    value: function set(val) {
      this.store = val;
      localStorage.setItem(this.key, JSON.stringify(val));
    }
  }, {
    key: "render",
    value: function render() {
      return null;
    }
  }]);

  return VueStore;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

vue_store_VueStore = vue_store_decorate([vue_class_component_common_default.a], vue_store_VueStore);
/* harmony default export */ var vue_store = (vue_store_VueStore);
// CONCATENATED MODULE: ./utils/mixin.ts







var mixin_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};




var mixin_Mixin =
/*#__PURE__*/
function (_Vue) {
  _inherits(Mixin, _Vue);

  function Mixin() {
    _classCallCheck(this, Mixin);

    return _possibleConstructorReturn(this, _getPrototypeOf(Mixin).apply(this, arguments));
  }

  _createClass(Mixin, [{
    key: "isMobile",
    get: function get() {
      var ua = this.$ssrContext ? this.$ssrContext.userAgent : window.navigator.userAgent;
      return /mobile/i.test(ua);
    }
  }]);

  return Mixin;
}(external_commonjs_vue_commonjs2_vue_root_Vue_default.a);

mixin_Mixin = mixin_decorate([vue_class_component_common_default.a], mixin_Mixin);
/* harmony default export */ var mixin = (mixin_Mixin);
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/Cover.tsx







var Cover_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var Cover_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var Cover_a;





var Cover_Cover =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(Cover, _Vue$Component);

  function Cover() {
    _classCallCheck(this, Cover);

    return _possibleConstructorReturn(this, _getPrototypeOf(Cover).apply(this, arguments));
  }

  _createClass(Cover, [{
    key: "handleClick",
    value: function handleClick(e) {
      this.$emit('click', e);
    }
  }, {
    key: "render",
    value: function render() {
      var h = arguments[0];
      return h("div", {
        "class": "aplayer-pic",
        style: this.style,
        on: {
          "click": this.handleClick
        }
      }, [this.$slots.default]);
    }
  }, {
    key: "style",
    get: function get() {
      var _this$aplayer = this.aplayer,
          options = _this$aplayer.options,
          currentTheme = _this$aplayer.currentTheme,
          currentMusic = _this$aplayer.currentMusic;
      var cover = currentMusic.cover || options.defaultCover;
      return {
        backgroundImage: cover && "url(\"".concat(cover, "\")"),
        backgroundColor: currentTheme
      };
    }
  }]);

  return Cover;
}(lib["Component"]);

Cover_decorate([Inject(), Cover_metadata("design:type", Object)], Cover_Cover.prototype, "aplayer", void 0);

Cover_Cover = Cover_decorate([vue_class_component_common_default.a], Cover_Cover);
/* harmony default export */ var components_Cover = (Cover_Cover);
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/Icon.tsx







var Icon_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var Icon_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};




var icon = function icon(type) {
  return __webpack_require__("52f0")("./".concat(type, ".svg")).default;
}; // eslint-disable-line

var Icon_Icon =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(Icon, _Vue$Component);

  function Icon() {
    _classCallCheck(this, Icon);

    return _possibleConstructorReturn(this, _getPrototypeOf(Icon).apply(this, arguments));
  }

  _createClass(Icon, [{
    key: "render",
    value: function render() {
      var h = arguments[0];
      var I = icon(this.type);
      return h(I);
    }
  }]);

  return Icon;
}(lib["Component"]);

Icon_decorate([Prop({
  type: String,
  required: true
}), Icon_metadata("design:type", String)], Icon_Icon.prototype, "type", void 0);

Icon_Icon = Icon_decorate([vue_class_component_common_default.a], Icon_Icon);
/* harmony default export */ var components_Icon = (Icon_Icon);
// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.array.sort.js
var es6_array_sort = __webpack_require__("55dd");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.regexp.split.js
var es6_regexp_split = __webpack_require__("28a5");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.regexp.replace.js
var es6_regexp_replace = __webpack_require__("a481");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.regexp.match.js
var es6_regexp_match = __webpack_require__("4917");

// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/utils/index.ts




/* eslint-disable no-param-reassign */
function shuffle(arr) {
  for (var i = arr.length - 1; i >= 0; i--) {
    var randomIndex = Math.floor(Math.random() * (i + 1));
    var itemAtIndex = arr[randomIndex];
    arr[randomIndex] = arr[i];
    arr[i] = itemAtIndex;
  }

  return arr;
}
var utils_HttpRequest =
/*#__PURE__*/
function () {
  function HttpRequest() {
    _classCallCheck(this, HttpRequest);

    this.xhr = new XMLHttpRequest();
  }

  _createClass(HttpRequest, [{
    key: "download",
    value: function download(url) {
      var _this = this;

      var responseType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      return new Promise(function (resolve, reject) {
        _this.xhr.open('get', url);

        _this.xhr.responseType = responseType;

        _this.xhr.onload = function () {
          var status = _this.xhr.status;

          if (status >= 200 && status < 300 || status === 304) {
            resolve(_this.xhr.response);
          }
        };

        _this.xhr.onabort = reject;
        _this.xhr.onerror = reject;
        _this.xhr.ontimeout = reject;

        _this.xhr.send();
      });
    }
  }]);

  return HttpRequest;
}();
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/Lyric.tsx
















var Lyric_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var Lyric_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var Lyric_a;







var Lyric_Lyric =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(Lyric, _Vue$Component);

  function Lyric() {
    var _this;

    _classCallCheck(this, Lyric);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Lyric).apply(this, arguments));
    _this.lrc = '';
    _this.xhr = new utils_HttpRequest();
    _this.isLoading = false;
    return _this;
  }

  _createClass(Lyric, [{
    key: "getLyricFromCurrentMusic",
    value: function getLyricFromCurrentMusic() {
      var _this2 = this;

      return new Promise(function (resolve, reject) {
        var _this2$aplayer = _this2.aplayer,
            lrcType = _this2$aplayer.lrcType,
            currentMusic = _this2$aplayer.currentMusic;

        switch (lrcType) {
          case 0:
            resolve('');
            break;

          case 1:
            resolve(currentMusic.lrc);
            break;

          case 3:
            resolve(currentMusic.lrc ? _this2.xhr.download(currentMusic.lrc) : '');
            break;

          default:
            reject(new Error("Illegal lrcType: ".concat(lrcType)));
            break;
        }
      });
    }
  }, {
    key: "parseLRC",
    value: function parseLRC(lrc) {
      var reg = /\[(\d+):(\d+)[.|:](\d+)\](.+)/;
      var regTime = /\[(\d+):(\d+)[.|:](\d+)\]/g;
      var regCompatible = /\[(\d+):(\d+)]()(.+)/;
      var regTimeCompatible = /\[(\d+):(\d+)]/g;
      var regOffset = /\[offset:\s*(-?\d+)\]/;
      var offsetMatch = this.lrc.match(regOffset);
      var offset = offsetMatch ? Number(offsetMatch[1]) : 0;
      var parsed = [];

      var matchAll = function matchAll(line) {
        var match = line.match(reg) || line.match(regCompatible);
        if (!match || match.length !== 5) return;
        var minutes = Number(match[1]) || 0;
        var seconds = Number(match[2]) || 0;
        var milliseconds = Number(match[3]) || 0;
        var time = minutes * 60 * 1000 + seconds * 1000 + milliseconds + offset; // eslint-disable-line no-mixed-operators

        var text = match[4].replace(regTime, '').replace(regTimeCompatible, ''); // 优化：不要显示空行

        if (!text) return;
        parsed.push({
          time: time,
          text: text
        });
        matchAll(match[4]); // 递归匹配多个时间标签
      };

      lrc.replace(/\\n/g, '\n').split('\n').forEach(function (line) {
        return matchAll(line);
      });

      if (parsed.length > 0) {
        parsed.sort(function (a, b) {
          return a.time - b.time;
        });
      }

      return parsed;
    }
  }, {
    key: "handleChange",
    value: function () {
      var _handleChange = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee() {
        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.prev = 0;
                this.isLoading = true;
                this.lrc = '';
                _context.next = 5;
                return this.getLyricFromCurrentMusic();

              case 5:
                this.lrc = _context.sent;

              case 6:
                _context.prev = 6;
                this.isLoading = false;
                return _context.finish(6);

              case 9:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this, [[0,, 6, 9]]);
      }));

      function handleChange() {
        return _handleChange.apply(this, arguments);
      }

      return handleChange;
    }()
  }, {
    key: "render",
    value: function render() {
      var h = arguments[0];
      var visible = this.visible,
          style = this.style,
          parsed = this.parsed,
          current = this.current,
          noLyric = this.noLyric;
      return h("div", {
        "class": classnames_default()({
          'aplayer-lrc': true,
          'aplayer-lrc-hide': !visible
        })
      }, [h("div", {
        "class": "aplayer-lrc-contents",
        style: style
      }, [parsed.length > 0 ? parsed.map(function (item, index) {
        return h("p", {
          key: item.time,
          "class": classnames_default()({
            'aplayer-lrc-current': current.time === item.time
          })
        }, [item.text]);
      }) : h("p", {
        "class": "aplayer-lrc-current"
      }, [noLyric])])]);
    }
  }, {
    key: "noLyric",
    get: function get() {
      /* eslint-disable no-nested-ternary */
      var currentMusic = this.aplayer.currentMusic;
      return !currentMusic.id ? '(ಗ ‸ ಗ ) 未加载音频' : this.isLoading ? '(*ゝω・) 少女祈祷中..' : this.lrc ? '(・∀・*) 抱歉，该歌词格式不支持' : '(,,•́ . •̀,,) 抱歉，当前歌曲暂无歌词';
      /* eslint-enable no-nested-ternary */
    }
  }, {
    key: "parsed",
    get: function get() {
      return this.parseLRC(this.lrc);
    }
  }, {
    key: "current",
    get: function get() {
      var _this$aplayer = this.aplayer,
          media = _this$aplayer.media,
          currentPlayed = _this$aplayer.currentPlayed;
      var match = this.parsed.filter(function (x) {
        return x.time < currentPlayed * media.duration * 1000;
      });
      if (match && match.length > 0) return match[match.length - 1];
      return this.parsed[0];
    }
  }, {
    key: "transitionDuration",
    get: function get() {
      return this.parsed.length > 1 ? 500 : 0;
    }
  }, {
    key: "translateY",
    get: function get() {
      var current = this.current,
          parsed = this.parsed;
      if (parsed.length <= 0) return 0;
      var index = parsed.indexOf(current);
      var isLast = index === parsed.length - 1;
      return (isLast ? (index - 1) * 16 : index * 16) * -1;
    }
  }, {
    key: "style",
    get: function get() {
      return {
        transitionDuration: "".concat(this.transitionDuration, "ms"),
        transform: "translate3d(0, ".concat(this.translateY, "px, 0)")
      };
    }
  }]);

  return Lyric;
}(lib["Component"]);

Lyric_decorate([Prop({
  type: Boolean,
  required: false,
  default: true
}), Lyric_metadata("design:type", Boolean)], Lyric_Lyric.prototype, "visible", void 0);

Lyric_decorate([Inject(), Lyric_metadata("design:type", Object)], Lyric_Lyric.prototype, "aplayer", void 0);

Lyric_decorate([Watch('aplayer.lrcType', {
  immediate: true
}), Watch('aplayer.currentMusic.lrc', {
  immediate: true
}), Lyric_metadata("design:type", Function), Lyric_metadata("design:paramtypes", []), Lyric_metadata("design:returntype", Promise)], Lyric_Lyric.prototype, "handleChange", null);

Lyric_Lyric = Lyric_decorate([vue_class_component_common_default.a], Lyric_Lyric);
/* harmony default export */ var components_Lyric = (Lyric_Lyric);
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/Main.tsx









var Main_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var Main_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var Main_a;






var Main_Main =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(Main, _Vue$Component);

  function Main() {
    _classCallCheck(this, Main);

    return _possibleConstructorReturn(this, _getPrototypeOf(Main).apply(this, arguments));
  }

  _createClass(Main, [{
    key: "render",
    value: function render() {
      var h = arguments[0];
      var music = this.music;
      var fixed = this.aplayer.fixed;
      return h("div", {
        "class": "aplayer-info"
      }, [h("div", {
        "class": "aplayer-music"
      }, [h("span", {
        "class": "aplayer-title"
      }, [music.name]), h("span", {
        "class": "aplayer-author"
      }, [music.artist])]), !fixed ? h(components_Lyric) : null, this.$slots.default]);
    }
  }, {
    key: "music",
    get: function get() {
      var currentMusic = this.aplayer.currentMusic;
      return {
        name: currentMusic.name,
        artist: currentMusic.artist ? " - ".concat(currentMusic.artist) : ''
      };
    }
  }]);

  return Main;
}(lib["Component"]);

Main_decorate([Inject(), Main_metadata("design:type", Object)], Main_Main.prototype, "aplayer", void 0);

Main_Main = Main_decorate([vue_class_component_common_default.a], Main_Main);
/* harmony default export */ var components_Main = (Main_Main);
// EXTERNAL MODULE: ./node_modules/core-js/modules/es6.string.starts-with.js
var es6_string_starts_with = __webpack_require__("f559");

// EXTERNAL MODULE: ./node_modules/core-js/modules/es7.string.pad-start.js
var es7_string_pad_start = __webpack_require__("f576");

// CONCATENATED MODULE: ./packages/@moefe/vue-touch/index.tsx








var vue_touch_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var vue_touch_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};






var vue_touch_Touch =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(Touch, _Vue$Component);

  function Touch() {
    var _this;

    _classCallCheck(this, Touch);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(Touch).apply(this, arguments));
    _this.isDragMove = false;
    return _this;
  }

  _createClass(Touch, [{
    key: "thumbMove",
    value: function thumbMove(e) {
      this.isDragMove = true;
      this.$emit('panMove', e);
    }
  }, {
    key: "thumbUp",
    value: function thumbUp(e) {
      document.removeEventListener(this.dragMove, this.thumbMove);
      document.removeEventListener(this.dragEnd, this.thumbUp);
      this.isDragMove = false;
      this.$emit('panEnd', e);
    }
  }, {
    key: "mounted",
    value: function mounted() {
      var _this2 = this;

      this.$el.addEventListener(this.dragStart, function (e) {
        _this2.$emit('panStart', e);

        document.addEventListener(_this2.dragMove, _this2.thumbMove);
        document.addEventListener(_this2.dragEnd, _this2.thumbUp);
      });
    }
  }, {
    key: "render",
    value: function render() {
      var h = arguments[0];
      return h("div", {
        "class": this.classNames,
        style: {
          touchAction: 'none',
          userSelect: 'none',
          webkitUserDrag: 'none',
          webkitTapHighlightColor: 'rgba(0, 0, 0, 0)'
        }
      }, [this.$slots.default]);
    }
  }, {
    key: "classNames",
    get: function get() {
      var panMoveClass = this.panMoveClass,
          isDragMove = this.isDragMove;
      return _defineProperty({}, panMoveClass, isDragMove);
    }
  }, {
    key: "dragStart",
    get: function get() {
      return this.isMobile ? 'touchstart' : 'mousedown';
    }
  }, {
    key: "dragMove",
    get: function get() {
      return this.isMobile ? 'touchmove' : 'mousemove';
    }
  }, {
    key: "dragEnd",
    get: function get() {
      return this.isMobile ? 'touchend' : 'mouseup';
    }
  }]);

  return Touch;
}(lib["Component"]);

vue_touch_decorate([Prop({
  type: String,
  required: false
}), vue_touch_metadata("design:type", String)], vue_touch_Touch.prototype, "panMoveClass", void 0);

vue_touch_Touch = vue_touch_decorate([vue_class_component_common_default()({
  mixins: [mixin]
})], vue_touch_Touch);
/* harmony default export */ var vue_touch = (vue_touch_Touch);
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/Button.tsx







var Button_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var Button_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};






var Button_Button =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(Button, _Vue$Component);

  function Button() {
    _classCallCheck(this, Button);

    return _possibleConstructorReturn(this, _getPrototypeOf(Button).apply(this, arguments));
  }

  _createClass(Button, [{
    key: "handleClick",
    value: function handleClick() {
      this.$emit('click');
    }
  }, {
    key: "render",
    value: function render() {
      var h = arguments[0];
      return h("button", {
        attrs: {
          type: "button"
        },
        "class": "aplayer-icon aplayer-icon-".concat(this.type),
        on: {
          "click": this.handleClick
        }
      }, [h(components_Icon, {
        attrs: {
          type: this.icon
        }
      })]);
    }
  }]);

  return Button;
}(lib["Component"]);

Button_decorate([Prop({
  type: String,
  required: true
}), Button_metadata("design:type", String)], Button_Button.prototype, "type", void 0);

Button_decorate([Prop({
  type: String,
  required: true
}), Button_metadata("design:type", String)], Button_Button.prototype, "icon", void 0);

Button_Button = Button_decorate([vue_class_component_common_default.a], Button_Button);
/* harmony default export */ var components_Button = (Button_Button);
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/Progress.tsx








var Progress_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var Progress_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var Progress_a;







var Progress_Progress =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(Progress, _Vue$Component);

  function Progress() {
    _classCallCheck(this, Progress);

    return _possibleConstructorReturn(this, _getPrototypeOf(Progress).apply(this, arguments));
  }

  _createClass(Progress, [{
    key: "handleChange",
    value: function handleChange(e) {
      var target = this.$refs.progressBar;
      var targetLeft = target.getBoundingClientRect().left;
      var clientX = !e.type.startsWith('touch') ? e.clientX : e.changedTouches[0].clientX;
      var offsetLeft = clientX - targetLeft;
      var percent = offsetLeft / target.offsetWidth;
      percent = Math.min(percent, 1);
      percent = Math.max(percent, 0);
      this.handleChangeProgress(e, percent);
    }
  }, {
    key: "render",
    value: function render() {
      var h = arguments[0];
      var _this$aplayer = this.aplayer,
          currentTheme = _this$aplayer.currentTheme,
          currentLoaded = _this$aplayer.currentLoaded,
          currentPlayed = _this$aplayer.currentPlayed;
      return h(vue_touch, {
        "class": "aplayer-bar-wrap",
        on: {
          "panMove": this.handleChange,
          "panEnd": this.handleChange
        }
      }, [h("div", {
        ref: "progressBar",
        "class": "aplayer-bar"
      }, [h("div", {
        "class": "aplayer-loaded",
        style: {
          width: "".concat(currentLoaded * 100, "%")
        }
      }), h("div", {
        "class": "aplayer-played",
        style: {
          width: "".concat(currentPlayed * 100, "%"),
          backgroundColor: currentTheme
        }
      }, [h("span", {
        "class": "aplayer-thumb",
        style: {
          backgroundColor: currentTheme
        }
      }, [h("span", {
        "class": "aplayer-loading-icon"
      }, [h(components_Icon, {
        attrs: {
          type: "loading"
        }
      })])])])])]);
    }
  }]);

  return Progress;
}(lib["Component"]);

Progress_decorate([Inject(), Progress_metadata("design:type", Object)], Progress_Progress.prototype, "aplayer", void 0);

Progress_decorate([Inject(), Progress_metadata("design:type", Function)], Progress_Progress.prototype, "handleChangeProgress", void 0);

Progress_Progress = Progress_decorate([vue_class_component_common_default.a], Progress_Progress);
/* harmony default export */ var components_Progress = (Progress_Progress);
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/Controller.tsx










var Controller_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var Controller_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var Controller_a;









var Controller_Controller =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(Controller, _Vue$Component);

  function Controller() {
    _classCallCheck(this, Controller);

    return _possibleConstructorReturn(this, _getPrototypeOf(Controller).apply(this, arguments));
  }

  _createClass(Controller, [{
    key: "timeSecondsFormat",
    // eslint-disable-next-line class-methods-use-this
    value: function timeSecondsFormat() {
      var time = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
      var minutes = Math.floor(time / 60) || 0;
      var seconds = Math.floor(time % 60) || 0;
      return "".concat(minutes.toString().padStart(2, '0'), ":").concat(seconds.toString().padStart(2, '0')); // prettier-ignore
    }
  }, {
    key: "handleToggleVolume",
    value: function handleToggleVolume() {
      var _this$aplayer = this.aplayer,
          currentVolume = _this$aplayer.currentVolume,
          currentSettings = _this$aplayer.currentSettings;
      this.handleChangeVolume(currentVolume > 0 ? 0 : currentSettings.volume);
    }
  }, {
    key: "handleClickVolumeBar",
    value: function handleClickVolumeBar(e) {
      this.handlePanMove(e);
    }
  }, {
    key: "handlePanMove",
    value: function handlePanMove(e) {
      var target = this.$refs.volumeBar;
      var targetTop = target.getBoundingClientRect().bottom;
      if (targetTop <= 0) return; // 音量控制面板已隐藏

      var clientY = !e.type.startsWith('touch') ? e.clientY : e.changedTouches[0].clientY;
      var offsetTop = Math.round(targetTop - clientY);
      var volume = offsetTop / target.offsetHeight;
      volume = Math.min(volume, 1);
      volume = Math.max(volume, 0);
      this.handleChangeVolume(volume);
    }
  }, {
    key: "render",
    value: function render() {
      var h = arguments[0];
      var ptime = this.ptime,
          dtime = this.dtime,
          volumeIcon = this.volumeIcon;
      var _this$aplayer2 = this.aplayer,
          lrcType = _this$aplayer2.lrcType,
          currentTheme = _this$aplayer2.currentTheme,
          currentVolume = _this$aplayer2.currentVolume,
          currentOrder = _this$aplayer2.currentOrder,
          currentLoop = _this$aplayer2.currentLoop;
      return h("div", {
        "class": "aplayer-controller"
      }, [h(components_Progress), h("div", {
        "class": "aplayer-time"
      }, [h("span", {
        "class": "aplayer-time-inner"
      }, [h("span", {
        "class": "aplayer-ptime"
      }, [ptime]), " /", ' ', h("span", {
        "class": "aplayer-dtime"
      }, [dtime]), ' ']), h("span", {
        "class": "aplayer-icon aplayer-icon-back",
        on: {
          "click": this.handleSkipBack
        }
      }, [h(components_Icon, {
        attrs: {
          type: "skip"
        }
      })]), h("span", {
        "class": "aplayer-icon aplayer-icon-play",
        on: {
          "click": this.handleTogglePlay
        }
      }, [h(components_Icon, {
        attrs: {
          type: this.playIcon
        }
      })]), h("span", {
        "class": "aplayer-icon aplayer-icon-forward",
        on: {
          "click": this.handleSkipForward
        }
      }, [h(components_Icon, {
        attrs: {
          type: "skip"
        }
      })]), h("div", {
        "class": "aplayer-volume-wrap"
      }, [h(components_Button, {
        attrs: {
          type: "volume-".concat(volumeIcon),
          icon: "volume-".concat(volumeIcon)
        },
        on: {
          "click": this.handleToggleVolume
        }
      }), h(vue_touch, {
        "class": "aplayer-volume-bar-wrap",
        attrs: {
          panMoveClass: "aplayer-volume-bar-wrap-active"
        },
        on: {
          "panMove": this.handlePanMove
        }
      }, [h("div", {
        ref: "volumeBar",
        "class": "aplayer-volume-bar",
        on: {
          "click": this.handleClickVolumeBar
        }
      }, [h("div", {
        "class": "aplayer-volume",
        style: {
          height: "".concat(currentVolume * 100, "%"),
          backgroundColor: currentTheme
        }
      })])])]), ' ', h(components_Button, {
        attrs: {
          type: "order",
          icon: "order-".concat(currentOrder)
        },
        on: {
          "click": this.handleToggleOrderMode
        }
      }), ' ', h(components_Button, {
        attrs: {
          type: "loop",
          icon: "loop-".concat(currentLoop)
        },
        on: {
          "click": this.handleToggleLoopMode
        }
      }), ' ', h(components_Button, {
        attrs: {
          type: "menu",
          icon: "menu"
        },
        on: {
          "click": this.handleTogglePlaylist
        }
      }), lrcType !== 0 ? h(components_Button, {
        attrs: {
          type: "lrc",
          icon: "lrc"
        },
        on: {
          "click": this.handleToggleLyric
        }
      }) : null])]);
    }
  }, {
    key: "playIcon",
    get: function get() {
      return this.aplayer.media.paused ? 'play' : 'pause';
    }
  }, {
    key: "volumeIcon",
    get: function get() {
      var currentVolume = this.aplayer.currentVolume;
      return currentVolume <= 0 ? 'off' : currentVolume >= 0.95 ? 'up' : 'down'; // eslint-disable-line no-nested-ternary
    }
  }, {
    key: "ptime",
    get: function get() {
      var _this$aplayer3 = this.aplayer,
          media = _this$aplayer3.media,
          currentPlayed = _this$aplayer3.currentPlayed;
      return this.timeSecondsFormat(currentPlayed * media.duration);
    }
  }, {
    key: "dtime",
    get: function get() {
      return this.timeSecondsFormat(this.aplayer.media.duration);
    }
  }]);

  return Controller;
}(lib["Component"]);

Controller_decorate([Inject(), Controller_metadata("design:type", Object)], Controller_Controller.prototype, "aplayer", void 0);

Controller_decorate([Inject(), Controller_metadata("design:type", Function)], Controller_Controller.prototype, "handleSkipBack", void 0);

Controller_decorate([Inject(), Controller_metadata("design:type", Function)], Controller_Controller.prototype, "handleSkipForward", void 0);

Controller_decorate([Inject(), Controller_metadata("design:type", Function)], Controller_Controller.prototype, "handleTogglePlay", void 0);

Controller_decorate([Inject(), Controller_metadata("design:type", Function)], Controller_Controller.prototype, "handleToggleOrderMode", void 0);

Controller_decorate([Inject(), Controller_metadata("design:type", Function)], Controller_Controller.prototype, "handleToggleLoopMode", void 0);

Controller_decorate([Inject(), Controller_metadata("design:type", Function)], Controller_Controller.prototype, "handleTogglePlaylist", void 0);

Controller_decorate([Inject(), Controller_metadata("design:type", Function)], Controller_Controller.prototype, "handleToggleLyric", void 0);

Controller_decorate([Inject(), Controller_metadata("design:type", Function)], Controller_Controller.prototype, "handleChangeVolume", void 0);

Controller_Controller = Controller_decorate([vue_class_component_common_default.a], Controller_Controller);
/* harmony default export */ var components_Controller = (Controller_Controller);
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/Player.tsx








var Player_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var Player_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var Player_a, Player_b;










var Player_Player =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(Player, _Vue$Component);

  function Player() {
    _classCallCheck(this, Player);

    return _possibleConstructorReturn(this, _getPrototypeOf(Player).apply(this, arguments));
  }

  _createClass(Player, [{
    key: "handleTogglePlay",
    value: function handleTogglePlay() {
      this.$emit('togglePlay');
    }
  }, {
    key: "handleSkipBack",
    value: function handleSkipBack() {
      this.$emit('skipBack');
    }
  }, {
    key: "handleSkipForward",
    value: function handleSkipForward() {
      this.$emit('skipForward');
    }
  }, {
    key: "handleToggleOrderMode",
    value: function handleToggleOrderMode() {
      this.$emit('toggleOrderMode');
    }
  }, {
    key: "handleToggleLoopMode",
    value: function handleToggleLoopMode() {
      this.$emit('toggleLoopMode');
    }
  }, {
    key: "handleTogglePlaylist",
    value: function handleTogglePlaylist() {
      this.$emit('togglePlaylist');
    }
  }, {
    key: "handleToggleLyric",
    value: function handleToggleLyric() {
      this.$emit('toggleLyric');
    }
  }, {
    key: "handleChangeVolume",
    value: function handleChangeVolume(volume) {
      this.$emit('changeVolume', volume);
    }
  }, {
    key: "handleChangeProgress",
    value: function handleChangeProgress(e, percent) {
      this.$emit('changeProgress', e, percent);
    }
  }, {
    key: "handleMiniSwitcher",
    value: function handleMiniSwitcher() {
      this.$emit('miniSwitcher');
    }
  }, {
    key: "render",
    value: function render() {
      var h = arguments[0];
      var playIcon = this.playIcon,
          notice = this.notice;
      return h("div", {
        "class": "aplayer-body"
      }, [h(components_Cover, {
        on: {
          "click": this.handleTogglePlay
        }
      }, [h("div", {
        "class": "aplayer-button aplayer-".concat(playIcon)
      }, [h(components_Icon, {
        attrs: {
          type: playIcon
        }
      })])]), h(components_Main, [h(components_Controller, {
        on: {
          "skipBack": this.handleSkipBack,
          "skipForward": this.handleSkipForward,
          "togglePlay": this.handleTogglePlay,
          "toggleOrderMode": this.handleToggleOrderMode,
          "toggleLoopMode": this.handleToggleLoopMode,
          "togglePlaylist": this.handleTogglePlaylist,
          "toggleLyric": this.handleToggleLyric,
          "changeVolume": this.handleChangeVolume,
          "changeProgress": this.handleChangeProgress
        }
      })]), h("div", {
        "class": "aplayer-notice",
        style: {
          opacity: notice.opacity
        }
      }, [notice.text]), h("div", {
        "class": "aplayer-miniswitcher",
        on: {
          "click": this.handleMiniSwitcher
        }
      }, [h(components_Button, {
        attrs: {
          type: "miniswitcher",
          icon: "right"
        }
      })])]);
    }
  }, {
    key: "playIcon",
    get: function get() {
      return this.aplayer.media.paused ? 'play' : 'pause';
    }
  }]);

  return Player;
}(lib["Component"]);

Player_decorate([Prop({
  type: Object,
  required: true
}), Player_metadata("design:type", Object)], Player_Player.prototype, "notice", void 0);

Player_decorate([Inject(), Player_metadata("design:type", Object)], Player_Player.prototype, "aplayer", void 0);

Player_decorate([Provide(), Player_metadata("design:type", Function), Player_metadata("design:paramtypes", []), Player_metadata("design:returntype", void 0)], Player_Player.prototype, "handleTogglePlay", null);

Player_decorate([Provide(), Player_metadata("design:type", Function), Player_metadata("design:paramtypes", []), Player_metadata("design:returntype", void 0)], Player_Player.prototype, "handleSkipBack", null);

Player_decorate([Provide(), Player_metadata("design:type", Function), Player_metadata("design:paramtypes", []), Player_metadata("design:returntype", void 0)], Player_Player.prototype, "handleSkipForward", null);

Player_decorate([Provide(), Player_metadata("design:type", Function), Player_metadata("design:paramtypes", []), Player_metadata("design:returntype", void 0)], Player_Player.prototype, "handleToggleOrderMode", null);

Player_decorate([Provide(), Player_metadata("design:type", Function), Player_metadata("design:paramtypes", []), Player_metadata("design:returntype", void 0)], Player_Player.prototype, "handleToggleLoopMode", null);

Player_decorate([Provide(), Player_metadata("design:type", Function), Player_metadata("design:paramtypes", []), Player_metadata("design:returntype", void 0)], Player_Player.prototype, "handleTogglePlaylist", null);

Player_decorate([Provide(), Player_metadata("design:type", Function), Player_metadata("design:paramtypes", []), Player_metadata("design:returntype", void 0)], Player_Player.prototype, "handleToggleLyric", null);

Player_decorate([Provide(), Player_metadata("design:type", Function), Player_metadata("design:paramtypes", [Number]), Player_metadata("design:returntype", void 0)], Player_Player.prototype, "handleChangeVolume", null);

Player_decorate([Provide(), Player_metadata("design:type", Function), Player_metadata("design:paramtypes", [Object, Number]), Player_metadata("design:returntype", void 0)], Player_Player.prototype, "handleChangeProgress", null);

Player_Player = Player_decorate([vue_class_component_common_default.a], Player_Player);
/* harmony default export */ var components_Player = (Player_Player);
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/PlayList.tsx












var PlayList_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var PlayList_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var PlayList_a, PlayList_b, _c;






var PlayList_PlayList =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(PlayList, _Vue$Component);

  function PlayList() {
    _classCallCheck(this, PlayList);

    return _possibleConstructorReturn(this, _getPrototypeOf(PlayList).apply(this, arguments));
  }

  _createClass(PlayList, [{
    key: "handleChangeScrollTop",
    value: function () {
      var _handleChangeScrollTop = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee() {
        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.next = 2;
                return this.$nextTick();

              case 2:
                if (this.visible) {
                  this.$refs.list.scrollTop = this.scrollTop;
                }

              case 3:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function handleChangeScrollTop() {
        return _handleChangeScrollTop.apply(this, arguments);
      }

      return handleChangeScrollTop;
    }()
  }, {
    key: "render",
    value: function render() {
      var _this = this;

      var h = arguments[0];
      var listHeight = this.listHeight,
          dataSource = this.dataSource,
          currentMusic = this.currentMusic;
      var currentTheme = this.aplayer.currentTheme;
      return h("ol", {
        ref: "list",
        "class": "aplayer-list",
        style: {
          height: "".concat(listHeight, "px")
        }
      }, [dataSource.map(function (item, index) {
        return h("li", {
          key: item.id,
          "class": classnames_default()({
            'aplayer-list-light': item.id === currentMusic.id
          }),
          on: {
            "click": function click() {
              return _this.$emit('change', item, index);
            }
          }
        }, [h("span", {
          "class": "aplayer-list-cur",
          style: {
            backgroundColor: currentTheme
          }
        }), h("span", {
          "class": "aplayer-list-index"
        }, [index + 1]), ' ', h("span", {
          "class": "aplayer-list-title"
        }, [item.name]), h("span", {
          "class": "aplayer-list-author"
        }, [item.artist])]);
      })]);
    }
  }, {
    key: "listHeight",
    get: function get() {
      var visible = this.visible,
          dataSource = this.dataSource;
      return visible ? Math.min(dataSource.length * 33, Number(this.aplayer.listMaxHeight)) : 0;
    }
  }]);

  return PlayList;
}(lib["Component"]);

PlayList_decorate([Prop({
  type: Boolean,
  required: false,
  default: true
}), PlayList_metadata("design:type", Boolean)], PlayList_PlayList.prototype, "visible", void 0);

PlayList_decorate([Prop({
  type: Object,
  required: true
}), PlayList_metadata("design:type", typeof (PlayList_a = typeof APlayer !== "undefined" && APlayer.Audio) === "function" ? PlayList_a : Object)], PlayList_PlayList.prototype, "currentMusic", void 0);

PlayList_decorate([Prop({
  type: Array,
  required: true
}), PlayList_metadata("design:type", typeof (PlayList_b = typeof Array !== "undefined" && Array) === "function" ? PlayList_b : Object)], PlayList_PlayList.prototype, "dataSource", void 0);

PlayList_decorate([Prop({
  type: Number,
  required: true
}), PlayList_metadata("design:type", Number)], PlayList_PlayList.prototype, "scrollTop", void 0);

PlayList_decorate([Inject(), PlayList_metadata("design:type", Object)], PlayList_PlayList.prototype, "aplayer", void 0);

PlayList_decorate([Watch('scrollTop', {
  immediate: true
}), Watch('dataSource', {
  immediate: true,
  deep: true
}), Watch('visible'), PlayList_metadata("design:type", Function), PlayList_metadata("design:paramtypes", []), PlayList_metadata("design:returntype", Promise)], PlayList_PlayList.prototype, "handleChangeScrollTop", null);

PlayList_PlayList = PlayList_decorate([vue_class_component_common_default.a], PlayList_PlayList);
/* harmony default export */ var components_PlayList = (PlayList_PlayList);
// EXTERNAL MODULE: ./packages/@moefe/vue-aplayer/assets/style/aplayer.scss
var aplayer = __webpack_require__("610a");

// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/components/APlayer.tsx
























var APlayer_decorate = undefined && undefined.__decorate || function (decorators, target, key, desc) {
  var c = arguments.length,
      r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc,
      d;
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);else for (var i = decorators.length - 1; i >= 0; i--) {
    if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  }
  return c > 3 && r && Object.defineProperty(target, key, r), r;
};

var APlayer_metadata = undefined && undefined.__metadata || function (k, v) {
  if ((typeof Reflect === "undefined" ? "undefined" : _typeof(Reflect)) === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var APlayer_a, APlayer_b, APlayer_c, _d, _e, _f, _g, _h;













var instances = [];
var store = new vue_store();
var channel = null;

if (typeof BroadcastChannel !== 'undefined') {
  channel = new BroadcastChannel('aplayer');
}

var APlayer_APlayer =
/*#__PURE__*/
function (_Vue$Component) {
  _inherits(APlayer, _Vue$Component);

  function APlayer() {
    var _this;

    _classCallCheck(this, APlayer);

    _this = _possibleConstructorReturn(this, _getPrototypeOf(APlayer).apply(this, arguments)); // 是否正在拖动进度条（防止抖动）

    _this.isDraggingProgressBar = false; // 是否正在等待进度条更新（防止抖动）

    _this.isAwaitChangeProgressBar = false; // 是否是迷你模式

    _this.isMini = _this.mini !== null ? _this.mini : _this.fixed; // 是否是 arrow 模式

    _this.isArrow = false; // 当 currentMusic 改变时是否允许播放

    _this.canPlay = !_this.isMobile && _this.autoplay; // 播放列表是否可见

    _this.listVisible = !_this.listFolded; // 控制迷你模式下的歌词是否可见

    _this.lyricVisible = true; // 封面图片对象

    _this.img = new Image(); // 封面下载对象

    _this.xhr = new utils_HttpRequest(); // 响应式媒体对象

    _this.media = new vue_audio(); // 核心音频对象

    _this.player = _this.media.audio; // 播放器设置存储对象

    _this.store = store; // 当前播放的音乐

    _this.currentMusic = {
      id: NaN,
      name: '未加载音频',
      artist: '(ಗ ‸ ಗ )',
      url: ''
    }; // 当前已播放比例

    _this.currentPlayed = 0; // 当前音量

    _this.currentVolume = _this.volume; // 当前循环模式

    _this.currentLoop = _this.loop; // 当前顺序模式

    _this.currentOrder = _this.order; // 当前主题，通过封面自适应主题 > 当前播放的音乐指定的主题 > 主题选项

    _this.currentTheme = _this.currentMusic.theme || _this.theme; // 通知对象

    _this.notice = {
      text: '',
      time: 2000,
      opacity: 0
    };
    return _this;
  } // #endregion
  // 提供当前实例的引用，让子组件获取该实例的可响应数据


  _createClass(APlayer, [{
    key: "handleChangePlayList",
    // #region 监听属性
    value: function () {
      var _handleChangePlayList = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee(newList, oldList) {
        var newLength, oldLength, _this$currentMusic, id, url, oldIndex, _this$currentList, music;

        return regeneratorRuntime.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (oldList) {
                  newLength = newList.length;
                  oldLength = oldList.length;

                  if (newLength !== oldLength) {
                    if (newLength <= 0) this.$emit('listClear');else if (newLength > oldLength) this.$emit('listAdd');else {
                      if (this.currentOrderIndex < 0) {
                        _this$currentMusic = this.currentMusic, id = _this$currentMusic.id, url = _this$currentMusic.url;
                        oldIndex = oldList.findIndex(function (item) {
                          return item.id === id || item.url === url;
                        });
                        Object.assign(this.currentMusic, oldList[oldIndex - 1]);
                      }

                      this.canPlay = !this.player.paused;
                      this.$emit('listRemove');
                    }
                  }
                } // 播放列表初始化


                if (!(this.orderList.length > 0)) {
                  _context.next = 6;
                  break;
                }

                if (!this.currentMusic.id) {
                  _this$currentList = _slicedToArray(this.currentList, 1);
                  this.currentMusic = _this$currentList[0];
                } else {
                  this.canPlay = !this.player.paused;
                  music = this.orderList[this.currentOrderIndex] || this.orderList[0]; // eslint-disable-line max-len

                  Object.assign(this.currentMusic, music);
                }

                _context.next = 5;
                return this.$nextTick();

              case 5:
                this.canPlay = true;

              case 6:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function handleChangePlayList(_x, _x2) {
        return _handleChangePlayList.apply(this, arguments);
      }

      return handleChangePlayList;
    }()
  }, {
    key: "handleChangeCurrentMusic",
    value: function () {
      var _handleChangeCurrentMusic = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee3(newMusic, oldMusic) {
        var _this2 = this;

        var cover, src;
        return regeneratorRuntime.wrap(function _callee3$(_context3) {
          while (1) {
            switch (_context3.prev = _context3.next) {
              case 0:
                if (newMusic.theme) {
                  this.currentTheme = newMusic.theme;
                } else {
                  cover = newMusic.cover || this.options.defaultCover;

                  if (cover) {
                    setTimeout(
                    /*#__PURE__*/
                    _asyncToGenerator(
                    /*#__PURE__*/
                    regeneratorRuntime.mark(function _callee2() {
                      return regeneratorRuntime.wrap(function _callee2$(_context2) {
                        while (1) {
                          switch (_context2.prev = _context2.next) {
                            case 0:
                              _context2.prev = 0;
                              _context2.next = 3;
                              return _this2.getThemeColorFromCover(cover);

                            case 3:
                              _this2.currentTheme = _context2.sent;
                              _context2.next = 9;
                              break;

                            case 6:
                              _context2.prev = 6;
                              _context2.t0 = _context2["catch"](0);
                              _this2.currentTheme = newMusic.theme || _this2.theme;

                            case 9:
                            case "end":
                              return _context2.stop();
                          }
                        }
                      }, _callee2, this, [[0, 6]]);
                    })));
                  }
                }

                if (!newMusic.url) {
                  _context3.next = 15;
                  break;
                }

                if (!((oldMusic !== undefined && oldMusic.url) !== newMusic.url || this.player.src !== newMusic.url)) {
                  _context3.next = 14;
                  break;
                }

                this.currentPlayed = 0;

                if (oldMusic && oldMusic.id) {
                  // 首次初始化时不要触发事件
                  this.handleChangeSettings();
                  this.$emit('listSwitch', newMusic);
                }

                _context3.next = 7;
                return this.getAudioUrl(newMusic);

              case 7:
                src = _context3.sent;
                if (src) this.player.src = src;
                this.player.playbackRate = newMusic.speed || 1;
                this.player.preload = this.preload;
                this.player.volume = this.currentVolume;
                this.player.currentTime = 0;

                this.player.onerror = function (e) {
                  _this2.showNotice(e.toString());
                };

              case 14:
                // **请勿移动此行**，否则当歌曲结束播放时如果歌单中只有一首歌曲将无法重复播放
                if (this.canPlay) this.play();

              case 15:
              case "end":
                return _context3.stop();
            }
          }
        }, _callee3, this);
      }));

      function handleChangeCurrentMusic(_x3, _x4) {
        return _handleChangeCurrentMusic.apply(this, arguments);
      }

      return handleChangeCurrentMusic;
    }()
  }, {
    key: "handleChangeVolume",
    value: function handleChangeVolume(volume) {
      this.currentVolume = volume;
    }
  }, {
    key: "handleChangeCurrentVolume",
    value: function handleChangeCurrentVolume() {
      this.player.volume = this.currentVolume;
      this.$emit('update:volume', this.currentVolume);
    }
  }, {
    key: "handleChangeCurrentTime",
    value: function handleChangeCurrentTime() {
      if (!this.isDraggingProgressBar && !this.isAwaitChangeProgressBar) {
        this.currentPlayed = this.media.currentTime / this.media.duration || 0;
      }
    }
  }, {
    key: "handleChangeSettings",
    value: function handleChangeSettings() {
      var settings = {
        currentTime: this.media.currentTime,
        duration: this.media.duration,
        paused: this.media.paused,
        mini: this.isMini,
        lrc: this.lyricVisible,
        list: this.listVisible,
        volume: this.currentVolume,
        loop: this.currentLoop,
        order: this.currentOrder,
        music: this.currentMusic
      };

      if (settings.volume <= 0) {
        settings.volume = this.currentSettings.volume;
      }

      this.saveSettings(settings);
    }
  }, {
    key: "handleChangeEnded",
    value: function handleChangeEnded() {
      if (!this.media.ended) return;
      this.currentPlayed = 0;

      switch (this.currentLoop) {
        default:
        case 'all':
          this.handleSkipForward();
          break;

        case 'one':
          this.play();
          break;

        case 'none':
          if (this.currentIndex === this.currentList.length - 1) {
            var _this$currentList2 = _slicedToArray(this.currentList, 1);

            this.currentMusic = _this$currentList2[0];
            this.pause();
            this.canPlay = false;
          } else this.handleSkipForward();

          break;
      }
    }
  }, {
    key: "handleChangeMini",
    value: function handleChangeMini() {
      this.isMini = this.mini;
    }
  }, {
    key: "handleChangeCurrentMini",
    value: function () {
      var _handleChangeCurrentMini = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee4(newVal, oldVal) {
        var container;
        return regeneratorRuntime.wrap(function _callee4$(_context4) {
          while (1) {
            switch (_context4.prev = _context4.next) {
              case 0:
                _context4.next = 2;
                return this.$nextTick();

              case 2:
                container = this.$refs.container;
                this.isArrow = container && container.offsetWidth <= 300;

                if (oldVal !== undefined) {
                  this.$emit('update:mini', this.isMini);
                  this.handleChangeSettings();
                }

              case 5:
              case "end":
                return _context4.stop();
            }
          }
        }, _callee4, this);
      }));

      function handleChangeCurrentMini(_x5, _x6) {
        return _handleChangeCurrentMini.apply(this, arguments);
      }

      return handleChangeCurrentMini;
    }()
  }, {
    key: "handleChangeLoop",
    value: function handleChangeLoop() {
      this.currentLoop = this.loop;
    }
  }, {
    key: "handleChangeCurrentLoop",
    value: function handleChangeCurrentLoop() {
      this.$emit('update:loop', this.currentLoop);
      this.handleChangeSettings();
    }
  }, {
    key: "handleChangeOrder",
    value: function handleChangeOrder() {
      this.currentOrder = this.order;
    }
  }, {
    key: "handleChangeCurrentOrder",
    value: function handleChangeCurrentOrder() {
      this.$emit('update:order', this.currentOrder);
      this.handleChangeSettings();
    }
  }, {
    key: "handleChangeListVisible",
    value: function handleChangeListVisible() {
      this.$emit(this.listVisible ? 'listShow' : 'listHide');
      this.$emit('update:listFolded', this.listVisible);
      this.handleChangeSettings();
    }
  }, {
    key: "handleChangeLyricVisible",
    value: function handleChangeLyricVisible() {
      this.$emit(this.lyricVisible ? 'lrcShow' : 'lrcHide');
      this.handleChangeSettings();
    } // #endregion
    // #region 公开 API

  }, {
    key: "play",
    value: function () {
      var _play = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee5() {
        return regeneratorRuntime.wrap(function _callee5$(_context5) {
          while (1) {
            switch (_context5.prev = _context5.next) {
              case 0:
                _context5.prev = 0;
                if (this.mutex) this.pauseOtherInstances();
                _context5.next = 4;
                return this.player.play();

              case 4:
                _context5.next = 10;
                break;

              case 6:
                _context5.prev = 6;
                _context5.t0 = _context5["catch"](0);
                this.showNotice(_context5.t0.message);
                this.player.pause();

              case 10:
              case "end":
                return _context5.stop();
            }
          }
        }, _callee5, this, [[0, 6]]);
      }));

      function play() {
        return _play.apply(this, arguments);
      }

      return play;
    }()
  }, {
    key: "pause",
    value: function pause() {
      this.player.pause();
    }
  }, {
    key: "toggle",
    value: function toggle() {
      if (this.media.paused) this.play();else this.pause();
    }
  }, {
    key: "seeking",
    value: function () {
      var _seeking = _asyncToGenerator(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee6(percent) {
        var paused,
            oldPaused,
            _args6 = arguments;
        return regeneratorRuntime.wrap(function _callee6$(_context6) {
          while (1) {
            switch (_context6.prev = _context6.next) {
              case 0:
                paused = _args6.length > 1 && _args6[1] !== undefined ? _args6[1] : true;
                _context6.prev = 1;
                this.isAwaitChangeProgressBar = true;

                if (!(this.preload === 'none')) {
                  _context6.next = 11;
                  break;
                }

                if (this.player.src) {
                  _context6.next = 7;
                  break;
                }

                _context6.next = 7;
                return this.media.srcLoaded();

              case 7:
                oldPaused = this.player.paused;
                _context6.next = 10;
                return this.play();

              case 10:
                // preload 为 none 的情况下必须先 play
                if (paused && oldPaused) this.pause();

              case 11:
                if (paused) this.pause();
                _context6.next = 14;
                return this.media.loaded();

              case 14:
                this.player.currentTime = percent * this.media.duration;

                if (!paused) {
                  this.play();

                  if (channel && this.mutex) {
                    channel.postMessage('mutex');
                  }
                }

                _context6.next = 21;
                break;

              case 18:
                _context6.prev = 18;
                _context6.t0 = _context6["catch"](1);
                this.showNotice(_context6.t0.message);

              case 21:
                _context6.prev = 21;
                this.isAwaitChangeProgressBar = false;
                return _context6.finish(21);

              case 24:
              case "end":
                return _context6.stop();
            }
          }
        }, _callee6, this, [[1, 18, 21, 24]]);
      }));

      function seeking(_x7) {
        return _seeking.apply(this, arguments);
      }

      return seeking;
    }()
  }, {
    key: "seek",
    value: function seek(time) {
      this.seeking(time / this.media.duration, this.media.paused);
    }
  }, {
    key: "switch",
    value: function _switch(audio) {
      switch (_typeof(audio)) {
        case 'number':
          this.currentMusic = this.orderList[Math.min(Math.max(0, audio), this.orderList.length - 1)];
          break;
        // eslint-disable-next-line no-case-declarations

        default:
          var music = this.orderList.find(function (item) {
            return typeof item.name === 'string' && item.name.includes(audio);
          });
          if (music) this.currentMusic = music;
          break;
      }
    }
  }, {
    key: "skipBack",
    value: function skipBack() {
      var playIndex = this.getPlayIndexByMode('skipBack');
      this.currentMusic = _objectSpread({}, this.currentList[playIndex]);
    }
  }, {
    key: "skipForward",
    value: function skipForward() {
      var playIndex = this.getPlayIndexByMode('skipForward');
      this.currentMusic = _objectSpread({}, this.currentList[playIndex]);
    }
  }, {
    key: "showLrc",
    value: function showLrc() {
      this.lyricVisible = true;
    }
  }, {
    key: "hideLrc",
    value: function hideLrc() {
      this.lyricVisible = false;
    }
  }, {
    key: "toggleLrc",
    value: function toggleLrc() {
      this.lyricVisible = !this.lyricVisible;
    }
  }, {
    key: "showList",
    value: function showList() {
      this.listVisible = true;
    }
  }, {
    key: "hideList",
    value: function hideList() {
      this.listVisible = false;
    }
  }, {
    key: "toggleList",
    value: function toggleList() {
      this.listVisible = !this.listVisible;
    }
  }, {
    key: "showNotice",
    value: function showNotice(text) {
      var _this3 = this;

      var time = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 2000;
      var opacity = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0.8;
      return new Promise(function (resolve) {
        if (_this3.isMini) {
          // eslint-disable-next-line no-console
          console.warn('aplayer notice:', text);
          resolve();
        } else {
          _this3.notice = {
            text: text,
            time: time,
            opacity: opacity
          };

          _this3.$emit('noticeShow');

          if (time > 0) {
            setTimeout(function () {
              _this3.notice.opacity = 0;

              _this3.$emit('noticeHide');

              resolve();
            }, time);
          }
        }
      });
    } // #endregion
    // #region 私有 API
    // 从封面中获取主题颜色

  }, {
    key: "getThemeColorFromCover",
    value: function getThemeColorFromCover(url) {
      var _this4 = this;

      return new Promise(
      /*#__PURE__*/
      function () {
        var _ref2 = _asyncToGenerator(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee7(resolve, reject) {
          var image, reader;
          return regeneratorRuntime.wrap(function _callee7$(_context7) {
            while (1) {
              switch (_context7.prev = _context7.next) {
                case 0:
                  _context7.prev = 0;

                  if (!(typeof ColorThief !== 'undefined')) {
                    _context7.next = 12;
                    break;
                  }

                  _context7.next = 4;
                  return _this4.xhr.download(url, 'blob');

                case 4:
                  image = _context7.sent;
                  reader = new FileReader();

                  reader.onload = function () {
                    _this4.img.src = reader.result;

                    _this4.img.onload = function () {
                      var _getColor = new ColorThief().getColor(_this4.img),
                          _getColor2 = _slicedToArray(_getColor, 3),
                          r = _getColor2[0],
                          g = _getColor2[1],
                          b = _getColor2[2];

                      var theme = "rgb(".concat(r, ", ").concat(g, ", ").concat(b, ")");
                      resolve(theme || _this4.currentMusic.theme || _this4.theme);
                    };

                    _this4.img.onabort = reject;
                    _this4.img.onerror = reject;
                  };

                  reader.onabort = reject;
                  reader.onerror = reject;
                  reader.readAsDataURL(image);
                  _context7.next = 13;
                  break;

                case 12:
                  resolve(_this4.currentMusic.theme || _this4.theme);

                case 13:
                  _context7.next = 18;
                  break;

                case 15:
                  _context7.prev = 15;
                  _context7.t0 = _context7["catch"](0);
                  resolve(_this4.currentMusic.theme || _this4.theme);

                case 18:
                case "end":
                  return _context7.stop();
              }
            }
          }, _callee7, this, [[0, 15]]);
        }));

        return function (_x8, _x9) {
          return _ref2.apply(this, arguments);
        };
      }());
    }
  }, {
    key: "getAudioUrl",
    value: function getAudioUrl(music) {
      var _this5 = this;

      return new Promise(function (resolve, reject) {
        var type = music.type;

        if (type && _this5.customAudioType && _this5.customAudioType[type]) {
          if (typeof _this5.customAudioType[type] === 'function') {
            _this5.customAudioType[type](_this5.player, music, _this5);
          } else {
            // eslint-disable-next-line no-console
            console.error("Illegal customType: ".concat(type));
          }

          resolve();
        } else {
          if (!type || type === 'auto') {
            type = /m3u8(#|\?|$)/i.test(music.url) ? 'hls' : 'normal';
          }

          if (type === 'hls') {
            try {
              if (Hls.isSupported()) {
                var hls = new Hls();
                hls.loadSource(music.url);
                hls.attachMedia(_this5.player);
                resolve();
              } else if (_this5.player.canPlayType('application/x-mpegURL') || _this5.player.canPlayType('application/vnd.apple.mpegURL')) {
                resolve(music.url);
              } else {
                reject(new Error('HLS is not supported.'));
              }
            } catch (e) {
              reject(new Error('HLS is not supported.'));
            }
          } else {
            resolve(music.url);
          }
        }
      });
    }
  }, {
    key: "getPlayIndexByMode",
    value: function getPlayIndexByMode(type) {
      var length = this.currentList.length;
      var index = this.currentIndex;
      return (type === 'skipBack' ? length + (index - 1) : index + 1) % length;
    }
  }, {
    key: "pauseOtherInstances",
    value: function pauseOtherInstances() {
      var _this6 = this;

      instances.filter(function (inst) {
        return inst !== _this6;
      }).forEach(function (inst) {
        return inst.pause();
      });
    }
  }, {
    key: "saveSettings",
    value: function saveSettings(settings) {
      var instanceIndex = instances.indexOf(this);
      if (settings === null) delete instances[instanceIndex];
      this.store.set(this.settings[instanceIndex] !== undefined ? this.settings.map(function (item, index) {
        return index === instanceIndex ? settings : item;
      }) : _toConsumableArray(this.settings).concat([settings]));
    } // #endregion
    // #region 事件处理
    // 切换上一曲

  }, {
    key: "handleSkipBack",
    value: function handleSkipBack() {
      this.skipBack();
    } // 切换下一曲

  }, {
    key: "handleSkipForward",
    value: function handleSkipForward() {
      this.skipForward();
    } // 切换播放

  }, {
    key: "handleTogglePlay",
    value: function handleTogglePlay() {
      this.toggle();
    } // 处理切换顺序模式

  }, {
    key: "handleToggleOrderMode",
    value: function handleToggleOrderMode() {
      this.currentOrder = this.currentOrder === 'list' ? 'random' : 'list';
    } // 处理切换循环模式

  }, {
    key: "handleToggleLoopMode",
    value: function handleToggleLoopMode() {
      this.currentLoop = this.currentLoop === 'all' ? 'one' : this.currentLoop === 'one' ? 'none' : 'all';
    } // 处理切换播放/暂停事件

  }, {
    key: "handleTogglePlaylist",
    value: function handleTogglePlaylist() {
      this.toggleList();
    } // 处理切换歌词显隐事件

  }, {
    key: "handleToggleLyric",
    value: function handleToggleLyric() {
      this.toggleLrc();
    } // 处理进度条改变事件

  }, {
    key: "handleChangeProgress",
    value: function handleChangeProgress(e, percent) {
      this.currentPlayed = percent;
      this.isDraggingProgressBar = e.type.includes('move');

      if (['touchend', 'mouseup'].includes(e.type)) {
        this.seeking(percent, this.media.paused); // preload 为 none 的情况下无法获取到 duration
      }
    } // 处理切换迷你模式事件

  }, {
    key: "handleMiniSwitcher",
    value: function handleMiniSwitcher() {
      this.isMini = !this.isMini;
    } // 处理播放曲目改变事件

  }, {
    key: "handleChangePlaylist",
    value: function handleChangePlaylist(music, index) {
      if (music.id === this.currentMusic.id) this.handleTogglePlay();else this.currentMusic = this.orderList[index];
    } // #endregion

  }, {
    key: "beforeMount",
    value: function beforeMount() {
      var _this7 = this;

      this.store.key = this.storageName;
      var emptyIndex = instances.findIndex(function (x) {
        return !x;
      });
      if (emptyIndex > -1) instances[emptyIndex] = this;else instances.push(this);

      if (this.currentSettings) {
        var _this$currentSettings = this.currentSettings,
            mini = _this$currentSettings.mini,
            lrc = _this$currentSettings.lrc,
            list = _this$currentSettings.list,
            volume = _this$currentSettings.volume,
            loop = _this$currentSettings.loop,
            order = _this$currentSettings.order,
            music = _this$currentSettings.music,
            currentTime = _this$currentSettings.currentTime,
            duration = _this$currentSettings.duration,
            paused = _this$currentSettings.paused;
        this.isMini = mini;
        this.lyricVisible = lrc;
        this.listVisible = list;
        this.currentVolume = volume;
        this.currentLoop = loop;
        this.currentOrder = order;

        if (music) {
          this.currentMusic = music;

          if (!this.isMobile && duration) {
            this.seeking(currentTime / duration, paused);
          }
        }
      } // 处理多页面互斥


      if (channel) {
        if (this.mutex) {
          channel.addEventListener('message', function (_ref3) {
            var data = _ref3.data;
            if (data === 'mutex') _this7.pause();
          });
        }
      } else {// 不支持 BroadcastChannel，暂不处理
      }

      events.forEach(function (event) {
        _this7.player.addEventListener(event, function (e) {
          return _this7.$emit(event, e);
        });
      });
    }
  }, {
    key: "beforeDestroy",
    value: function beforeDestroy() {
      this.pause();
      this.saveSettings(null);
      this.$emit('destroy');
      this.$el.remove();
    }
  }, {
    key: "render",
    value: function render() {
      var h = arguments[0];
      var dataSource = this.dataSource,
          fixed = this.fixed,
          lrcType = this.lrcType,
          isMini = this.isMini,
          isMobile = this.isMobile,
          isArrow = this.isArrow,
          isLoading = this.isLoading,
          notice = this.notice,
          listVisible = this.listVisible,
          listScrollTop = this.listScrollTop,
          currentMusic = this.currentMusic,
          lyricVisible = this.lyricVisible;
      return h("div", {
        ref: "container",
        "class": classnames_default()({
          aplayer: true,
          'aplayer-withlist': dataSource.length > 1,
          'aplayer-withlrc': !fixed && lrcType !== 0 && lyricVisible,
          'aplayer-narrow': isMini,
          'aplayer-fixed': fixed,
          'aplayer-mobile': isMobile,
          'aplayer-arrow': isArrow,
          'aplayer-loading': isLoading
        })
      }, [h(components_Player, {
        attrs: {
          notice: notice
        },
        on: {
          "skipBack": this.handleSkipBack,
          "skipForward": this.handleSkipForward,
          "togglePlay": this.handleTogglePlay,
          "toggleOrderMode": this.handleToggleOrderMode,
          "toggleLoopMode": this.handleToggleLoopMode,
          "togglePlaylist": this.handleTogglePlaylist,
          "toggleLyric": this.handleToggleLyric,
          "changeVolume": this.handleChangeVolume,
          "changeProgress": this.handleChangeProgress,
          "miniSwitcher": this.handleMiniSwitcher
        }
      }), h(components_PlayList, {
        attrs: {
          visible: listVisible,
          scrollTop: listScrollTop,
          currentMusic: currentMusic,
          dataSource: dataSource
        },
        on: {
          "change": this.handleChangePlaylist
        }
      }), fixed && lrcType !== 0 ? h(components_Lyric, {
        attrs: {
          visible: lyricVisible
        }
      }) : null]);
    }
  }, {
    key: "aplayer",
    get: function get() {
      return this;
    }
  }, {
    key: "settings",
    get: function get() {
      return this.store.store;
    }
  }, {
    key: "currentSettings",
    get: function get() {
      return this.settings[instances.indexOf(this)];
    } // 当前播放模式对应的播放列表

  }, {
    key: "currentList",
    get: function get() {
      return this.currentOrder === 'list' ? this.orderList : this.randomList;
    } // 数据源，自动生成 ID 作为播放列表项的 key

  }, {
    key: "dataSource",
    get: function get() {
      return (Array.isArray(this.audio) ? this.audio : [this.audio]).filter(function (x) {
        return x;
      }).map(function (item, index) {
        return _objectSpread({
          id: index + 1
        }, item);
      });
    } // 根据数据源生成顺序播放列表（处理 VNode）

  }, {
    key: "orderList",
    get: function get() {
      var text = function text(vnode, key) {
        return typeof vnode === 'string' ? vnode : vnode.data && vnode.data.attrs && vnode.data.attrs["data-".concat(key)];
      };

      return this.dataSource.map(function (_ref4) {
        var name = _ref4.name,
            artist = _ref4.artist,
            item = _objectWithoutProperties(_ref4, ["name", "artist"]);

        return _objectSpread({}, item, {
          name: text(name, 'name'),
          artist: text(artist, 'artist')
        });
      });
    } // 根据顺序播放列表生成随机播放列表

  }, {
    key: "randomList",
    get: function get() {
      return shuffle(_toConsumableArray(this.orderList));
    } // 是否正在缓冲

  }, {
    key: "isLoading",
    get: function get() {
      var preload = this.preload,
          currentPlayed = this.currentPlayed,
          currentLoaded = this.currentLoaded;
      var _this$media = this.media,
          src = _this$media.src,
          paused = _this$media.paused,
          duration = _this$media.duration;
      var loading = !!src && (currentPlayed > currentLoaded || !duration);
      return preload === 'none' ? !paused && loading : loading;
    }
  }, {
    key: "listScrollTop",
    get: function get() {
      return this.currentOrderIndex * 33;
    } // 当前播放的音乐索引

  }, {
    key: "currentIndex",
    get: function get() {
      return this.currentOrder === 'list' ? this.currentOrderIndex : this.currentRandomIndex;
    }
  }, {
    key: "currentOrderIndex",
    get: function get() {
      var _this$currentMusic2 = this.currentMusic,
          id = _this$currentMusic2.id,
          url = _this$currentMusic2.url;
      return this.orderList.findIndex(function (item) {
        return item.id === id || item.url === url;
      });
    }
  }, {
    key: "currentRandomIndex",
    get: function get() {
      var _this$currentMusic3 = this.currentMusic,
          id = _this$currentMusic3.id,
          url = _this$currentMusic3.url;
      return this.randomList.findIndex(function (item) {
        return item.id === id || item.url === url;
      });
    } // 当前已缓冲比例

  }, {
    key: "currentLoaded",
    get: function get() {
      if (this.media.readyState < ReadyState.HAVE_FUTURE_DATA) return 0;
      var length = this.media.buffered.length;
      return length > 0 ? this.media.buffered.end(length - 1) / this.media.duration : 1;
    }
  }]);

  return APlayer;
}(lib["Component"]);

APlayer_APlayer.version = "2.0.0-beta.5";

APlayer_decorate([Prop({
  type: Boolean,
  required: false,
  default: false
}), APlayer_metadata("design:type", Boolean)], APlayer_APlayer.prototype, "fixed", void 0);

APlayer_decorate([Prop({
  type: Boolean,
  required: false,
  default: null
}), APlayer_metadata("design:type", Boolean)], APlayer_APlayer.prototype, "mini", void 0);

APlayer_decorate([Prop({
  type: Boolean,
  required: false,
  default: false
}), APlayer_metadata("design:type", Boolean)], APlayer_APlayer.prototype, "autoplay", void 0);

APlayer_decorate([Prop({
  type: String,
  required: false,
  default: '#b7daff'
}), APlayer_metadata("design:type", String)], APlayer_APlayer.prototype, "theme", void 0);

APlayer_decorate([Prop({
  type: String,
  required: false,
  default: 'all'
}), APlayer_metadata("design:type", typeof (APlayer_a = typeof APlayer_APlayer !== "undefined" && APlayer_APlayer.LoopMode) === "function" ? APlayer_a : Object)], APlayer_APlayer.prototype, "loop", void 0);

APlayer_decorate([Prop({
  type: String,
  required: false,
  default: 'list'
}), APlayer_metadata("design:type", typeof (APlayer_b = typeof APlayer_APlayer !== "undefined" && APlayer_APlayer.OrderMode) === "function" ? APlayer_b : Object)], APlayer_APlayer.prototype, "order", void 0);

APlayer_decorate([Prop({
  type: String,
  required: false,
  default: 'auto'
}), APlayer_metadata("design:type", typeof (APlayer_c = typeof APlayer_APlayer !== "undefined" && APlayer_APlayer.Preload) === "function" ? APlayer_c : Object)], APlayer_APlayer.prototype, "preload", void 0);

APlayer_decorate([Prop({
  type: Number,
  required: false,
  default: 0.7
}), APlayer_metadata("design:type", Number)], APlayer_APlayer.prototype, "volume", void 0);

APlayer_decorate([Prop({
  type: [Object, Array],
  required: true
}), APlayer_metadata("design:type", Object)], APlayer_APlayer.prototype, "audio", void 0);

APlayer_decorate([Prop({
  type: Object,
  required: false
}), APlayer_metadata("design:type", Object)], APlayer_APlayer.prototype, "customAudioType", void 0);

APlayer_decorate([Prop({
  type: Boolean,
  required: false,
  default: true
}), APlayer_metadata("design:type", Boolean)], APlayer_APlayer.prototype, "mutex", void 0);

APlayer_decorate([Prop({
  type: Number,
  required: false,
  default: 0
}), APlayer_metadata("design:type", typeof (_f = typeof APlayer_APlayer !== "undefined" && APlayer_APlayer.LrcType) === "function" ? _f : Object)], APlayer_APlayer.prototype, "lrcType", void 0);

APlayer_decorate([Prop({
  type: Boolean,
  required: false,
  default: false
}), APlayer_metadata("design:type", Boolean)], APlayer_APlayer.prototype, "listFolded", void 0);

APlayer_decorate([Prop({
  type: Number,
  required: false,
  default: 250
}), APlayer_metadata("design:type", Number)], APlayer_APlayer.prototype, "listMaxHeight", void 0);

APlayer_decorate([Prop({
  type: String,
  required: false,
  default: 'aplayer-setting'
}), APlayer_metadata("design:type", String)], APlayer_APlayer.prototype, "storageName", void 0);

APlayer_decorate([Provide(), APlayer_metadata("design:type", Object), APlayer_metadata("design:paramtypes", [])], APlayer_APlayer.prototype, "aplayer", null);

APlayer_decorate([Watch('orderList', {
  immediate: true,
  deep: true
}), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", [Array, Array]), APlayer_metadata("design:returntype", Promise)], APlayer_APlayer.prototype, "handleChangePlayList", null);

APlayer_decorate([Watch('currentMusic', {
  immediate: true,
  deep: true
}), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", [typeof (_g = typeof APlayer_APlayer !== "undefined" && APlayer_APlayer.Audio) === "function" ? _g : Object, typeof (_h = typeof APlayer_APlayer !== "undefined" && APlayer_APlayer.Audio) === "function" ? _h : Object]), APlayer_metadata("design:returntype", Promise)], APlayer_APlayer.prototype, "handleChangeCurrentMusic", null);

APlayer_decorate([Watch('volume'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", [Number]), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeVolume", null);

APlayer_decorate([Watch('currentVolume'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeCurrentVolume", null);

APlayer_decorate([Watch('media.currentTime'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeCurrentTime", null);

APlayer_decorate([Watch('media.$data', {
  deep: true
}), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeSettings", null);

APlayer_decorate([Watch('media.ended'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeEnded", null);

APlayer_decorate([Watch('mini'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeMini", null);

APlayer_decorate([Watch('isMini', {
  immediate: true
}), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", [Boolean, Boolean]), APlayer_metadata("design:returntype", Promise)], APlayer_APlayer.prototype, "handleChangeCurrentMini", null);

APlayer_decorate([Watch('loop'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeLoop", null);

APlayer_decorate([Watch('currentLoop'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeCurrentLoop", null);

APlayer_decorate([Watch('order'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeOrder", null);

APlayer_decorate([Watch('currentOrder'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeCurrentOrder", null);

APlayer_decorate([Watch('listVisible'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeListVisible", null);

APlayer_decorate([Watch('lyricVisible'), APlayer_metadata("design:type", Function), APlayer_metadata("design:paramtypes", []), APlayer_metadata("design:returntype", void 0)], APlayer_APlayer.prototype, "handleChangeLyricVisible", null);

APlayer_APlayer = APlayer_decorate([vue_class_component_common_default()({
  mixins: [mixin]
})], APlayer_APlayer);
/* harmony default export */ var components_APlayer = (APlayer_APlayer);
// CONCATENATED MODULE: ./packages/@moefe/vue-aplayer/index.ts




function install(Vue, options) {
  var defaultOptions = {
    productionTip: true,
    defaultCover: 'https://avatars2.githubusercontent.com/u/20062482?s=270'
  };

  var opts = _objectSpread({}, defaultOptions, options);

  Object.assign(components_APlayer.prototype, {
    options: opts
  });
  Vue.component('aplayer', components_APlayer);
  Vue.component('APlayer', components_APlayer);

  if (opts.productionTip) {
    // eslint-disable-next-line no-console
    console.log("%c vue-aplayer %c v".concat("2.0.0-beta.5", " ").concat("dd10c50", " %c"), 'background: #35495e; padding: 1px; border-radius: 3px 0 0 3px; color: #fff', 'background: #41b883; padding: 1px; border-radius: 0 3px 3px 0; color: #fff', 'background: transparent');
  }
}
// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/entry-lib.js
/* concated harmony reexport APlayer */__webpack_require__.d(__webpack_exports__, "APlayer", function() { return components_APlayer; });


/* harmony default export */ var entry_lib = __webpack_exports__["default"] = (install);



/***/ }),

/***/ "fdef":
/***/ (function(module, exports) {

module.exports = '\x09\x0A\x0B\x0C\x0D\x20\xA0\u1680\u180E\u2000\u2001\u2002\u2003' +
  '\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';


/***/ })

/******/ });
});
//# sourceMappingURL=VueAPlayer.umd.js.map