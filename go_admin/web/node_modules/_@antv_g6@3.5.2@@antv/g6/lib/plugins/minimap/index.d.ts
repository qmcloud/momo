import GCanvas from '@antv/g-canvas/lib/canvas';
import Base, { IPluginBaseConfig } from '../base';
import { ShapeStyle } from '../../types';
interface MiniMapConfig extends IPluginBaseConfig {
    viewportClassName?: string;
    type?: 'default' | 'keyShape' | 'delegate';
    size?: number[];
    delegateStyle?: ShapeStyle;
    refresh?: boolean;
    padding?: number;
}
export default class MiniMap extends Base {
    constructor(cfg?: MiniMapConfig);
    getDefaultCfgs(): MiniMapConfig;
    getEvents(): {
        beforepaint: string;
        beforeanimate: string;
        afteranimate: string;
        viewportchange: string;
    };
    protected disableRefresh(): void;
    protected enableRefresh(): void;
    protected disableOneRefresh(): void;
    private initViewport;
    /**
     * 更新 viewport 视图
     */
    private updateViewport;
    /**
     * 将主图上的图形完全复制到小图
     */
    private updateGraphShapes;
    private updateKeyShapes;
    /**
     * 增加/更新单个元素的 keyShape
     * @param item INode 实例
     */
    private updateOneNodeKeyShape;
    /**
     * Minimap 中展示自定义的rect，支持用户自定义样式和节点大小
     */
    private updateDelegateShapes;
    private clearDestroyedShapes;
    /**
     * 设置只显示 edge 的 keyShape
     * @param item IEdge 实例
     */
    private updateOneEdgeKeyShape;
    /**
     * Minimap 中展示自定义的 rect，支持用户自定义样式和节点大小
     * 增加/更新单个元素
     * @param item INode 实例
     */
    private updateOneNodeDelegateShape;
    /**
     * 主图更新的监听函数，使用 debounce 减少渲染频率
     * e.g. 拖拽节点只会在松手后的 100ms 后执行 updateCanvas
     * e.g. render 时大量 addItem 也只会执行一次 updateCanvas
     */
    private handleUpdateCanvas;
    init(): void;
    /**
     * 初始化 Minimap 的容器
     */
    initContainer(): void;
    updateCanvas(): void;
    /**
     * 获取minimap的画布
     * @return {GCanvas} G的canvas实例
     */
    getCanvas(): GCanvas;
    /**
     * 获取minimap的窗口
     * @return {HTMLElement} 窗口的dom实例
     */
    getViewport(): HTMLElement;
    /**
     * 获取minimap的容器dom
     * @return {HTMLElement} dom
     */
    getContainer(): HTMLElement;
    destroy(): void;
}
export {};
