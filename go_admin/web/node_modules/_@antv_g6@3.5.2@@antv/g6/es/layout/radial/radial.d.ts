/**
 * @fileOverview random layout
 * @author shiwu.wyy@antfin.com
 */
import { IPointTuple, NodeConfig } from '../../types';
import { BaseLayout } from '../layout';
declare type Node = NodeConfig;
/**
 * 辐射状布局
 */
export default class RadialLayout extends BaseLayout {
    /** 布局中心 */
    center: IPointTuple;
    /** 停止迭代的最大迭代数 */
    maxIteration: number;
    /** 中心点，默认为数据中第一个点 */
    focusNode: string | Node | null;
    /** 每一圈半径 */
    unitRadius: number | null;
    /** 默认边长度 */
    linkDistance: number;
    /** 是否防止重叠 */
    preventOverlap: boolean;
    /** 节点直径 */
    nodeSize: number | undefined;
    /** 节点间距，防止节点重叠时节点之间的最小距离（两节点边缘最短距离） */
    nodeSpacing: number | Function | undefined;
    /** 是否必须是严格的 radial 布局，即每一层的节点严格布局在一个环上。preventOverlap 为 true 时生效 */
    strictRadial: boolean;
    /** 防止重叠步骤的最大迭代次数 */
    maxPreventOverlapIteration: number;
    sortBy: string | undefined;
    sortStrength: number;
    width: number | undefined;
    height: number | undefined;
    private focusIndex;
    private distances;
    private eIdealDistances;
    private weights;
    private radii;
    getDefaultCfg(): {
        center: number[];
        maxIteration: number;
        focusNode: any;
        unitRadius: any;
        linkDistance: number;
        preventOverlap: boolean;
        nodeSize: any;
        nodeSpacing: any;
        strictRadial: boolean;
        maxPreventOverlapIteration: number;
        sortBy: any;
        sortStrength: number;
    };
    /**
     * 执行布局
     */
    execute(): void;
    run(): void;
    private oneIteration;
    private eIdealDisMatrix;
    private handleInfinity;
    private maxToFocus;
}
export {};
