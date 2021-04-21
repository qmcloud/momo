import Base, { IPluginBaseConfig } from '../base';
import Edge from '../../item/edge';
import { GraphData, NodeConfig } from '../../types';
import { Point } from '@antv/g-base/lib/types';
interface BundlingConfig extends IPluginBaseConfig {
    edgeBundles?: Edge[];
    edgePoints?: NodeConfig[];
    K?: number;
    lambda?: number;
    divisions?: number;
    divRate?: number;
    cycles?: number;
    iterations?: number;
    iterRate?: number;
    bundleThreshold?: number;
    eps?: number;
    onLayoutEnd?: () => void;
    onTick?: () => void;
}
interface VectorPosition {
    source: {
        x: number;
        y: number;
    };
    target: {
        x: number;
        y: number;
    };
    vx: number;
    vy: number;
    length: number;
}
export default class Bundling extends Base {
    constructor(cfg?: BundlingConfig);
    getDefaultCfgs(): BundlingConfig;
    init(): void;
    bundling(data: GraphData): void;
    updateBundling(cfg: BundlingConfig): void;
    divideEdges(divisions: number): Point[][];
    /**
     * 计算边的长度
     * @param points
     */
    getEdgeLength(points: Point[]): number;
    getEdgeBundles(): number[];
    getBundleScore(ei: any, ej: any): number;
    protected getAngleScore(ei: VectorPosition, ej: VectorPosition): number;
    protected getScaleScore(ei: VectorPosition, ej: VectorPosition): number;
    protected getPositionScore(ei: VectorPosition, ej: VectorPosition): number;
    protected getVisibilityScore(ei: VectorPosition, ej: VectorPosition): number;
    protected getEdgeVisibility(ei: VectorPosition, ej: VectorPosition): number;
    protected getEdgeForces(e: any, eidx: number, divisions: number, lambda: number): Point[];
    protected getSpringForce(divisions: any, kp: number): Point;
    protected getElectrostaticForce(pidx: number, eidx: number): Point;
    isTicking(): boolean;
    getSimulation(): any;
    destroy(): void;
}
export {};
