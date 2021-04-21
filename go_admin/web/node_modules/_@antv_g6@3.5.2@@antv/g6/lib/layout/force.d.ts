/**
 * @fileOverview random layout
 * @author shiwu.wyy@antfin.com
 */
import { GraphData, IPointTuple } from '../types';
import { BaseLayout } from './layout';
/**
 * 经典力导布局 force-directed
 */
export default class ForceLayout<Cfg = any> extends BaseLayout {
    /** 向心力作用点 */
    center: IPointTuple;
    /** 节点作用力 */
    nodeStrength: number | null;
    /** 边的作用力, 默认为根据节点的入度出度自适应 */
    edgeStrength: number | null;
    /** 是否防止节点相互覆盖 */
    preventOverlap: boolean;
    /** 节点大小 / 直径，用于防止重叠时的碰撞检测 */
    nodeSize: number | number[] | ((d?: unknown) => number) | undefined;
    /** 节点间距，防止节点重叠时节点之间的最小距离（两节点边缘最短距离） */
    nodeSpacing: ((d?: unknown) => number) | undefined;
    /** 默认边长度 */
    linkDistance: number;
    /** 自定义 force 方法 */
    forceSimulation: any;
    /** 迭代阈值的衰减率 [0, 1]，0.028 对应最大迭代数为 300 */
    alphaDecay: number;
    /** 停止迭代的阈值 */
    alphaMin: number;
    /** 当前阈值 */
    alpha: number;
    /** 防止重叠的力强度 */
    collideStrength: number;
    /** 是否启用web worker。前提是在web worker里执行布局，否则无效	*/
    workerEnabled: boolean;
    tick: () => void;
    /** 布局完成回调 */
    onLayoutEnd: () => void;
    /** 布局每一次迭代完成的回调 */
    onTick: () => void;
    /** 是否正在布局 */
    private ticking;
    getDefaultCfg(): {
        center: number[];
        nodeStrength: any;
        edgeStrength: any;
        preventOverlap: boolean;
        nodeSize: any;
        nodeSpacing: any;
        linkDistance: number;
        forceSimulation: any;
        alphaDecay: number;
        alphaMin: number;
        alpha: number;
        collideStrength: number;
        tick(): void;
        onLayoutEnd(): void;
        onTick(): void;
        workerEnabled: boolean;
    };
    /**
     * 初始化
     * @param {object} data 数据
     */
    init(data: GraphData): void;
    /**
     * 执行布局
     */
    execute(): void;
    /**
     * 防止重叠
     * @param {object} simulation 力模拟模型
     */
    overlapProcess(simulation: any): void;
    /**
     * 更新布局配置，但不执行布局
     * @param {object} cfg 需要更新的配置项
     */
    updateCfg(cfg: Partial<Cfg>): void;
    destroy(): void;
}
