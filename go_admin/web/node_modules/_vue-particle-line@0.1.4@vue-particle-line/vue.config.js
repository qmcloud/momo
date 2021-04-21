const path = require('path');

function resolve(dir) {
  return path.join(__dirname, dir)
}

module.exports = {
  // 修改 src 目录 为 examples 目录
  baseUrl: process.env.NODE_ENV === 'production'
    ? '/vue-particle-line/'
    : '/',
  pages: {
    index: {
      entry: 'examples/main.js',
      template: 'public/index.html',
      filename: 'index.html'
    }
  },
  chainWebpack: config => {
    config.resolve.alias
      .set('packages', resolve('packages'))
      .set('common', resolve('common'))
    config.module
      .rule('js')
      .include
        .add('/packages')
        .end()
      .use('babel')
        .loader('babel-loader')
        .tap(options => {
          // 修改它的选项...
          return options
        })
  }
}
