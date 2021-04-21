import { IPointTuple, Matrix } from '../../types';
export default class MDS {
    /** distance matrix */
    distances: Matrix[];
    /** dimensions */
    dimension: number;
    /** link distance */
    linkDistance: number;
    constructor(params: {
        distances: Matrix[];
        dimension?: number;
        linkDistance: number;
    });
    layout(): IPointTuple[];
}
