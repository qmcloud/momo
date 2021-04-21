// TypeScript Version: 2.8
import _Vue from 'vue';
import * as Vue from 'vue-tsx-support';

export class APlayer extends Vue.Component<APlayer.Options, APlayer.Events> {
  static readonly version: string;

  readonly $refs: {
    container: HTMLDivElement;
  };

  readonly currentMusic: APlayer.Audio;

  readonly currentSettings: APlayer.Settings;

  readonly media: APlayer.Media;

  play(): Promise<void>;

  pause(): void;

  toggle(): void;

  seek(time: number): void;

  switch(audio: number | string): void;

  skipBack(): void;

  skipForward(): void;

  showLrc(): void;

  hideLrc(): void;

  toggleLrc(): void;

  showList(): void;

  hideList(): void;

  toggleList(): void;

  showNotice(text: string, time?: number, opacity?: number): void;
}

export default function install(
  Vue: typeof _Vue,
  options?: APlayer.InstallOptions
): void;
