"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.Shape = exports.version = void 0;
var tslib_1 = require("tslib");
var Shape = require("./shape");
exports.Shape = Shape;
var pkg = require('../package.json');
exports.version = pkg.version;
tslib_1.__exportStar(require("./types"), exports);
tslib_1.__exportStar(require("./interfaces"), exports);
var g_base_1 = require("@antv/g-base");
Object.defineProperty(exports, "Event", { enumerable: true, get: function () { return g_base_1.Event; } });
var canvas_1 = require("./canvas");
Object.defineProperty(exports, "Canvas", { enumerable: true, get: function () { return canvas_1.default; } });
var group_1 = require("./group");
Object.defineProperty(exports, "Group", { enumerable: true, get: function () { return group_1.default; } });
//# sourceMappingURL=index.js.map