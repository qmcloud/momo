// 导入组件，组件必须声明 name
import vueParticleLine from './src/vue-particle-line.vue'

// 为组件提供 install 安装方法，供按需引入
vueParticleLine.install = function (Vue) {
  Vue.component(vueParticleLine.name, vueParticleLine)
}

// 默认导出组件
export default vueParticleLine
