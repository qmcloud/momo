/**
 * @fileOverview random layout
 * @author shiwu.wyy@antfin.com
 */
import { BaseLayout } from './layout';
/**
 * 层次布局
 */
export default class DagreLayout extends BaseLayout {
    /** layout 方向, 可选 TB, BT, LR, RL */
    rankdir: 'TB' | 'BT' | 'LR' | 'RL';
    /** 节点对齐方式，可选 UL, UR, DL, DR */
    align: undefined | 'UL' | 'UR' | 'DL' | 'DR';
    /** 节点大小 */
    nodeSize: number | number[] | undefined;
    /** 节点水平间距(px) */
    nodesepFunc: ((d?: any) => number) | undefined;
    /** 每一层节点之间间距 */
    ranksepFunc: ((d?: any) => number) | undefined;
    /** 节点水平间距(px) */
    nodesep: number;
    /** 每一层节点之间间距 */
    ranksep: number;
    /** 是否保留布局连线的控制点 */
    controlPoints: boolean;
    getDefaultCfg(): {
        rankdir: string;
        align: any;
        nodeSize: any;
        nodesepFunc: any;
        ranksepFunc: any;
        nodesep: number;
        ranksep: number;
        controlPoints: boolean;
    };
    /**
     * 执行布局
     */
    execute(): void;
}
