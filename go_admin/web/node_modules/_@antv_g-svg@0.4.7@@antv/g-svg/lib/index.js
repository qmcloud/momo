"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var Shape = require("./shape");
exports.Shape = Shape;
var pkg = require('../package.json');
exports.version = pkg.version;
var g_base_1 = require("@antv/g-base");
exports.Event = g_base_1.Event;
var canvas_1 = require("./canvas");
exports.Canvas = canvas_1.default;
var group_1 = require("./group");
exports.Group = group_1.default;
//# sourceMappingURL=index.js.map