import { EdgeConfig, Item, ITEM_TYPE, ModelConfig, NodeConfig, ComboTree, ComboConfig } from '../../types';
import Graph from '../graph';
import { ICombo } from '../../interface/item';
export default class ItemController {
    private graph;
    destroyed: boolean;
    constructor(graph: Graph);
    /**
     * 增加 Item 实例
     *
     * @param {ITEM_TYPE} type 实例类型，node 或 edge
     * @param {(NodeConfig & EdgeConfig)} model 数据模型
     * @returns {(Item)}
     * @memberof ItemController
     */
    addItem<T extends Item>(type: ITEM_TYPE, model: ModelConfig): T;
    /**
     * 更新节点或边
     *
     * @param {Item} item ID 或 实例
     * @param {(EdgeConfig | Partial<NodeConfig>)} cfg 数据模型
     * @returns
     * @memberof ItemController
     */
    updateItem(item: Item | string, cfg: EdgeConfig | Partial<NodeConfig>): void;
    /**
     * 根据 combo 的子元素更新 combo 的位置及大小
     *
     * @param {ICombo} combo ID 或 实例
     * @returns
     * @memberof ItemController
     */
    updateCombo(combo: ICombo | string, children: ComboTree[]): void;
    /**
     * 收起 combo，隐藏相关元素
     */
    collapseCombo(combo: ICombo | string): void;
    /**
     * 展开 combo，相关元素出现
     * 若子 combo 原先是收起状态，则保持它的收起状态
     */
    expandCombo(combo: ICombo | string): void;
    /**
     * 删除指定的节点或边
     *
     * @param {Item} item item ID 或实例
     * @returns {void}
     * @memberof ItemController
     */
    removeItem(item: Item | string): void;
    /**
     * 更新 item 状态
     *
     * @param {Item} item Item 实例
     * @param {string} state 状态名称
     * @param {boolean} value 是否启用状态或状态值
     * @returns {void}
     * @memberof ItemController
     */
    setItemState(item: Item, state: string, value: string | boolean): void;
    /**
     * 清除所有指定的状态
     *
     * @param {Item} item Item 实例
     * @param {string[]} states 状态名称集合
     * @memberof ItemController
     */
    clearItemStates(item: Item | string, states?: string | string[]): void;
    /**
     * 刷新指定的 Item
     *
     * @param {Item} item Item ID 或 实例
     * @memberof ItemController
     */
    refreshItem(item: Item | string): void;
    /**
     * 根据 graph 上用 combos 数据生成的 comboTree 来增加所有 combos
     *
     * @param {ComboTree[]} comboTrees graph 上用 combos 数据生成的 comboTree
     * @param {ComboConfig[]} comboModels combos 数据
     * @memberof ItemController
     */
    addCombos(comboTrees: ComboTree[], comboModels: ComboConfig[]): void;
    /**
     * 改变Item的显示状态
     *
     * @param {Item} item Item ID 或 实例
     * @param {boolean} visible 是否显示
     * @memberof ItemController
     */
    changeItemVisibility(item: Item | string, visible: boolean): void;
    destroy(): void;
}
