import { ICombo } from '../interface/item';
import { G6Event, IG6GraphEvent, Item } from '../types';
declare const _default: {
    getDefaultCfg(): object;
    getEvents(): {
        click?: string;
        mousedown?: string;
        mouseup?: string;
        dblclick?: string;
        contextmenu?: string;
        mouseenter?: string;
        mouseout?: string;
        mouseover?: string;
        mousemove?: string;
        mouseleave?: string;
        dragstart?: string;
        dragend?: string;
        drag?: string;
        dragenter?: string;
        dragleave?: string;
        dragover?: string;
        dragout?: string;
        drop?: string;
        keyup?: string;
        keydown?: string;
        wheel?: string;
        focus?: string;
        "node:click"?: string;
        "node:contextmenu"?: string;
        "node:dblclick"?: string;
        "node:dragstart"?: string;
        "node:drag"?: string;
        "node:dragend"?: string;
        "node:mouseenter"?: string;
        "node:mouseleave"?: string;
        "node:mousemove"?: string;
        "node:drop"?: string;
        "node:dragenter"?: string;
        "node:dragleave"?: string;
        "edge:click"?: string;
        "edge:contextmenu"?: string;
        "edge:dblclick"?: string;
        "edge:mouseenter"?: string;
        "edge:mouseleave"?: string;
        "edge:mousemove"?: string;
        "canvas:mousedown"?: string;
        "canvas:mousemove"?: string;
        "canvas:mouseup"?: string;
        "canvas:click"?: string;
        "canvas:mouseleave"?: string;
        "canvas:dragstart"?: string;
        "canvas:drag"?: string;
        "canvas:dragend"?: string;
        "combo:click"?: string;
        "combo:contextmenu"?: string;
        "combo:dblclick"?: string;
        "combo:dragstart"?: string;
        "combo:drag"?: string;
        "combo:dragend"?: string;
        "combo:mouseenter"?: string;
        "combo:mouseleave"?: string;
        "combo:mousemove"?: string;
        "combo:drop"?: string;
        "combo:dragover"?: string;
        "combo:dragleave"?: string;
        "combo:dragenter"?: string;
    };
    validationCombo(item: ICombo): void;
    /**
     * 开始拖动节点
     * @param evt
     */
    onDragStart(evt: IG6GraphEvent): void;
    /**
     * 持续拖动节点
     * @param evt
     */
    onDrag(evt: IG6GraphEvent): void;
    /**
     * 拖动结束，设置拖动元素capture为true，更新元素位置，如果是拖动涉及到 combo，则更新 combo 结构
     * @param evt
     */
    onDragEnd(evt: IG6GraphEvent): void;
    /**
     * 拖动过程中将节点放置到 combo 上
     * @param evt
     */
    onDropCombo(evt: IG6GraphEvent): void;
    /**
     * 将节点拖入到 Combo 中
     * @param evt
     */
    onDragEnter(evt: IG6GraphEvent): void;
    /**
     * 将节点从 Combo 中拖出
     * @param evt
     */
    onDragLeave(evt: IG6GraphEvent): void;
    /**
     * 更新节点
     * @param item 拖动的节点实例
     * @param evt
     */
    update(item: Item, evt: IG6GraphEvent): void;
    /**
     * 更新拖动元素时的delegate
     * @param {Event} e 事件句柄
     * @param {number} x 拖动单个元素时候的x坐标
     * @param {number} y 拖动单个元素时候的y坐标
     */
    updateDelegate(e: any): void;
    /**
     * 计算delegate位置，包括左上角左边及宽度和高度
     * @memberof ItemGroup
     * @return {object} 计算出来的delegate坐标信息及宽高
     */
    calculationGroupPosition(evt: IG6GraphEvent): {
        x: number;
        y: number;
        width: number;
        height: number;
        minX: number;
        minY: number;
    };
};
export default _default;
