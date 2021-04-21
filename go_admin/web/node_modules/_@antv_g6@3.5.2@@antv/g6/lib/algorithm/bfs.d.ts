import { IAlgorithmCallbacks } from '../types';
import { IGraph } from '../interface/graph';
/**
 * 广度优先遍历图
 * @param graph Graph 图实例
 * @param startNode 开始遍历的节点
 * @param originalCallbacks 回调
 */
declare const breadthFirstSearch: (graph: IGraph, startNodeId: string, originalCallbacks?: IAlgorithmCallbacks) => void;
export default breadthFirstSearch;
