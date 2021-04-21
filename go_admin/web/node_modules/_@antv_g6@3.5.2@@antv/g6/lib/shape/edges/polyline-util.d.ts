import { IShapeBase } from '../../types';
interface PolyPoint {
    x: number;
    y: number;
    id?: string;
}
declare type PBBox = Partial<{
    x: number;
    y: number;
    minX: number;
    minY: number;
    maxX: number;
    maxY: number;
    height: number;
    width: number;
    centerX: number;
    centerY: number;
}>;
export declare const getBBoxFromPoint: (point: PolyPoint) => PBBox;
export declare const getBBoxFromPoints: (points?: PolyPoint[]) => PBBox;
export declare const isBBoxesOverlapping: (b1: PBBox, b2: PBBox) => boolean;
export declare const filterConnectPoints: (points: PolyPoint[]) => PolyPoint[];
export declare const simplifyPolyline: (points: PolyPoint[]) => PolyPoint[];
export declare const getSimplePolyline: (sPoint: PolyPoint, tPoint: PolyPoint) => PolyPoint[];
export declare const getExpandedBBox: (bbox: any, offset: number) => PBBox;
export declare const isHorizontalPort: (port: PolyPoint, bbox: PBBox) => boolean;
export declare const getExpandedBBoxPoint: (bbox: any, point: PolyPoint) => PolyPoint;
/**
 *
 * @param b1
 * @param b2
 */
export declare const mergeBBox: (b1: PBBox, b2: PBBox) => PBBox;
export declare const getPointsFromBBox: (bbox: PBBox) => PolyPoint[];
export declare const isPointOutsideBBox: (point: PolyPoint, bbox: PBBox) => boolean;
export declare const getBBoxXCrossPoints: (bbox: PBBox, x: number) => PolyPoint[];
export declare const getBBoxYCrossPoints: (bbox: PBBox, y: number) => PolyPoint[];
export declare const getBBoxCrossPointsByPoint: (bbox: PBBox, point: PolyPoint) => PolyPoint[];
export declare const distance: (p1: PolyPoint, p2: PolyPoint) => number;
export declare const _costByPoints: (p: PolyPoint, points: PolyPoint[]) => number;
export declare const heuristicCostEstimate: (p: PolyPoint, ps: PolyPoint, pt: PolyPoint, source?: PolyPoint, target?: PolyPoint) => number;
export declare const reconstructPath: (pathPoints: PolyPoint[], pointById: any, cameFrom: any, currentId: string, iterator?: number) => void;
export declare const removeFrom: (arr: PolyPoint[], item: PolyPoint) => void;
export declare const isSegmentsIntersected: (p0: PolyPoint, p1: PolyPoint, p2: PolyPoint, p3: PolyPoint) => boolean;
export declare const isSegmentCrossingBBox: (p1: PolyPoint, p2: PolyPoint, bbox: PBBox) => boolean;
export declare const getNeighborPoints: (points: PolyPoint[], point: PolyPoint, bbox1: PBBox, bbox2: PBBox) => PolyPoint[];
export declare const pathFinder: (points: PolyPoint[], start: PolyPoint, goal: any, sBBox: PBBox, tBBox: PBBox, os: any, ot: any) => PolyPoint[];
export declare const isBending: (p0: PolyPoint, p1: PolyPoint, p2: PolyPoint) => boolean;
export declare const getBorderRadiusPoints: (p0: PolyPoint, p1: PolyPoint, p2: PolyPoint, r: number) => PolyPoint[];
export declare const getPathWithBorderRadiusByPolyline: (points: PolyPoint[], borderRadius: number) => string;
export declare const getPolylinePoints: (start: PolyPoint, end: PolyPoint, sNode: IShapeBase, tNode: IShapeBase, offset: number) => PolyPoint[];
export {};
