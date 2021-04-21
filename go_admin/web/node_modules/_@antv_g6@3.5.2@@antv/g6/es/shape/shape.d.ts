/**
 * @fileOverview 自定义 Shape 的基类
 * @author dxq613@gmail.com
 */
import { ShapeOptions } from '../interface/shape';
export default class Shape {
    static Node: any;
    static Edge: any;
    static Combo: any;
    static registerFactory(factoryType: string, cfg: object): object;
    static getFactory(factoryType: string): any;
    static registerNode(shapeType: string, nodeDefinition: ShapeOptions, extendShapeType?: string): any;
    static registerEdge(shapeType: string, edgeDefinition: ShapeOptions, extendShapeType?: string): any;
    static registerCombo(shapeType: string, comboDefinition: ShapeOptions, extendShapeType?: string): any;
}
