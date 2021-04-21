/**
 * @fileOverview fruchterman layout
 * @author shiwu.wyy@antfin.com
 */
import { EdgeConfig, IPointTuple, NodeConfig, NodeIdxMap } from '../types';
import { BaseLayout } from './layout';
declare type Node = NodeConfig & {
    cluster: string | number;
};
declare type Edge = EdgeConfig;
declare type NodeMap = {
    [key: string]: Node;
};
/**
 * fruchterman 布局
 */
export default class FruchtermanLayout extends BaseLayout {
    /** 布局中心 */
    center: IPointTuple;
    /** 停止迭代的最大迭代数 */
    maxIteration: number;
    /** 重力大小，影响图的紧凑程度 */
    gravity: number;
    /** 速度 */
    speed: number;
    /** 是否产生聚类力 */
    clustering: boolean;
    /** 聚类力大小 */
    clusterGravity: number;
    nodes: Node[];
    edges: Edge[];
    width: number;
    height: number;
    nodeMap: NodeMap;
    nodeIdxMap: NodeIdxMap;
    getDefaultCfg(): {
        maxIteration: number;
        center: number[];
        gravity: number;
        speed: number;
        clustering: boolean;
        clusterGravity: number;
    };
    /**
     * 执行布局
     */
    execute(): void;
    run(): void;
    private applyCalculate;
    private calRepulsive;
    private calAttractive;
}
export {};
