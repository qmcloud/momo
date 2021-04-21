export declare function getPixelRatio(): number;
/**
 * 两点之间的距离
 * @param {number} x1 起始点 x
 * @param {number} y1 起始点 y
 * @param {number} x2 结束点 x
 * @param {number} y2 结束点 y
 */
export declare function distance(x1: number, y1: number, x2: number, y2: number): number;
/**
 * 是否在包围盒内
 * @param {number} minX   包围盒开始的点 x
 * @param {number} minY   包围盒开始的点 y
 * @param {number} width  宽度
 * @param {number} height 高度
 * @param {[type]} x      检测点的 x
 * @param {[type]} y      监测点的 y
 */
export declare function inBox(minX: number, minY: number, width: number, height: number, x: any, y: any): boolean;
export declare function intersectRect(box1: any, box2: any): boolean;
export declare function mergeRegion(region1: any, region2: any): any;
/**
 * 判断两个点是否重合，点坐标的格式为 [x, y]
 * @param {Array} point1 第一个点
 * @param {Array} point2 第二个点
 */
export declare function isSamePoint(point1: any, point2: any): boolean;
export { default as isNil } from '@antv/util/lib/is-nil';
export { default as isString } from '@antv/util/lib/is-string';
export { default as isFunction } from '@antv/util/lib/is-function';
export { default as isArray } from '@antv/util/lib/is-array';
export { default as each } from '@antv/util/lib/each';
export { default as toRadian } from '@antv/util/lib/to-radian';
export { default as mod } from '@antv/util/lib/mod';
export { default as isNumberEqual } from '@antv/util/lib/is-number-equal';
export { default as requestAnimationFrame } from '@antv/util/lib/request-animation-frame';
export { default as clearAnimationFrame } from '@antv/util/lib/clear-animation-frame';
