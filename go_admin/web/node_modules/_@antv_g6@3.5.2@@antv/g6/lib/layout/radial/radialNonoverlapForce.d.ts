import { Matrix, IPointTuple } from '../../types';
export declare type RadialNonoverlapForceParam = {
    positions: IPointTuple[];
    adjMatrix: Matrix[];
    focusID: number;
    radii: number[];
    iterations?: number;
    height?: number;
    width?: number;
    speed?: number;
    gravity?: number;
    nodeSizeFunc: (node: any) => number;
    k: number;
    strictRadial: boolean;
    nodes: any[];
};
export default class RadialNonoverlapForce {
    /** node positions */
    positions: IPointTuple[];
    /** adjacency matrix */
    adjMatrix: Matrix[];
    /** focus node */
    focusID: number;
    /** radii */
    radii: number[];
    /** the number of iterations */
    iterations: number;
    /** the height of the canvas */
    height: number;
    /** the width of the canvas */
    width: number;
    /** the moving speed */
    speed: number;
    /** the gravity */
    gravity: number;
    /** the node size */
    nodeSizeFunc: (node: any) => number;
    /** the strength of forces */
    k: number;
    /** if each circle can be separated into subcircles to avoid overlappings */
    strictRadial: boolean;
    /** the nodes data */
    nodes: any[];
    private maxDisplace;
    private disp;
    constructor(params: RadialNonoverlapForceParam);
    layout(): IPointTuple[];
    private getRepulsion;
    private updatePositions;
}
