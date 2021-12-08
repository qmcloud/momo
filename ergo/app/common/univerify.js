import Vuex from '@/store/index.js'

export function univerifyLogin() {
	const commit = Vuex.commit;
	const PROVIDER = 'univerify';

	/**
	 * reject(true) 点击其他登录方式
	 * reject(false) 关闭登录框
	 */
	return new Promise((resolve, reject) => {
		uni.getProvider({
			service: 'oauth',
			success: (res) => {
				if (res.provider.indexOf(PROVIDER) !== -1) {
					// 一键登录已在APP onLaunch的时候进行了预登陆，可以显著提高登录速度。登录成功后，预登陆状态会重置
					uni.login({
						provider: PROVIDER,
						success: (res) => {
							uni.closeAuthView();
							uni.showLoading();

							uniCloud.callFunction({
								name: 'user-center',
								data: {
									action: 'loginByUniverify',
									params: res.authResult
								},
								success: (e) => {
									console.log('login success', e);

									if (e.result.code == 0) {
										const username = e.result.username || e.result.mobile || '一键登录新用户'

										uni.setStorageSync('uni_id_token', e.result.token)
										uni.setStorageSync('username', username)
										uni.setStorageSync('login_type', 'online')

										commit('login', username)
										resolve();

										uni.switchTab({
											url: '../main/main',
										});
									} else {
										uni.showModal({
											title: `登录失败: ${e.result.code}`,
											content: e.result.message,
											showCancel: false
										})
										console.log('登录失败', e);

										e.result.errMsg = e.result.message;
									}
								},
								fail: (e) => {
									uni.showModal({
										title: `登录失败`,
										content: e.errMsg,
										showCancel: false
									})
								},
								complete: () => {
									uni.hideLoading()
								}
							})
						},
						fail: (err) => {
							console.error('授权登录失败：' + JSON.stringify(err));

							// 一键登录点击其他登录方式
							if (err.code == 30002) {
								uni.closeAuthView();
								reject(true);
								return;
							}
							
							// 关闭登录
							if (err.code == 30003) {
								uni.closeAuthView();
								reject(false);
								return;
							}

							reject(err);
						}
					})
				} else {
					reject();
				}
			},
			fail: (err) => {
				console.error('获取服务供应商失败：' + err.errMsg);
				reject(err)
			}
		});
	})
}

export function univerifyErrorHandler(err, cb) {
	if (!err) {
		cb && cb()
		return
	};

	const state = Vuex.state;
	const obj = {
		/* showCancel: true,
		cancelText: '其他登录方式',
		success(res) {
			if (res.cancel) {
				cb && cb()
			}
		} */
	}

	switch (true) {
		// 未开通
		case err.code == 1000:
			uni.showModal(Object.assign({
				title: `登录失败`,
				content: `${err.errMsg}，错误码：${err.code}\n开通指南：https://ask.dcloud.net.cn/article/37965`,
			}, obj));
			break;
			// 预登陆失败
		case err.code == 30005:
			uni.showModal(Object.assign({
				showCancel: false,
				title: `预登录失败`,
				content: `${err.errMsg}，错误码：${err.code}`
			}, obj));
			break;
			//用户关闭验证界面
		case err.code != 30003:
			uni.showModal(Object.assign({
				showCancel: false,
				title: `登录失败`,
				content: `${err.errMsg}，错误码：${err.code}`,
			}, obj));
			break;
	}
}
