import { IGroup } from '@antv/g-base/lib/interfaces';
import ShapeBase from '@antv/g-canvas/lib/shape/base';
import { Point } from '@antv/g-canvas/lib/types';
import { IG6GraphEvent } from '../../types';
import { IGraph } from '../../interface/graph';
import { IEdge } from '../../interface/item';
interface ICustomGroup {
    nodeGroup: IGroup;
    groupStyle: {
        width: number;
        height: number;
        x: number;
        y: number;
        r: number;
        btnOffset: number;
    };
}
interface IGroupPosition {
    x: number;
    y: number;
    width: number;
    height: number;
    maxX?: number;
}
export default class CustomGroup {
    private static getDefaultCfg;
    private graph;
    private styles;
    private customGroup;
    private delegateInGroup;
    private nodePoint;
    destroyed: boolean;
    constructor(graph: IGraph);
    /**
     * 生成群组
     * @param {string} groupId 群组ID
     * @param {array} nodes 群组中的节点集合
     * @param {string} type 群组类型，默认为circle，支持rect
     * @param {number} zIndex 群组层级，默认为0
     * @param {boolean} updateDataModel 是否更新节点数据，默认为false，只有当手动创建group时才为true
     * @param {object} title 分组标题配置
     * @memberof ItemGroup
     * @return {object} null
     */
    create(groupId: string, nodes: string[], type?: string, zIndex?: number, updateDataModel?: boolean, title?: {}): void;
    /**
     * 修改Group样式
     * @param {Item} keyShape 群组的keyShape
     * @param {Object | String} style 样式
     */
    setGroupStyle(keyShape: ShapeBase, style: string | object): void;
    /**
     * 根据GroupID计算群组位置，包括左上角左边及宽度和高度
     *
     * @param {object} nodes 符合条件的node集合：选中的node或具有同一个groupID的node
     * @param {object} position delegate的坐标位置
     * @return {object} 根据节点计算出来的包围盒坐标
     * @memberof ItemGroup
     */
    calculationGroupPosition(nodes: string[], position?: Point): IGroupPosition;
    /**
    * 扁平的数据格式转成树形
    * @param {array} data 扁平结构的数据
    * @param {string} value 树状结构的唯一标识
    * @param {string} parentId 父节点的键值
    * @return {array} 转成的树形结构数据
    */
    flatToTree(data: any, value?: string, parentId?: string): any[];
    /**
     * 当group中含有group时，获取padding值
     * @param {string} groupId 节点分组ID
     * @return {number} 在x和y方向上的偏移值
     */
    getGroupPadding(groupId: string): number;
    /**
     * 设置群组对象及属性值
     *
     * @param {string} groupId 群组ID
     * @param {Group} deletage 群组元素
     * @param {object} property 属性值，里面包括width、height和maxX
     * @memberof ItemGroup
     */
    setDeletageGroupByStyle(groupId: string, deletage: IGroup, property: any): void;
    /**
     * 根据群组ID获取群组及属性对象
     *
     * @param {string} groupId 群组ID
     * @return {Item} 群组
     * @memberof ItemGroup
     */
    getDeletageGroupById(groupId: string): ICustomGroup;
    /**
     * 收起和展开群组
     * @param {string} groupId 群组ID
     */
    collapseExpandGroup(groupId: string): void;
    /**
     * 将临时节点递归地设置到groupId及父节点上
     * @param {string} groupId 群组ID
     * @param {string} tmpNodeId 临时节点ID
     */
    setGroupTmpNode(groupId: string, tmpNodeId: string): void;
    /**
     * 收起群组，隐藏群组中的节点及边，群组外部相邻的边都连接到群组上
     *
     * @param {string} id 群组ID
     * @memberof ItemGroup
     */
    collapseGroup(id: string): void;
    /**
     * 收起群组时生成临时的节点，用于连接群组外的节点
     *
     * @param {string} groupId 群组ID
     * @param {array} sourceOutTargetInEdges 出度的边
     * @param {array} sourceInTargetOutEdges 入度的边
     * @memberof ItemGroup
     */
    updateEdgeInGroupLinks(groupId: string, sourceOutTargetInEdges: IEdge[], sourceInTargetOutEdges: IEdge[]): void;
    /**
     * 展开群组，恢复群组中的节点及边
     *
     * @param {string} id 群组ID
     * @memberof ItemGroup
     */
    expandGroup(id: string): void;
    deleteTmpNode(groupId: string, tmpNodeId: string): void;
    /**
     * 删除节点分组
     * @param {string} groupId 节点分组ID
     * @memberof ItemGroup
     */
    remove(groupId: string): void;
    /**
     * 更新节点分组位置及里面的节点和边的位置
     * @param {string} groupId 节点分组ID
     * @param {object} position delegate的坐标位置
     */
    updateGroup(groupId: string, position: Point, originPosition: Point): void;
    /**
     * 更新节点分组中节点和边的位置
     * @param {string} groupId 节点分组ID
     * @param {object} position delegate的坐标位置
     */
    updateItemInGroup(groupId: string, position: Point, originPosition: Point): void;
    /**
     * 更新节点分组的 Title
     * @param {Group} group 当前 Group 实例
     * @param {string} groupId 分组ID
     * @param {number} x x坐标
     * @param {number} y y坐标
     */
    updateGroupTitle(group: IGroup, groupId: string, x: number, y: number): void;
    /**
     * 拖动节点时候动态改变节点分组大小
     * @param {Event} evt 事件句柄
     * @param {Group} currentGroup 当前操作的群组
     * @param {Item} keyShape 当前操作的keyShape
     * @description 节点拖入拖出后动态改变群组大小
     */
    dynamicChangeGroupSize(evt: IG6GraphEvent, currentGroup: IGroup, keyShape: ShapeBase): void;
    resetNodePoint(): void;
    destroy(): void;
}
export {};
