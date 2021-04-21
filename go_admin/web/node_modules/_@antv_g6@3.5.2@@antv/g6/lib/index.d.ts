import Behaviors from './behavior';
import Graph from './graph/graph';
import TreeGraph from './graph/tree-graph';
import Shape from './shape';
import Layout from './layout';
import Global from './global';
import Util from './util';
import * as Algorithm from './algorithm';
declare const registerNode: typeof Shape.registerNode;
declare const registerEdge: typeof Shape.registerEdge;
declare const registerCombo: typeof Shape.registerCombo;
declare const registerBehavior: typeof Behaviors.registerBehavior;
declare const registerLayout: <Cfg>(type: string, layout: Partial<import("./interface/layout").ILayout<Cfg>>, layoutCons?: new () => import("./layout/layout").BaseLayout<Cfg>) => void;
declare const Minimap: typeof import("./plugins/minimap").default;
declare const Grid: typeof import("./plugins/grid").default;
declare const Bundling: typeof import("./plugins/bundling").default;
declare const Menu: typeof import("./plugins/menu").default;
export { registerNode, registerCombo, Graph, TreeGraph, Util, registerEdge, Layout, Global, registerLayout, Minimap, Grid, Bundling, Menu, registerBehavior, Algorithm };
declare const _default: {
    version: string;
    Graph: typeof Graph;
    TreeGraph: typeof TreeGraph;
    Util: any;
    registerNode: typeof Shape.registerNode;
    registerEdge: typeof Shape.registerEdge;
    registerCombo: typeof Shape.registerCombo;
    registerBehavior: typeof Behaviors.registerBehavior;
    registerLayout: <Cfg>(type: string, layout: Partial<import("./interface/layout").ILayout<Cfg>>, layoutCons?: new () => import("./layout/layout").BaseLayout<Cfg>) => void;
    Layout: {
        [layoutType: string]: any;
        registerLayout<Cfg>(type: string, layout: Partial<import("./interface/layout").ILayout<Cfg>>, layoutCons?: new () => import("./layout/layout").BaseLayout<Cfg>): void;
    };
    Global: {
        version: string;
        rootContainerClassName: string;
        nodeContainerClassName: string;
        edgeContainerClassName: string;
        comboContainerClassName: string;
        customGroupContainerClassName: string;
        delegateContainerClassName: string;
        defaultShapeFillColor: string;
        defaultShapeStrokeColor: string;
        defaultLoopPosition: string;
        nodeLabel: {
            style: {
                fill: string;
                textAlign: string;
                textBaseline: string;
            };
            offset: number;
        };
        defaultNode: {
            type: string;
            style: {
                fill: string;
                lineWidth: number;
                stroke: string;
            };
            size: number;
            color: string;
        };
        edgeLabel: {
            style: {
                fill: string;
                textAlign: string;
                textBaseline: string;
            };
        };
        defaultEdge: {
            type: string;
            style: {
                stroke: string;
            };
            size: number;
            color: string;
        };
        comboLabel: {
            style: {
                fill: string;
                textBaseline: string;
            };
            refY: number;
            refX: number;
        };
        defaultCombo: {
            type: string;
            style: {
                fill: string;
                lineWidth: number;
                stroke: string;
                opacity: number;
                r: number;
                width: number;
                height: number;
            };
            size: number[];
            color: string;
            padding: number[];
        };
        nodeStateStyle: {};
        delegateStyle: {
            fill: string;
            fillOpacity: number;
            stroke: string;
            strokeOpacity: number;
            lineDash: number[];
        };
    };
    Minimap: typeof import("./plugins/minimap").default;
    Grid: typeof import("./plugins/grid").default;
    Bundling: typeof import("./plugins/bundling").default;
    Menu: typeof import("./plugins/menu").default;
    Algorithm: typeof Algorithm;
};
export default _default;
