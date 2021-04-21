import Vue from 'vue';
import Comopnent from 'vue-class-component';
import APlayerPlugin, { APlayer } from './'; // eslint-disable-line

Vue.use<APlayer.InstallOptions>(APlayerPlugin, {
  defaultCover: '',
  productionTip: true,
});

console.log(APlayer.version);

@Comopnent
export default class App extends Vue {
  readonly $refs!: {
    aplayer: APlayer;
  };

  async created() {
    const { aplayer } = this.$refs;
    console.log(aplayer.$refs.container);
    console.log(aplayer.media.currentTime);
    console.log(aplayer.media.duration);
    console.log(aplayer.media.paused);
    console.log(aplayer.currentMusic);
    console.log(aplayer.currentSettings);
    await aplayer.play();
    aplayer.toggle();
    aplayer.pause();
    aplayer.seek(0);
    aplayer.switch(0);
    aplayer.switch('');
    aplayer.skipBack();
    aplayer.skipForward();
    aplayer.showLrc();
    aplayer.hideList();
    aplayer.toggleLrc();
    aplayer.showList();
    aplayer.hideList();
    aplayer.toggleList();
    aplayer.showNotice('');
    aplayer.showNotice('', 1e3);
    aplayer.showNotice('', 1e3, 0.8);
  }

  render() {
    return (
      <div id="app">
        <APlayer
          ref="aplayer"
          mutex
          fixed
          mini
          autoplay
          theme="#b7daff"
          loop="all"
          order="random"
          preload="metadata"
          volume={0.75}
          audio={{
            name: (
              <span data-name="光るなら">
                <span class="aplayer-badge">[HOT]</span> 光るなら
              </span>
            ),
            artist: 'Goose house',
            url: 'https://moeplayer.b0.upaiyun.com/aplayer/hikarunara.mp3',
            cover: 'https://moeplayer.b0.upaiyun.com/aplayer/hikarunara.jpg',
            lrc: 'https://moeplayer.b0.upaiyun.com/aplayer/hikarunara.lrc',
            theme: '#ebd0c2',
          }}
          lrcType={2}
          listFolded={false}
          listMaxHeight={250}
          storageName="aplayer-setting"
          onAbort={() => {}}
          onCanplay={() => {}}
          onCanplaythrough={() => {}}
          onDurationchange={() => {}}
          onEmptied={() => {}}
          onEnded={() => {}}
          onError={() => {}}
          onLoadeddata={() => {}}
          onLoadedmetadata={() => {}}
          onLoadstart={() => {}}
          onPause={() => {}}
          onPlay={() => {}}
          onPlaying={() => {}}
          onProgress={() => {}}
          onRatechange={() => {}}
          onReadystatechange={() => {}}
          onSeeked={() => {}}
          onSeeking={() => {}}
          onStalled={() => {}}
          onSuspend={() => {}}
          onTimeupdate={() => {}}
          onVolumechange={() => {}}
          onWaiting={() => {}}
          onListSwitch={() => {}}
          onListShow={() => {}}
          onListHide={() => {}}
          onListAdd={() => {}}
          onListRemove={() => {}}
          onListClear={() => {}}
          onNoticeShow={() => {}}
          onNoticeHide={() => {}}
          onLrcShow={() => {}}
          onLrcHide={() => {}}
          onDestroy={() => {}}
        />
      </div>
    );
  }
}
