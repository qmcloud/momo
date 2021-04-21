/**
 * @fileOverview grid layout
 * @author shiwu.wyy@antfin.com
 * this algorithm refers to <cytoscape.js> - https://github.com/cytoscape/cytoscape.js/
 */
import { EdgeConfig, IPointTuple, NodeConfig } from '../types';
import { BaseLayout } from './layout';
declare type Node = NodeConfig & {
    degree: number;
};
declare type Edge = EdgeConfig;
/**
 * 网格布局
 */
export default class GridLayout extends BaseLayout {
    /** 布局起始点 */
    begin: IPointTuple;
    /** prevents node overlap, may overflow boundingBox if not enough space */
    preventOverlap: boolean;
    /** extra spacing around nodes when preventOverlap: true */
    preventOverlapPadding: number;
    /** uses all available space on false, uses minimal space on true */
    condense: boolean;
    /** force num of rows in the grid */
    rows: number | undefined;
    /** force num of columns in the grid */
    cols: number | undefined;
    /** returns { row, col } for element */
    position: ((node: Node) => {
        row: number;
        col: number;
    }) | undefined;
    /** a sorting function to order the nodes; e.g. function(a, b){ return a.datapublic ('weight') - b.data('weight') } */
    sortBy: string;
    nodeSize: number | number[];
    nodes: Node[];
    edges: Edge[];
    /** 布局中心 */
    center: IPointTuple;
    width: number;
    height: number;
    private cells;
    private row;
    private col;
    private splits;
    private columns;
    private cellWidth;
    private cellHeight;
    private cellUsed;
    private id2manPos;
    getDefaultCfg(): {
        begin: number[];
        preventOverlap: boolean;
        preventOverlapPadding: number;
        condense: boolean;
        rows: any;
        cols: any;
        position: any;
        sortBy: string;
        nodeSize: number;
    };
    /**
     * 执行布局
     */
    execute(): void;
    private small;
    private large;
    private used;
    private use;
    private moveToNextCell;
    private getPos;
}
export {};
