import { ModeType, Modes } from '../../types';
import Graph from '../graph';
export default class ModeController {
    private graph;
    destroyed: boolean;
    /**
     * modes = {
     *  default: [ 'drag-node', 'zoom-canvas' ],
     *  edit: [ 'drag-canvas', {
     *    type: 'brush-select',
     *    trigger: 'ctrl'
     *  }]
     * }
     *
     * @private
     * @type {Modes}
     * @memberof Mode
     */
    modes: Modes;
    /**
     * mode = 'drag-node'
     *
     * @private
     * @type {string}
     * @memberof Mode
     */
    mode: string;
    private currentBehaves;
    constructor(graph: Graph);
    private formatModes;
    private setBehaviors;
    private static mergeBehaviors;
    private static filterBehaviors;
    setMode(mode: string): void;
    getMode(): string;
    /**
     * 动态增加或删除 Behavior
     *
     * @param {ModeType[]} behaviors
     * @param {(ModeType[] | ModeType)} modes
     * @param {boolean} isAdd
     * @returns {Mode}
     * @memberof Mode
     */
    manipulateBehaviors(behaviors: ModeType[] | ModeType, modes: string[] | string, isAdd: boolean): ModeController;
    destroy(): void;
}
