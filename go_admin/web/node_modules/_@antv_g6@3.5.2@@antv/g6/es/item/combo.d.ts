import { ICombo, INode } from '../interface/item';
import Node from './node';
import { ComboConfig, IBBox } from '../types';
export default class Combo extends Node implements ICombo {
    getDefaultCfg(): {
        type: string;
        nodes: any[];
        edges: any[];
        combos: any[];
    };
    getShapeCfg(model: ComboConfig): ComboConfig;
    /**
     * 根据 keyshape 计算包围盒
     */
    calculateCanvasBBox(): IBBox;
    /**
     * 获取 Combo 中所有的子元素，包括 Combo、Node 及 Edge
     */
    getChildren(): {
        nodes: INode[];
        combos: ICombo[];
    };
    /**
     * 获取 Combo 中所有子节点
     */
    getNodes(): INode[];
    /**
     * 获取 Combo 中所有子 combo
     */
    getCombos(): ICombo[];
    /**
     * 向 Combo 中增加子 combo 或 node
     * @param item Combo 或节点实例
     * @return boolean 添加成功返回 true，否则返回 false
     */
    addChild(item: ICombo | INode): boolean;
    /**
     * 向 Combo 中增加 combo
     * @param combo Combo 实例
     * @return boolean 添加成功返回 true，否则返回 false
     */
    addCombo(combo: ICombo): boolean;
    /**
     * 向 Combo 中添加节点
     * @param node 节点实例
     * @return boolean 添加成功返回 true，否则返回 false
     */
    addNode(node: string | INode): boolean;
    /**
     * 向 Combo 中增加子 combo 或 node
     * @param item Combo 或节点实例
     * @return boolean 添加成功返回 true，否则返回 false
     */
    removeChild(item: ICombo | INode): boolean;
    /**
     * 从 Combo 中移除指定的 combo
     * @param combo Combo 实例
     * @return boolean 移除成功返回 true，否则返回 false
     */
    removeCombo(combo: ICombo): boolean;
    /**
    * 向 Combo 中移除指定的节点
    * @param node 节点实例
    * @return boolean 移除成功返回 true，否则返回 false
    */
    removeNode(node: INode): boolean;
    isOnlyMove(cfg?: any): boolean;
    /**
     * 获取 item 的包围盒，这个包围盒是相对于 item 自己，不会将 matrix 计算在内
     * @return {Object} 包含 x,y,width,height, centerX, centerY
     */
    getBBox(): IBBox;
    clearCache(): void;
    destroy(): void;
}
