import { IElement } from '../interfaces';
import { Region } from '../types';
export declare function applyAttrsToContext(context: CanvasRenderingContext2D, element: IElement): void;
export declare function drawChildren(context: CanvasRenderingContext2D, children: IElement[], region?: Region): void;
export declare function drawPath(shape: any, context: any, attrs: any, arcParamsCache: any): void;
export declare function refreshElement(element: any, changeType: any): void;
export declare function getRefreshRegion(element: any): any;
export declare function getMergedRegion(elements: any): Region;
export declare function mergeView(region: any, viewRegion: any): {
    minX: number;
    minY: number;
    maxX: number;
    maxY: number;
};
