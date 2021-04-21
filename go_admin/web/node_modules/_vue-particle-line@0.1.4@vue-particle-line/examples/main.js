import 'normalize.css/normalize.css'
import 'prismjs/themes/prism.css'
import 'prismjs/themes/prism-okaidia.css'

import Vue from 'vue'
import App from './App.vue'

// 导入组件库
import vueParticleLine from 'packages/index'
import 'vue-particle-line/dist/vue-particle-line.css'
// 注册组件库
Vue.use(vueParticleLine)

Vue.config.productionTip = false

new Vue({
  render: h => h(App)
}).$mount('#app')
