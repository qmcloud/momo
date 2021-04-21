/**
 * @fileOverview Combo force layout
 * @author shiwu.wyy@antfin.com
 */
import { EdgeConfig, IPointTuple, NodeConfig, ComboConfig } from '../types';
import { BaseLayout } from './layout';
declare type Node = NodeConfig & {
    depth: number;
};
declare type Edge = EdgeConfig;
/**
 * force layout for graph with combos
 */
export default class ComboForce extends BaseLayout {
    /** 布局中心 */
    center: IPointTuple;
    /** 停止迭代的最大迭代数 */
    maxIteration: number;
    /** 重力大小，影响图的紧凑程度 */
    gravity: number;
    /** 群组中心力大小 */
    comboGravity: number;
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
    edgeStrength: number | ((d?: unknown) => number);
    /** 节点引力大小 */
    nodeStrength: number | ((d?: unknown) => number);
    /** 是否开启防止重叠 */
    preventOverlap: boolean;
    /** 是否开启节点之间的防止重叠 */
    preventNodeOverlap: boolean;
    /** 是否开启 Combo 之间的防止重叠 */
    preventComboOverlap: boolean;
    /** 防止重叠的碰撞力大小 */
    collideStrength: number | undefined;
    /** 防止重叠的碰撞力大小 */
    nodeCollideStrength: number | undefined;
    /** 防止重叠的碰撞力大小 */
    comboCollideStrength: number | undefined;
    /** 节点大小，用于防止重叠 */
    nodeSize: number | number[] | ((d?: unknown) => number) | undefined;
    /** 节点最小间距，防止重叠时的间隙 */
    nodeSpacing: ((d?: unknown) => number) | undefined;
    /** Combo 最小间距，防止重叠时的间隙 */
    comboSpacing: ((d?: unknown) => number) | undefined;
    /** Combo 内部的 padding */
    comboPadding: ((d?: unknown) => number) | undefined;
    /** 优化计算斥力的速度，两节点间距超过 optimizeRangeFactor * width 则不再计算斥力和重叠斥力 */
    optimizeRangeFactor: number;
    /** 每次迭代的回调函数 */
    onTick: () => void;
    /** 每次迭代的回调函数 */
    onLayoutEnd: () => void;
    /** 根据边两端节点层级差距的调整引力系数的因子，取值范围 [0, 1]。层级差距越大，引力越小 */
    depthAttractiveForceScale: number;
    /** 根据边两端节点层级差距的调整斥力系数的因子，取值范围 [1, Infinity]。层级差距越大，斥力越大 */
    depthRepulsiveForceScale: number;
    /** 内部计算参数 */
    nodes: Node[];
    edges: Edge[];
    combos: ComboConfig[];
    private comboTrees;
    private comboTree;
    private width;
    private height;
    private bias;
    private nodeMap;
    private oriComboMap;
    private nodeIdxMap;
    private comboMap;
    private previousLayouted;
    getDefaultCfg(): {
        maxIteration: number;
        center: number[];
        gravity: number;
        speed: number;
        comboGravity: number;
        preventOverlap: boolean;
        preventComboOverlap: boolean;
        preventNodeOverlap: boolean;
        nodeSpacing: any;
        collideStrength: any;
        nodeCollideStrength: number;
        comboCollideStrength: number;
        comboSpacing: number;
        comboPadding: number;
        edgeStrength: number;
        nodeStrength: number;
        linkDistance: number;
    };
    /**
     * 执行布局
     */
    execute(): void;
    run(): void;
    private initVals;
    private initPos;
    private getComboMap;
    private applyComboCenterForce;
    private applyCalculate;
    /**
     * Update the sizes of the combos according to their children
     * Used for combos nonoverlap, but not re-render the combo shapes
     */
    private updateComboSizes;
    /**
     * prevent the overlappings among combos
     */
    private comboNonOverlapping;
    /**
     * Calculate the repulsive force between each node pair
     * @param displacements The array stores the displacements for nodes
     * @param vecMap The map stores vector between each node pair
     */
    private calRepulsive;
    /**
     * Calculate the attractive force between the node pair with edge
     * @param displacements The array stores the displacements for nodes
     * @param vecMap The map stores vector between each node pair
     */
    private calAttractive;
}
export {};
