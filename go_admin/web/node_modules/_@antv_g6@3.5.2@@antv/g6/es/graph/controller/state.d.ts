import { Item } from '../../types';
import Graph from '../graph';
export default class StateController {
    private graph;
    private cachedStates;
    destroyed: boolean;
    constructor(graph: Graph);
    /**
     * 检查 cache 的可用性
     *
     * @private
     * @param {Item} item
     * @param {string} state
     * @param {object} cache
     * @returns
     * @memberof State
     */
    private static checkCache;
    /**
     * 缓存 state
     *
     * @private
     * @param {Item} item Item 实例
     * @param {string} state 状态名称
     * @param {object} states
     * @memberof State
     */
    private static cacheState;
    /**
     * 更新 Item 的状态
     *
     * @param {Item} item Item实例
     * @param {string} state 状态名称
     * @param {boolean} enabled 状态是否可用
     * @memberof State
     */
    updateState(item: Item, state: string, enabled: boolean): void;
    /**
     * 批量更新 states，兼容 updateState，支持更新一个 state
     *
     * @param {Item} item
     * @param {(string | string[])} states
     * @param {boolean} enabled
     * @memberof State
     */
    updateStates(item: Item, states: string | string[], enabled: boolean): void;
    /**
     * 更新 states
     *
     * @memberof State
     */
    updateGraphStates(): void;
    destroy(): void;
}
