/**
 * @fileOverview random layout
 * @author shiwu.wyy@antfin.com
 */
import { IPointTuple } from '../types';
import { BaseLayout } from './layout';
/**
 * 随机布局
 */
export default class RandomLayout extends BaseLayout {
    /** 布局中心 */
    center: IPointTuple;
    /** 宽度 */
    width: number;
    /** 高度 */
    height: number;
    getDefaultCfg(): {
        center: number[];
        width: number;
        height: number;
    };
    /**
     * 执行布局
     */
    execute(): void;
}
