import Vue from 'vue'
import App from './App'

import store from './store'

Vue.config.productionTip = false

Vue.prototype.$store = store

App.mpType = 'app'

const app = new Vue({
	store,
	...App
})
Vue.prototype.globalData = {
	apiHost: 'http://192.168.1.2:8080/api/',
	// apiHost: 'https://ogreenphoto.xiaopeng.info/',
}
app.$mount()
