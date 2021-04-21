module.exports =
/******/ (function(modules) { // webpackBootstrap
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
/******/ 	return __webpack_require__(__webpack_require__.s = "2b43");
/******/ })
/************************************************************************/
/******/ ({

/***/ "2b43":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: ./node_modules/_@vue_cli-service@3.2.2@@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  var setPublicPath_i
  if ((setPublicPath_i = window.document.currentScript) && (setPublicPath_i = setPublicPath_i.src.match(/(.+\/)[^/]+\.js(\?.*)?$/))) {
    __webpack_require__.p = setPublicPath_i[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// CONCATENATED MODULE: ./node_modules/_cache-loader@1.2.5@cache-loader/dist/cjs.js?{"cacheDirectory":"node_modules/.cache/vue-loader","cacheIdentifier":"2bf59b84-vue-loader-template"}!./node_modules/_vue-loader@15.4.2@vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/_cache-loader@1.2.5@cache-loader/dist/cjs.js??ref--0-0!./node_modules/_vue-loader@15.4.2@vue-loader/lib??vue-loader-options!./packages/vue-particle-line/src/vue-particle-line.vue?vue&type=template&id=06727b55&scoped=true&
var render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:"vue-particle-line"},[_c('div',{staticClass:"slot-wraper"},[_vm._t("default")],2),_c('canvas',{staticClass:"canvas",attrs:{"id":"canvas"}})])}
var staticRenderFns = []


// CONCATENATED MODULE: ./packages/vue-particle-line/src/vue-particle-line.vue?vue&type=template&id=06727b55&scoped=true&

// CONCATENATED MODULE: ./packages/vue-particle-line/src/particle-line/color.js
class Color {
  constructor (min) {
    this.min = min || 0
    this._init(this.min)
  }

  _init (min) {
    this.r = this.colorValue(min)
    this.g = this.colorValue(min)
    this.b = this.colorValue(min)
    this.style = this.createColorStyle(this.r, this.g, this.b)
  }

  colorValue (min) {
    return Math.floor(Math.random() * 255 + min)
  }

  createColorStyle (r, g, b) {
    return `rgba(${r}, ${g}, ${b}, .8)`
  }
}

// CONCATENATED MODULE: ./packages/vue-particle-line/src/particle-line/dot.js


class dot_Dot {
  constructor (ctx, canvasWidth, canvasHeight, x, y) {
    this.ctx = ctx
    this.x = x || Math.random() * canvasWidth
    this.y = y || Math.random() * canvasHeight
    this._init()
  }

  _init () {
    this.vx = -0.5 + Math.random()
    this.vy = -0.5 + Math.random()
    this.radius = Math.random() * 3
    this.color = new Color()
  }

  draw () {
    this.ctx.beginPath()
    this.ctx.fillStyle = this.color.style
    this.ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2, false)
    this.ctx.fill()
  }
}

// CONCATENATED MODULE: ./packages/vue-particle-line/src/particle-line/index.js



const minWidth = 1200
const minHeight = 700

class particle_line_ParticleLine {
  constructor (tagId, options) {
    this.tagId = tagId
    this.options = options
    this.init()
  }

  init () {
    const canvas = document.querySelector(this.tagId)
    const ctx = canvas.getContext('2d')
    canvas.width = document.body.clientWidth > minWidth ? document.body.clientWidth : minWidth
    canvas.height = document.body.clientHeight > minHeight ? document.body.clientHeight : minHeight
    ctx.lineWidth = (this.options && this.options.lineWidth) || 0.3
    ctx.strokeStyle = (new Color(150)).style
    this.dots = {
      nb: (this.options && this.options.dotsNumber) || 100,
      distance: (this.options && this.options.dotsDistance) || 100,
      array: []
    }
    this.canvas = canvas
    this.ctx = ctx
    this.color = new Color()
    this.createDots(this.ctx, this.canvas.width, this.canvas.height)
    this.animateDots()
    this.hoverEffect()
  }

  hoverEffect () {
    if (this.options && this.options.hoverEffect) {
      this.canvas.addEventListener('mousemove', e => {
        if (this.dots.array.length > this.dots.nb) {
          this.dots.array.pop()
        }
        this.dots.array.push(new dot_Dot(this.ctx, this.canvas.width, this.canvas.height, e.pageX, e.pageY))
      })
    }
  }

  resize () {
    const width = document.body.clientWidth > minWidth ? document.body.clientWidth : minWidth
    const height = document.body.clientHeight > minHeight ? document.body.clientHeight : minHeight
    this.canvas.width = width
    this.canvas.height = height
    this.createDots(this.ctx, width, height)
  }

