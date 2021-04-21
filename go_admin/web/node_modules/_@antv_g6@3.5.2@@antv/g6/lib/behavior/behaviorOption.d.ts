import { IGraph } from '../interface/graph';
declare const _default: {
    getDefaultCfg(): {};
    /**
     * register event handler, behavior will auto bind events
     * for example:
     * return {
     *  click: 'onClick'
     * }
     */
    getEvents(): {};
    shouldBegin(): boolean;
    shouldUpdate(): boolean;
    shouldEnd(): boolean;
    /**
     * auto bind events when register behavior
     * @param graph Graph instance
     */
    bind(graph: IGraph): void;
    unbind(graph: IGraph): void;
    get(val: string): any;
    set(key: string, val: any): any;
};
export default _default;
