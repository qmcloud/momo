import Group from '@antv/g-canvas/lib/group';
import Path from '@antv/g-canvas/lib/shape/path';
import { EdgeData, IBBox, IShapeBase, LabelStyle, TreeGraphData, NodeConfig, ComboTree, ComboConfig } from '../types';
import { BBox } from '@antv/g-math/lib/types';
import { IGraph } from '../interface/graph';
export declare const getBBox: (element: IShapeBase, group: Group) => IBBox;
/**
 * get loop edge config
 * @param cfg edge config
 */
export declare const getLoopCfgs: (cfg: EdgeData) => EdgeData;
/**
 * 根据 label 所在线条的位置百分比，计算 label 坐标
 * @param {object}  pathShape  G 的 path 实例，一般是 Edge 实例的 keyShape
 * @param {number}  percent    范围 0 - 1 的线条百分比
 * @param {number}  refX     x 轴正方向为基准的 label 偏移
 * @param {number}  refY     y 轴正方向为基准的 label 偏移
 * @param {boolean} rotate     是否根据线条斜率旋转文本
 * @return {object} 文本的 x, y, 文本的旋转角度
 */
export declare const getLabelPosition: (pathShape: Path, percent: number, refX: number, refY: number, rotate: boolean) => LabelStyle;
/**
 * depth first traverse, from root to leaves, children in inverse order
 *  if the fn returns false, terminate the traverse
 */
export declare const traverseTree: <T extends {
    children?: T[];
}>(data: T, fn: (param: T) => boolean) => void;
/**
 * depth first traverse, from leaves to root, children in inverse order
 * if the fn returns false, terminate the traverse
 */
export declare const traverseTreeUp: <T extends {
    children?: T[];
}>(data: T, fn: (param: T) => boolean) => void;
export declare type TreeGraphDataWithPosition = TreeGraphData & {
    x: number;
    y: number;
    children?: TreeGraphDataWithPosition[];
};
/**
 *
 * @param data Tree graph data
 * @param layout
 */
export declare const radialLayout: (data: TreeGraphDataWithPosition, layout?: string) => TreeGraphDataWithPosition;
/**
 *
 * @param letter the letter
 * @param fontSize
 * @return the letter's width
 */
export declare const getLetterWidth: (letter: any, fontSize: any) => number;
/**
 *
 * @param text the text
 * @param fontSize
 * @return the text's size
 */
export declare const getTextSize: (text: any, fontSize: any) => any[];
/**
 * construct the trees from combos data
 * @param array the combos array
 * @param nodes the nodes array
 * @return the tree
 */
export declare const plainCombosToTrees: (array: ComboConfig[], nodes?: NodeConfig[]) => ComboTree[];
export declare const reconstructTree: (trees: ComboTree[], subtreeId?: string, newParentId?: string | undefined) => ComboTree[];
export declare const getComboBBox: (children: ComboTree[], graph: IGraph) => BBox;
