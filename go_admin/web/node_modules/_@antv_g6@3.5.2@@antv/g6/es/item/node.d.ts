import { IEdge, INode } from '../interface/item';
import { IPoint, NodeConfig } from '../types';
import Item from './item';
export default class Node extends Item implements INode {
    getNearestPoint(points: IPoint[], curPoint: IPoint): IPoint;
    getDefaultCfg(): {
        type: string;
        edges: any[];
    };
    /**
     * 获取从节点关联的所有边
     */
    getEdges(): IEdge[];
    /**
     * 获取所有的入边
     */
    getInEdges(): IEdge[];
    /**
     * 获取所有的出边
     */
    getOutEdges(): IEdge[];
    /**
     * 获取节点的邻居节点
     *
     * @returns {INode[]}
     * @memberof Node
     */
    getNeighbors(): INode[];
    /**
     * 获取以当前节点为source的邻居节点
     *
     * @returns {INode[]}
     * @memberof Node
     */
    getSourceNeighbors(): INode[];
    /**
     * 获取以当前节点为target的邻居节点
     *
     * @returns {INode[]}
     * @memberof Node
     */
    getTargetNeighbors(): INode[];
    /**
     * 根据锚点的索引获取连接点
     * @param  {Number} index 索引
     */
    getLinkPointByAnchor(index: number): IPoint;
    /**
     * 获取连接点
     * @param point
     */
    getLinkPoint(point: IPoint): IPoint | null;
    /**
     * 获取锚点的定义
     * @return {array} anchorPoints
     */
    getAnchorPoints(): IPoint[];
    /**
     * add edge
     * @param edge Edge instance
     */
    addEdge(edge: IEdge): void;
    /**
     * 锁定节点
     */
    lock(): void;
    /**
     * 解锁锁定的节点
     */
    unlock(): void;
    hasLocked(): boolean;
    /**
     * 移除边
     * @param {Edge} edge 边
     */
    removeEdge(edge: IEdge): void;
    clearCache(): void;
    /**
     * 是否仅仅移动节点，其他属性没变化
     * @param cfg 节点数据模型
     */
    isOnlyMove(cfg: NodeConfig): boolean;
}
