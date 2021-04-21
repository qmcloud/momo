/**
 * @fileOverview layout base file
 * @author shiwu.wyy@antfin.com
 */
import { EdgeConfig, GraphData, IPointTuple, NodeConfig, ComboConfig } from '../types';
import { ILayout } from '../interface/layout';
declare type LayoutOption<Cfg = any> = Partial<ILayout<Cfg>>;
declare type LayoutConstructor<Cfg = any> = new () => BaseLayout<Cfg>;
/**
 * 基础布局，将被自定义布局所继承
 */
export declare class BaseLayout<Cfg = any> implements ILayout<Cfg> {
    nodes: NodeConfig[] | null;
    edges: EdgeConfig[] | null;
    combos: ComboConfig[] | null;
    positions: IPointTuple[] | null;
    destroyed: boolean;
    init(data: GraphData): void;
    execute(): void;
    layout(data: GraphData): void;
    getDefaultCfg(): {};
    updateCfg(cfg: Partial<Cfg>): void;
    destroy(): void;
}
declare const Layout: {
    [layoutType: string]: any;
    registerLayout<Cfg>(type: string, layout: LayoutOption<Cfg>, layoutCons?: LayoutConstructor<Cfg>): void;
};
export default Layout;
