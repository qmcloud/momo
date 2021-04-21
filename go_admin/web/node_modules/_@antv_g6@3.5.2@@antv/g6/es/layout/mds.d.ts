/**
 * @fileOverview MDS layout
 * @author shiwu.wyy@antfin.com
 */
import { IPointTuple, Matrix } from '../types';
import { BaseLayout } from './layout';
/**
 * mds 布局
 */
export default class MDSLayout extends BaseLayout {
    /** 布局中心 */
    center: IPointTuple;
    /** 边长度 */
    linkDistance: number;
    private scaledDistances;
    getDefaultCfg(): {
        center: number[];
        linkDistance: number;
    };
    /**
     * 执行布局
     */
    execute(): void;
    /**
     * mds 算法
     * @return {array} positions 计算后的节点位置数组
     */
    runMDS(): IPointTuple[];
    handleInfinity(distances: Matrix[]): void;
}
