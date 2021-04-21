import { G6GraphEvent } from '../interface/behavior';
import { IG6GraphEvent, Padding, Matrix, Item } from '../types';
/**
 * turn padding into [top, right, bottom, right]
 * @param  {Number|Array} padding input padding
 * @return {array} output
 */
export declare const formatPadding: (padding: Padding) => number[];
/**
 * clone event
 * @param e
 */
export declare const cloneEvent: (e: IG6GraphEvent) => G6GraphEvent;
/**
 * 判断 viewport 是否改变，通过和单位矩阵对比
 * @param matrix Viewport 的 Matrix
 */
export declare const isViewportChanged: (matrix: Matrix) => boolean;
export declare const isNaN: (input: any) => boolean;
/**
 * 计算一组 Item 的 BBox
 * @param items 选中的一组Item，可以是 node 或 combo
 */
export declare const calculationItemsBBox: (items: Item[]) => {
    x: number;
    y: number;
    width: number;
    height: number;
    minX: number;
    minY: number;
};