  mixComponents (comp1, weight1, comp2, weight2) {
    return (comp1 * weight1 + comp2 * weight2) / (weight1 + weight2)
  }

  averageColorStyles (dot1, dot2) {
    const color1 = dot1.color
    const color2 = dot2.color
    const r = this.mixComponents(color1.r, dot1.radius, color2.r, dot2.radius)
    const g = this.mixComponents(color1.g, dot1.radius, color2.g, dot2.radius)
    const b = this.mixComponents(color1.b, dot1.radius, color2.b, dot2.radius)
    return this.color.createColorStyle(Math.floor(r), Math.floor(g), Math.floor(b))
  }

  createDots (ctx, canvasWidth, canvasHeight) {
    this.dots.array = []
    for (let i = 0; i < this.dots.nb; i++) {
      this.dots.array.push(new dot_Dot(ctx, canvasWidth, canvasHeight))
    }
  }

  moveDots () {
    for (let i = 0; i < this.dots.nb; i++) {
      const dot = this.dots.array[i]
      if (dot.y < 0 || dot.y > this.canvas.height) {
        dot.vx = dot.vx // eslint-disable-line
        dot.vy = -dot.vy
      } else if (dot.x < 0 || dot.x > this.canvas.width) {
        dot.vx = -dot.vx
        dot.vy = dot.vy // eslint-disable-line
      }
      dot.x += dot.vx
      dot.y += dot.vy
    }
  }

  connectDots () {
    for (let i = 0; i < this.dots.array.length; i++) {
      for (let j = 0; j < this.dots.array.length; j++) {
        const iDot = this.dots.array[i]
        const jDot = this.dots.array[j]
        if ((iDot.x - jDot.x) < this.dots.distance && (iDot.y - jDot.y) < this.dots.distance && (iDot.x - jDot.x) > -this.dots.distance && (iDot.y - jDot.y) > -this.dots.distance) {
          this.ctx.beginPath()
          this.ctx.strokeStyle = this.averageColorStyles(iDot, jDot)
          this.ctx.moveTo(iDot.x, iDot.y)
          this.ctx.lineTo(jDot.x, jDot.y)
          this.ctx.stroke()
          this.ctx.closePath()
        }
      }
    }
  }

  drawDots () {
    for (let i = 0; i < this.dots.array.length; i++) {
      const dot = this.dots.array[i]
      dot.draw()
    }
  }

  animateDots () {
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height)
    this.drawDots()
    this.connectDots()
    this.moveDots()
    requestAnimationFrame(this.animateDots.bind(this))
  }
}

// CONCATENATED MODULE: ./node_modules/_cache-loader@1.2.5@cache-loader/dist/cjs.js??ref--0-0!./node_modules/_vue-loader@15.4.2@vue-loader/lib??vue-loader-options!./packages/vue-particle-line/src/vue-particle-line.vue?vue&type=script&lang=js&
//
//
//
//
//
//
//
//
//


// import { debounce } from 'common/js/utils'
/* harmony default export */ var vue_particle_linevue_type_script_lang_js_ = ({
  name: 'vue-particle-line',
  props: {
    lineWidth: {
      type: Number,
      default: 0.3
    },
    dotsNumber: {
      type: Number,
      default: 100
    },
    dotsDistance: {
      type: Number,
      default: 100
    },
    hoverEffect: {
      type: Boolean,
      default: true
    }
  },
  mounted () {
    /* eslint-disable no-new */
    new particle_line_ParticleLine('canvas', {
      lineWidth: this.lineWidth,
      dotsNumber: this.dotsNumber,
      dotsDistance: this.dotsDistance,
      hoverEffect: this.hoverEffect
    })
    // particleLine.init()
    // window.onresize = debounce(() => particleLine.resize(), 500)
  }
});

// CONCATENATED MODULE: ./packages/vue-particle-line/src/vue-particle-line.vue?vue&type=script&lang=js&
 /* harmony default export */ var src_vue_particle_linevue_type_script_lang_js_ = (vue_particle_linevue_type_script_lang_js_); 
// EXTERNAL MODULE: ./packages/vue-particle-line/src/vue-particle-line.vue?vue&type=style&index=0&id=06727b55&lang=scss&scoped=true&
var vue_particle_linevue_type_style_index_0_id_06727b55_lang_scss_scoped_true_ = __webpack_require__("7bb6");

