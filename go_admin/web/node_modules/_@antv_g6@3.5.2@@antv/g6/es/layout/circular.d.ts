/**
 * @fileOverview random layout
 * @author shiwu.wyy@antfin.com
 */
import { EdgeConfig, IPointTuple, NodeConfig } from '../types';
import { BaseLayout } from './layout';
declare type Node = NodeConfig & {
    degree: number;
    children: string[];
    parent: string[];
};
declare type Edge = EdgeConfig;
/**
 * 圆形布局
 */
export default class CircularLayout extends BaseLayout {
    /** 布局中心 */
    center: IPointTuple;
    /** 固定半径，若设置了 radius，则 startRadius 与 endRadius 不起效 */
    radius: number | null;
    /** 起始半径 */
    startRadius: number | null;
    /** 终止半径 */
    endRadius: number | null;
    /** 起始角度 */
    startAngle: number;
    /** 终止角度 */
    endAngle: number;
    /** 是否顺时针 */
    clockwise: boolean;
    /** 节点在环上分成段数（几个段将均匀分布），在 endRadius - startRadius != 0 时生效 */
    divisions: number;
    /** 节点在环上排序的依据，可选: 'topology', 'degree', 'null' */
    ordering: 'topology' | 'topology-directed' | 'degree' | null;
    /** how many 2*pi from first to last nodes */
    angleRatio: number;
    nodes: Node[];
    edges: Edge[];
    private nodeMap;
    private degrees;
    private astep;
    width: number;
    height: number;
    getDefaultCfg(): {
        center: number[];
        radius: any;
        startRadius: any;
        endRadius: any;
        startAngle: number;
        endAngle: number;
        clockwise: boolean;
        divisions: number;
        ordering: any;
        angleRatio: number;
    };
    /**
     * 执行布局
     */
    execute(): void;
    /**
     * 根据节点的拓扑结构排序
     * @return {array} orderedNodes 排序后的结果
     */
    topologyOrdering(directed?: boolean): Node[];
    /**
     * 根据节点度数大小排序
     * @return {array} orderedNodes 排序后的结果
     */
    degreeOrdering(): Node[];
}
export {};
