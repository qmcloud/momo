"use strict";
/**
 * @fileoverview G 的基础接口定义和所有的抽象类
 * @author dxq613@gmail.com
 */
Object.defineProperty(exports, "__esModule", { value: true });
var PathUtil = require("./util/path");
exports.PathUtil = PathUtil;
var pkg = require('../package.json');
exports.version = pkg.version;
var graph_event_1 = require("./event/graph-event");
exports.Event = graph_event_1.default;
var base_1 = require("./abstract/base");
exports.Base = base_1.default;
var canvas_1 = require("./abstract/canvas");
exports.AbstractCanvas = canvas_1.default;
var group_1 = require("./abstract/group");
exports.AbstractGroup = group_1.default;
var shape_1 = require("./abstract/shape");
exports.AbstractShape = shape_1.default;
//# sourceMappingURL=index.js.map