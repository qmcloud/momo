import { Point } from '@antv/g-base/lib/types';
import { IGroup } from '@antv/g-canvas/lib/interfaces';
import { GraphData, ICircle, IEllipse, IRect, Matrix, EdgeConfig, NodeIdxMap } from '../types';
/**
 * point and rectangular intersection point
 * @param  {IRect} rect  rect
 * @param  {Point} point point
 * @return {PointPoint} rst;
 */
export declare const getRectIntersectByPoint: (rect: IRect, point: Point) => Point | null;
/**
 * get point and circle inIntersect
 * @param {ICircle} circle 圆点，x,y,r
 * @param {Point} point 点 x,y
 * @return {Point} applied point
 */
export declare const getCircleIntersectByPoint: (circle: ICircle, point: Point) => Point | null;
/**
 * get point and ellipse inIntersect
 * @param {Object} ellipse 椭圆 x,y,rx,ry
 * @param {Object} point 点 x,y
 * @return {object} applied point
 */
export declare const getEllipseIntersectByPoint: (ellipse: IEllipse, point: Point) => Point;
/**
 * coordinate matrix transformation
 * @param  {number} point   coordinate
 * @param  {Matrix} matrix  matrix
 * @param  {number} tag     could be 0 or 1
 * @return {Point} transformed point
 */
export declare const applyMatrix: (point: Point, matrix: Matrix, tag?: 0 | 1) => Point;
/**
 * coordinate matrix invert transformation
 * @param  {number} point   coordinate
 * @param  {number} matrix  matrix
 * @param  {number} tag     could be 0 or 1
 * @return {object} transformed point
 */
export declare const invertMatrix: (point: Point, matrix: Matrix, tag?: 0 | 1) => Point;
/**
 *
 * @param p1 First coordinate
 * @param p2 second coordinate
 * @param p3 three coordinate
 */
export declare const getCircleCenterByPoints: (p1: Point, p2: Point, p3: Point) => Point;
/**
 * get distance by two points
 * @param p1 first point
 * @param p2 second point
 */
export declare const distance: (p1: Point, p2: Point) => number;
/**
 * scale matrix
 * @param matrix [ [], [], [] ]
 * @param ratio
 */
export declare const scaleMatrix: (matrix: Matrix[], ratio: number) => Matrix[];
/**
 * Floyd Warshall algorithm for shortest path distances matrix
 * @param  {array} adjMatrix   adjacency matrix
 * @return {array} distances   shortest path distances matrix
 */
export declare const floydWarshall: (adjMatrix: Matrix[]) => Matrix[];
/**
 * get adjacency matrix
 * @param data graph data
 * @param directed whether it's a directed graph
 */
export declare const getAdjMatrix: (data: GraphData, directed: boolean) => Matrix[];
/**
 * 平移group
 * @param group Group 实例
 * @param vec 移动向量
 */
export declare const translate: (group: IGroup, vec: Point) => void;
/**
 * 移动到指定坐标点
 * @param group Group 实例
 * @param point 移动到的坐标点
 */
export declare const move: (group: IGroup, point: Point) => void;
/**
 * 缩放 group
 * @param group Group 实例
 * @param point 在x 和 y 方向上的缩放比例
 */
export declare const scale: (group: IGroup, ratio: number | number[]) => void;
/**
 *
 * @param group Group 实例
 * @param ratio 选择角度
 */
export declare const rotate: (group: IGroup, angle: number) => void;
export declare const getDegree: (n: number, nodeIdxMap: NodeIdxMap, edges: EdgeConfig[]) => number[];
