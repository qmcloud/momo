import Graph from '../graph/graph';
export interface IPluginBaseConfig {
    container?: HTMLDivElement | null;
    className?: string;
    graph?: Graph;
    [key: string]: any;
}
export default abstract class PluginBase {
    private _events;
    _cfgs: IPluginBaseConfig;
    destroyed: boolean;
    /**
     * 插件基类的构造函数
     * @param cfgs 插件的配置项
     */
    constructor(cfgs?: IPluginBaseConfig);
    /**
     * 获取默认的插件配置
     */
    getDefaultCfgs(): {};
    /**
     * 初始化插件
     * @param graph Graph 实例
     */
    initPlugin(graph: Graph): void;
    /**
     * 初始化方法，供子类实现
     */
    init(): void;
    /**
     * 获取插件中的事件和事件处理方法，供子类实现
     */
    getEvents(): {};
    /**
     * 获取配置项中的某个值
     * @param key 键值
     */
    get(key: string): any;
    /**
     * 将指定的值存储到 cfgs 中
     * @param key 键值
     * @param val 设置的值
     */
    set(key: string, val: any): void;
    /**
     * 销毁方法，供子类复写
     */
    destroy(): void;
    /**
     * 销毁插件
     */
    destroyPlugin(): void;
}
