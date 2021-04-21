import { G6Event, IG6GraphEvent, Item } from '../types';
import { ICombo } from '../interface/item';
declare const _default: {
    getDefaultCfg(): {
        enableDelegate: boolean;
        delegateStyle: {};
        onlyChangeComboSize: boolean;
        activeState: string;
        selectedState: string;
    };
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
    validationCombo(evt: IG6GraphEvent): void;
    onDragStart(evt: IG6GraphEvent): void;
    onDrag(evt: IG6GraphEvent): void;
    onDrop(evt: IG6GraphEvent): void;
    onDragEnter(evt: IG6GraphEvent): void;
    onDragLeave(evt: any): void;
    onDragEnd(evt: IG6GraphEvent): void;
    /**
     * 遍历 comboTree，分别更新 node 和 combo
     * @param data
     * @param fn
     */
    traverse<T extends Item>(data: T, fn: (param: T) => boolean): void;
    updateCombo(item: ICombo, evt: IG6GraphEvent): void;
    /**
     *
     * @param item 当前正在拖动的元素
     * @param evt
     */
    updateSignleItem(item: Item, evt: IG6GraphEvent): void;
    /**
     * 根据 ID 获取父 Combo
     * @param parentId 父 Combo ID
     */
    getParentCombo(parentId: string): ICombo | undefined;
    updateDelegate(evt: IG6GraphEvent): void;
};
export default _default;
