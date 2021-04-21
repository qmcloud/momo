import GraphEvent from '@antv/g-base/lib/event/graph-event';
import Canvas from '@antv/g-canvas/lib/canvas';
import { G6Event, IG6GraphEvent, Item } from '../types';
import { IGraph } from './graph';
export interface IBehavior {
    getEvents(): {
        [key in G6Event]?: string;
    };
    getDefaultCfg?(): object;
    shouldBegin?(e?: IG6GraphEvent): boolean;
    shouldUpdate?(e?: IG6GraphEvent): boolean;
    shouldEnd?(e?: IG6GraphEvent): boolean;
    bind?(e: IGraph): void;
    unbind?(e: IGraph): void;
}
export declare class G6GraphEvent extends GraphEvent implements IG6GraphEvent {
    item: Item;
    canvasX: number;
    canvasY: number;
    wheelDelta: number;
    detail: number;
    target: Item & Canvas;
    constructor(type: string, event: IG6GraphEvent);
}
