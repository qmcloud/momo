import { IGraph } from '../../interface/graph';
export default class LayoutController {
    graph: IGraph;
    destroyed: boolean;
    private layoutCfg;
    private layoutType;
    private layoutMethod;
    private worker;
    private workerData;
    private data;
    constructor(graph: IGraph);
    private initLayout;
    private getWorker;
    private stopWorker;
    getLayoutType(): string;
    /**
     * @param {function} success callback
     * @return {boolean} 是否使用web worker布局
     */
    layout(success?: () => void): boolean;
    /**
     * layout with web worker
     * @param {object} data graph data
     * @param {function} success callback function
     * @return {boolean} 是否支持web worker
     */
    private layoutWithWorker;
    private handleWorkerMessage;
    refreshLayout(): void;
    updateLayoutCfg(cfg: any): void;
    changeLayout(layoutType: string): void;
    changeData(): void;
    setDataFromGraph(): any;
    relayout(reloadData?: boolean): boolean;
    layoutAnimate(): void;
    moveToZero(): void;
    initPositions(center: any, nodes: any): boolean;
    destroy(): void;
}
