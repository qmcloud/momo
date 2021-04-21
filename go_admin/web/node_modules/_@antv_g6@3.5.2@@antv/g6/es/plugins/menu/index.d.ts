import { IG6GraphEvent } from '../../types';
import Base, { IPluginBaseConfig } from '../base';
interface MenuConfig extends IPluginBaseConfig {
    createDOM?: boolean;
    menu?: HTMLDivElement;
    getContent?: (evt?: IG6GraphEvent) => string;
    onShow: (evt?: IG6GraphEvent) => boolean;
    onHide: (evt?: IG6GraphEvent) => boolean;
}
export default class Menu extends Base {
    constructor(cfg: MenuConfig);
    getDefaultCfgs(): MenuConfig;
    getEvents(): {
        contextmenu: string;
    };
    init(): void;
    protected onMenuShow(e: IG6GraphEvent): void;
    private onMenuHide;
    destroy(): void;
}
export {};