// CONCATENATED MODULE: ./node_modules/_vue-loader@15.4.2@vue-loader/lib/runtime/componentNormalizer.js
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () { injectStyles.call(this, this.$root.$options.shadowRoot) }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}

// CONCATENATED MODULE: ./packages/vue-particle-line/src/vue-particle-line.vue






/* normalize component */

var component = normalizeComponent(
  src_vue_particle_linevue_type_script_lang_js_,
  render,
  staticRenderFns,
  false,
  null,
  "06727b55",
  null
  
)

component.options.__file = "vue-particle-line.vue"
/* harmony default export */ var vue_particle_line = (component.exports);
// CONCATENATED MODULE: ./packages/vue-particle-line/index.js
// 导入组件，组件必须声明 name


// 为组件提供 install 安装方法，供按需引入
vue_particle_line.install = function (Vue) {
  Vue.component(vue_particle_line.name, vue_particle_line)
}

// 默认导出组件
/* harmony default export */ var packages_vue_particle_line = (vue_particle_line);

// CONCATENATED MODULE: ./packages/index.js
// 导入组件


// 存储组件列表
const components = [
  packages_vue_particle_line
]

// 定义 install 方法，接收 Vue 作为参数。如果使用 use 注册插件，则所有的组件都将被注册
const install = function (Vue) {
  // 判断是否安装
  if (install.installed) return
  // 遍历注册全局组件
  components.map(component => Vue.component(component.name, component))
}

// 判断是否是直接引入文件
if (typeof window !== 'undefined' && window.Vue) {
  install(window.Vue)
}

/* harmony default export */ var packages_0 = ({
  // 导出的对象必须具有 install，才能被 Vue.use() 方法安装
  install,
  // 以下是具体的组件列表
  vueParticleLine: packages_vue_particle_line
});

// CONCATENATED MODULE: ./node_modules/_@vue_cli-service@3.2.2@@vue/cli-service/lib/commands/build/entry-lib.js


/* harmony default export */ var entry_lib = __webpack_exports__["default"] = (packages_0);



/***/ }),

/***/ "7bb6":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _node_modules_mini_css_extract_plugin_0_5_0_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_1_0_1_css_loader_index_js_ref_8_oneOf_1_1_node_modules_vue_loader_15_4_2_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_3_0_0_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_7_1_0_sass_loader_lib_loader_js_ref_8_oneOf_1_3_node_modules_cache_loader_1_2_5_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_15_4_2_vue_loader_lib_index_js_vue_loader_options_vue_particle_line_vue_vue_type_style_index_0_id_06727b55_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("8787");
/* harmony import */ var _node_modules_mini_css_extract_plugin_0_5_0_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_1_0_1_css_loader_index_js_ref_8_oneOf_1_1_node_modules_vue_loader_15_4_2_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_3_0_0_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_7_1_0_sass_loader_lib_loader_js_ref_8_oneOf_1_3_node_modules_cache_loader_1_2_5_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_15_4_2_vue_loader_lib_index_js_vue_loader_options_vue_particle_line_vue_vue_type_style_index_0_id_06727b55_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_0_5_0_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_1_0_1_css_loader_index_js_ref_8_oneOf_1_1_node_modules_vue_loader_15_4_2_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_3_0_0_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_7_1_0_sass_loader_lib_loader_js_ref_8_oneOf_1_3_node_modules_cache_loader_1_2_5_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_15_4_2_vue_loader_lib_index_js_vue_loader_options_vue_particle_line_vue_vue_type_style_index_0_id_06727b55_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* unused harmony reexport * */
 /* unused harmony default export */ var _unused_webpack_default_export = (_node_modules_mini_css_extract_plugin_0_5_0_mini_css_extract_plugin_dist_loader_js_ref_8_oneOf_1_0_node_modules_css_loader_1_0_1_css_loader_index_js_ref_8_oneOf_1_1_node_modules_vue_loader_15_4_2_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_3_0_0_postcss_loader_src_index_js_ref_8_oneOf_1_2_node_modules_sass_loader_7_1_0_sass_loader_lib_loader_js_ref_8_oneOf_1_3_node_modules_cache_loader_1_2_5_cache_loader_dist_cjs_js_ref_0_0_node_modules_vue_loader_15_4_2_vue_loader_lib_index_js_vue_loader_options_vue_particle_line_vue_vue_type_style_index_0_id_06727b55_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "8787":
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ })

/******/ });
//# sourceMappingURL=vue-particle-line.common.js.map