import { ITreeGraph } from '../interface/graph';
import { GraphData, Item, ShapeStyle, TreeGraphData, GraphOptions } from '../types';
import Graph, { PrivateGraphOption } from './graph';
export default class TreeGraph extends Graph implements ITreeGraph {
    private layoutAnimating;
    constructor(cfg: GraphOptions);
    /**
     * 通过 Layout 配置获取布局配置
     */
    private getLayout;
    /**
     * 返回指定节点在树图数据中的索引
     * @param children 树图数据
     * @param child 树图中某一个 Item 的数据
     */
    private static indexOfChild;
    getDefaultCfg(): Partial<PrivateGraphOption>;
    /**
     * 向🌲树中添加数据
     * @param treeData 树图数据
     * @param parent 父节点实例
     * @param animate 是否开启动画
     */
    private innerAddChild;
    /**
     * 将数据上的变更转换到视图上
     * @param data
     * @param parent
     * @param animate
     */
    private innerUpdateChild;
    /**
     * 删除子节点Item对象
     * @param id
     * @param to
     * @param animate
     */
    private innerRemoveChild;
    /**
     * 更新数据模型，差量更新并重新渲染
     * @param {object} data 数据模型
     */
    changeData(data?: GraphData | TreeGraphData): any;
    /**
     * 已更名为 updateLayout，为保持兼容暂且保留。
     * 更改并应用树布局算法
     * @param {object} layout 布局算法
     */
    changeLayout(layout: any): void;
    /**
     * 更改并应用树布局算法
     * @param {object} layout 布局算法
     */
    updateLayout(layout: any): void;
    /**
     * 已更名为 layout，为保持兼容暂且保留。
     * 根据目前的 data 刷新布局，更新到画布上。用于变更数据之后刷新视图。
     * @param {boolean} fitView 更新布局时是否需要适应窗口
     */
    refreshLayout(fitView?: boolean): void;
    /**
     * 根据目前的 data 刷新布局，更新到画布上。用于变更数据之后刷新视图。
     * @param {boolean} fitView 更新布局时是否需要适应窗口
     */
    layout(fitView?: boolean): void;
    /**
     * 添加子树到对应 id 的节点
     * @param {TreeGraphData} data 子树数据模型
     * @param {string} parent 子树的父节点id
     */
    addChild(data: TreeGraphData, parent: string | Item): void;
    /**
     * 更新源数据，差量更新子树
     * @param {TreeGraphData} data 子树数据模型
     * @param {string} parent 子树的父节点id
     */
    updateChild(data: TreeGraphData, parent?: string): void;
    /**
     * 删除子树
     * @param {string} id 子树根节点id
     */
    removeChild(id: string): void;
    /**
     * 根据id获取对应的源数据
     * @param {string} id 元素id
     * @param {TreeGraphData | undefined} parent 从哪个节点开始寻找，为空时从根节点开始查找
     * @return {TreeGraphData} 对应源数据
     */
    findDataById(id: string, parent?: TreeGraphData | undefined): TreeGraphData | null;
    /**
     * 布局动画接口，用于数据更新时做节点位置更新的动画
     * @param {TreeGraphData} data 更新的数据
     * @param {function} onFrame 定义节点位置更新时如何移动
     */
    layoutAnimate(data: TreeGraphData, onFrame?: (item: Item, ratio: number, originAttrs?: ShapeStyle, data?: TreeGraphData) => unknown): void;
    /**
     * 立即停止布局动画
     */
    stopLayoutAnimate(): void;
    /**
     * 是否在布局动画
     * @return {boolean} 是否有布局动画
     */
    isLayoutAnimating(): boolean;
    /**
     * 根据data接口的数据渲染视图
     */
    render(): void;
    /**
     * 导出图数据
     * @return {object} data
     */
    save(): TreeGraphData | GraphData;
}
