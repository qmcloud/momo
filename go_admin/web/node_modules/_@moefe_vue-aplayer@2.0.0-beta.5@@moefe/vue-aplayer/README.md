<p align="center">
  <a href="https://aplayer.netlify.com">
    <img alt="vue-aplayer" src="https://user-images.githubusercontent.com/34600369/40580777-0dee07f0-613e-11e8-9a8b-a6f866db5986.png" width="500">
  </a>
</p>

<p align="center">
  <a href="https://www.npmjs.com/package/@moefe/vue-aplayer"><img alt="NPM version" src="https://img.shields.io/npm/v/@moefe/vue-aplayer.svg?style=for-the-badge" /></a>
  <a href="https://travis-ci.org/MoePlayer/vue-aplayer"><img alt="Build Status" src="https://img.shields.io/travis/MoePlayer/vue-aplayer/dev.svg?style=for-the-badge"></a>
  <a href="./LICENSE"><img alt="LICENSE MIT" src="https://img.shields.io/badge/license-mit-blue.svg?style=for-the-badge"></a>
  <a href="https://github.com/prettier/prettier"><img alt="Code Style: Prettier" src="https://img.shields.io/badge/code_style-prettier-ff69b4.svg?style=for-the-badge"></a>
</p>

> This is the branch for `@moefe/vue-aplayer` 2.0.

## Status: Beta

Most of the planned features are in place but there may still be bugs.

## Documentation

Docs are available at [aplayer.moefe.org/docs/](https://aplayer.moefe.org/docs/)

## Install

```bash
yarn add @moefe/vue-aplayer
```

## Usage

[![Edit vue-aplayer](https://codesandbox.io/static/img/play-codesandbox.svg)](https://codesandbox.io/s/xrylkkp27w?fontsize=12&module=%2Fsrc%2FApp.vue)

```js
import Vue from 'vue';
import APlayer from '@moefe/vue-aplayer';

Vue.use(APlayer, {
  defaultCover: 'https://github.com/u3u.png', // set the default cover
  productionTip: false, // disable console output
});
```

## Related Projects

- [APlayer](https://github.com/MoePlayer/APlayer): Original project, thanks [@DIYgod](https://github.com/DIYgod)
- [Vue-Aplayer](https://github.com/SevenOutman/vue-aplayer): Another Vue implementation of APlayer by [@SevenOutman](https://github.com/SevenOutman)

## Contributing

1.  Fork it!
2.  Create your feature branch: `git checkout -b my-new-feature`
3.  Commit your changes: `git commit -am 'Add some feature'`
4.  Push to the branch: `git push origin my-new-feature`
5.  Submit a pull request :D

## Contributors

Thanks goes to these wonderful people ([emoji key](https://github.com/kentcdodds/all-contributors#emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->

<!-- prettier-ignore -->
| [<img src="https://avatars2.githubusercontent.com/u/20062482?v=4" width="100px;"/><br /><sub><b>さくら</b></sub>](https://qwq.cat)<br />[💻](https://github.com/MoePlayer/vue-aplayer/commits?author=u3u "Code") [📖](https://github.com/MoePlayer/vue-aplayer/commits?author=u3u "Documentation") [💡](#example-u3u "Examples") | [<img src="https://avatars2.githubusercontent.com/u/8266075?v=4" width="100px;"/><br /><sub><b>DIYgod</b></sub>](https://diygod.me)<br />[🎨](#design-DIYgod "Design") [🤔](#ideas-DIYgod "Ideas, Planning, & Feedback") | [<img src="https://avatars3.githubusercontent.com/u/27483702?v=4" width="100px;"/><br /><sub><b>Rex Zeng</b></sub>](https://forkmeongithub.com/)<br />[🐛](https://github.com/MoePlayer/vue-aplayer/issues?q=author%3ARexSkz "Bug reports") | [<img src="https://avatars0.githubusercontent.com/u/34600369?v=4" width="100px;"/><br /><sub><b>Nuno Jesus</b></sub>](https://github.com/nunojesus)<br />[🎨](#design-nunojesus "Design") |
| :---: | :---: | :---: | :---: |

<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/kentcdodds/all-contributors) specification. Contributions of any kind are welcome!

## Author

**vue-aplayer** © [u3u](https://github.com/u3u), Released under the [MIT](./LICENSE) License.<br>
Authored and maintained by u3u with help from contributors ([list](https://github.com/MoePlayer/vue-aplayer/contributors)).

> [qwq.cat](https://qwq.cat) · GitHub [@u3u](https://github.com/u3u)
