import { IAlgorithmCallbacks } from '../types';
import { IGraph } from '../interface/graph';
/**
 * 深度优先遍历图
 * @param data GraphData 图数据
 * @param startNodeId 开始遍历的节点的 ID
 * @param originalCallbacks 回调
 */
export default function depthFirstSearch(graph: IGraph, startNodeId: string, callbacks?: IAlgorithmCallbacks): void;
