import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		/**
		 * 是否需要强制登录
		 */
		forcedLogin: false,
		hasLogin: false,
		userName: "",
		univerifyErrorMsg: "",
		hideUniverify: true
	},
	mutations: {
		login(state, userName) {
			state.userName = userName || '新用户';
			state.hasLogin = true;
		},
		logout(state) {
			state.userName = "";
			state.hasLogin = false;
		},
		setUniverifyErrorMsg(state, payload = '') {
			state.univerifyErrorMsg = payload
		},
		setHideUniverify(state, payload = false) {
			state.hideUniverify = payload
		}
	}
})

export default store
