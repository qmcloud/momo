import { IGraph } from '../interface/graph';
import { INode } from '../interface/item';
declare const detectDirectedCycle: (graph: IGraph) => {
    [key: string]: INode;
};
export default detectDirectedCycle;
