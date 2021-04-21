import { VNode } from 'vue';

declare global {
  namespace APlayer {
    export interface InstallOptions {
      defaultCover?: string;
      productionTip?: boolean;
    }

    export type LoopMode = 'all' | 'one' | 'none';
    export type OrderMode = 'list' | 'random';
    export type Preload = 'none' | 'metadata' | 'auto';
    export type AudioType = 'auto' | 'hls' | 'normal';

    export enum LrcType {
      file = 3,
      html = 2, // not support
      string = 1,
      disabled = 0,
    }

    export interface Audio {
      [index: number]: this;

      id?: number;
      name: string | VNode; // eslint-disable-line no-restricted-globals
      artist: string | VNode;
      url: string;
      cover?: string;
      lrc?: string;
      theme?: string;
      type?: AudioType;
      speed?: number;
    }

    export interface Options {
      fixed?: boolean;
      mini?: boolean;
      autoplay?: boolean;
      theme?: string;
      loop?: LoopMode;
      order?: OrderMode;
      preload?: Preload;
      volume?: number;
      audio: Audio | Audio[];
      customAudioType?: any;
      mutex?: boolean;
      lrcType?: LrcType;
      listFolded?: boolean;
      listMaxHeight?: number;
      storageName?: string;
    }

    export interface Events {
      onAbort: Event;
      onCanplay: Event;
      onCanplaythrough: Event;
      onDurationchange: Event;
      onEmptied: Event;
      onEnded: Event;
      onError: Event;
      onLoadeddata: Event;
      onLoadedmetadata: Event;
      onLoadstart: Event;
      onPause: Event;
      onPlay: Event;
      onPlaying: Event;
      onProgress: Event;
      onRatechange: Event;
      onReadystatechange: Event;
      onSeeked: Event;
      onSeeking: Event;
      onStalled: Event;
      onSuspend: Event;
      onTimeupdate: Event;
      onVolumechange: Event;
      onWaiting: Event;

      onListSwitch: Audio;
      onListShow: void;
      onListHide: void;
      onListAdd: void;
      onListRemove: void;
      onListClear: void;
      onNoticeShow: void;
      onNoticeHide: void;
      onLrcShow: void;
      onLrcHide: void;
      onDestroy: void;
    }

    export interface Settings {
      currentTime: number;
      duration: number | null;
      paused: boolean;
      mini: boolean;
      lrc: boolean;
      list: boolean;
      volume: number;
      loop: LoopMode;
      order: OrderMode;
      music: Audio | null;
    }

    export interface Media {
      /** 返回表示可用音频轨道的 AudioTrackList 对象。 */
      readonly audioTracks: AudioTrackList;
      /** 设置或返回是否在就绪（加载完成）后随即播放音频。 */
      readonly autoplay: boolean;
      /** 返回表示音频已缓冲部分的 TimeRanges 对象。 */
      readonly buffered: TimeRanges;
      /** 设置或返回音频是否应该显示控件（比如播放/暂停等）。 */
      readonly controls: boolean;
      /** 设置或返回音频的 CORS 设置。 */
      readonly crossOrigin: string | null;
      /** 返回当前音频的 URL。 */
      readonly currentSrc: string;
      /** 设置或返回音频中的当前播放位置（以秒计）。 */
      readonly currentTime: number;
      /** 设置或返回音频默认是否静音。 */
      readonly defaultMuted: boolean;
      /** 设置或返回音频的默认播放速度。 */
      readonly defaultPlaybackRate: number;
      /** 返回音频的长度（以秒计）。 */
      readonly duration: number;
      /** 返回音频的播放是否已结束。 */
      readonly ended: boolean;
      /** 返回表示音频错误状态的 MediaError 对象。 */
      readonly error: MediaError | null;
      /** 设置或返回音频是否应在结束时再次播放。 */
      readonly loop: boolean;
      /** 设置或返回音频所属媒介组合的名称。 */
      readonly mediaKeys: MediaKeys | null;
      /** 设置或返回是否关闭声音。 */
      readonly muted: boolean;
      /** 返回音频的当前网络状态。 */
      readonly networkState: number;
      /** 设置或返回音频是否暂停。 */
      readonly paused: boolean;
      /** 设置或返回音频播放的速度。 */
      readonly playbackRate: number;
      /** 返回表示音频已播放部分的 TimeRanges 对象。 */
      readonly played: TimeRanges;
      /** 设置或返回音频的 preload 属性的值。 */
      readonly preload: string;
      /** 返回音频当前的就绪状态。 */
      readonly readyState: number;
      /** 返回表示音频可寻址部分的 TimeRanges 对象。 */
      readonly seekable: TimeRanges;
      /** 返回用户当前是否正在音频中进行查找。 */
      readonly seeking: boolean;
      /** 设置或返回音频的 src 属性的值。 */
      readonly src: string;
      /** 返回表示可用文本轨道的 TextTrackList 对象。 */
      readonly textTracks: TextTrackList;
      /** 设置或返回音频的音量。 */
      readonly volume: number;
    }
  }
}
