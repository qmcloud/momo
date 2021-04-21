/**
 * @fileOverview concentric layout
 * @author shiwu.wyy@antfin.com
 * this algorithm refers to <cytoscape.js> - https://github.com/cytoscape/cytoscape.js/
 */
import { EdgeConfig, IPointTuple, NodeConfig } from '../types';
import { BaseLayout } from './layout';
declare type Node = NodeConfig & {
    [key: string]: number;
};
declare type Edge = EdgeConfig;
/**
 * 同心圆布局
 */
export default class ConcentricLayout extends BaseLayout {
    /** 布局中心 */
    center: IPointTuple;
    nodeSize: number | IPointTuple;
    /** min spacing between outside of nodes (used for radius adjustment) */
    minNodeSpacing: number;
    /** prevents node overlap, may overflow boundingBox if not enough space */
    preventOverlap: boolean;
    /** how many radians should be between the first and last node (defaults to full circle) */
    sweep: number | undefined;
    /** whether levels have an equal radial distance betwen them, may cause bounding box overflow */
    equidistant: boolean;
    /** where nodes start in radians */
    startAngle: number;
    /** whether the layout should go clockwise (true) or counterclockwise/anticlockwise (false) */
    clockwise: boolean;
    /** the letiation of concentric values in each level */
    maxLevelDiff: undefined | number;
    /** 根据 sortBy 指定的属性进行排布，数值高的放在中心，如果是 sortBy 则会计算节点度数，度数最高的放在中心 */
    sortBy: string;
    nodes: Node[];
    edges: Edge[];
    width: number;
    height: number;
    private maxValueNode;
    private counterclockwise;
    getDefaultCfg(): {
        center: number[];
        nodeSize: number;
        minNodeSpacing: number;
        preventOverlap: boolean;
        sweep: any;
        equidistant: boolean;
        startAngle: number;
        clockwise: boolean;
        maxLevelDiff: any;
        sortBy: string;
    };
    /**
     * 执行布局
     */
    execute(): void;
}
export {};
