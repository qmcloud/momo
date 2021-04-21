/// <reference types="node" />
interface DetectedInfo<N extends string, O, V = null> {
    readonly name: N;
    readonly version: V;
    readonly os: O;
}
export declare class BrowserInfo implements DetectedInfo<Browser, OperatingSystem | null, string> {
    readonly name: Browser;
    readonly version: string;
    readonly os: OperatingSystem | null;
    constructor(name: Browser, version: string, os: OperatingSystem | null);
}
export declare class NodeInfo implements DetectedInfo<'node', NodeJS.Platform, string> {
    readonly version: string;
    readonly name: 'node';
    readonly os: NodeJS.Platform;
    constructor(version: string);
}
export declare class BotInfo implements DetectedInfo<'bot', null, null> {
    readonly bot: true;
    readonly name: 'bot';
    readonly version: null;
    readonly os: null;
}
export declare type Browser = 'aol' | 'edge' | 'edge-ios' | 'yandexbrowser' | 'vivaldi' | 'kakaotalk' | 'samsung' | 'silk' | 'miui' | 'beaker' | 'edge-chromium' | 'chrome' | 'chromium-webview' | 'phantomjs' | 'crios' | 'firefox' | 'fxios' | 'opera-mini' | 'opera' | 'ie' | 'bb10' | 'android' | 'ios' | 'safari' | 'facebook' | 'instagram' | 'ios-webview' | 'searchbot';
export declare type OperatingSystem = 'iOS' | 'Android OS' | 'BlackBerry OS' | 'Windows Mobile' | 'Amazon OS' | 'Windows 3.11' | 'Windows 95' | 'Windows 98' | 'Windows 2000' | 'Windows XP' | 'Windows Server 2003' | 'Windows Vista' | 'Windows 7' | 'Windows 8' | 'Windows 8.1' | 'Windows 10' | 'Windows ME' | 'Open BSD' | 'Sun OS' | 'Linux' | 'Mac OS' | 'QNX' | 'BeOS' | 'OS/2' | 'Chrome OS' | 'Search Bot';
export declare function detect(userAgent?: string): BrowserInfo | BotInfo | NodeInfo | null;
export declare function parseUserAgent(ua: string): BrowserInfo | BotInfo | null;
export declare function detectOS(ua: string): OperatingSystem | null;
export declare function getNodeVersion(): NodeInfo | null;
export {};
