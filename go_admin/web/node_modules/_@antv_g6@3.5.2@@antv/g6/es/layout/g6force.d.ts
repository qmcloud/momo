/**
 * @fileOverview G6's force layout, supports clustering
 * @author shiwu.wyy@antfin.com
 */
import { EdgeConfig, IPointTuple, NodeConfig } from '../types';
import { BaseLayout } from './layout';
declare type Node = NodeConfig & {
    cluster: string | number;
};
declare type Edge = EdgeConfig;
/**
 * G6's force layout
 */
export default class G6Force extends BaseLayout {
    /** 布局中心 */
    center: IPointTuple;
    /** 停止迭代的最大迭代数 */
    maxIteration: number;
    /** 重力大小，影响图的紧凑程度 */
    gravity: number;
    /** 是否产生聚类力 */
    clustering: boolean;
    /** 聚类力大小 */
    clusterGravity: number;
    /** 默认边长度 */
    linkDistance: number | ((d?: unknown) => number);
    /** 每次迭代位移的衰减相关参数 */
    alpha: number;
    alphaMin: number;
    alphaDecay: number;
    alphaTarget: number;
    /** 节点运动速度衰减参数 */
    velocityDecay: number;
    /** 边引力大小 */
    linkStrength: number | ((d?: unknown) => number);
    /** 节点引力大小 */
    nodeStrength: number | ((d?: unknown) => number);
    /** 是否开启防止重叠 */
    preventOverlap: boolean;
    /** 防止重叠的碰撞力大小 */
    collideStrength: number;
    /** 节点大小，用于防止重叠 */
    nodeSize: number | number[] | ((d?: unknown) => number) | undefined;
    /** 节点最小间距，防止重叠时的间隙 */
    nodeSpacing: ((d?: unknown) => number) | undefined;
    /** 优化计算斥力的速度，两节点间距超过 optimizeRangeFactor * width 则不再计算斥力和重叠斥力 */
    optimizeRangeFactor: number;
    /** 每次迭代的回调函数 */
    tick: () => void;
    /** 内部计算参数 */
    nodes: Node[];
    edges: Edge[];
    private width;
    private height;
    private bias;
    private nodeMap;
    private nodeIdxMap;
    getDefaultCfg(): {
        maxIteration: number;
        center: number[];
        gravity: number;
        speed: number;
        clustering: boolean;
        clusterGravity: number;
        preventOverlap: boolean;
        nodeSpacing: any;
        collideStrength: number;
    };
    /**
     * 执行布局
     */
    execute(): void;
    run(): void;
    private initVals;
    private getClusterMap;
    private applyClusterForce;
    private applyCalculate;
    private calRepulsive;
    private calAttractive;
}
export {};
