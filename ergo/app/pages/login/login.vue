<template>
	<view class="content">
		<view class="input-group">
			<view class="input-row border">
				<text class="title">用户名：</text>
				<m-input class="m-input" type="text" clearable focus v-model="username" placeholder="请输入用户名"></m-input>
			</view>
			<view class="input-row border">
				<text class="title">密码：</text>
				<m-input type="password" displayable v-model="password" placeholder="请输入密码"></m-input>
			</view>
		</view>
		<view class="btn-row">
			<button type="primary" class="primary" :loading="loginBtnLoading" @tap="loginByPwd">登录</button>
		</view>
		<div class="ext-row">
			<view class="action-row">
				<navigator url="../reg/reg">注册账号</navigator>
			</view>
			<view class="action-row">
				<navigator url="../pwd/pwd">忘记密码</navigator>
			</view>
		</div>		
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from 'vuex'
	import mInput from '../../components/m-input.vue'
	import {
		univerifyLogin,
		univerifyErrorHandler
	} from '@/common/univerify.js'
	import {
		getDeviceUUID
	} from '@/common/utils.js'

	let weixinAuthService;
	const captchaOptions = {
		deviceId: getDeviceUUID(),
		scene: 'login'
	}

	export default {
		components: {
			mInput
		},
		data() {
			return {
				platform: uni.getSystemInfoSync().platform,
				loginType: 0,
				loginTypeList: ['免密登录', '密码登录'],
				mobile: '',
				code: '',
				providerList: [],
				hasProvider: false,
				username: '',
				password: '',
				positionTop: 0,
				isDevtools: false,
				codeDuration: 0,
				loginBtnLoading: false,
				hasAppleLogin: false,
				needCaptcha: uni.getStorageSync('uni-needCaptcha'),
				captchaing: false,
				captchaBase64: '',
				captchaText: ''
			}
		},
		computed: mapState(['forcedLogin', 'hasLogin', 'univerifyErrorMsg', 'hideUniverify']),
		onLoad() {
			
		},
		methods: {
			...mapMutations(['login']),
			sendSmsCode() {
				if (this.codeDuration) {
					uni.showModal({
						content: `请在${this.codeDuration}秒后重试`,
						showCancel: false
					})
				}
				if (!/^1\d{10}$/.test(this.mobile)) {
					uni.showModal({
						content: '手机号码填写错误',
						showCancel: false
					})
					return
				}
				uniCloud.callFunction({
					name: 'user-center',
					data: {
						action: 'sendSmsCode',
						params: {
							mobile: this.mobile,
							type: 'login'
						}
					},
					success: (e) => {
						if (e.result.code == 0) {
							uni.showModal({
								content: '验证码发送成功，请注意查收',
								showCancel: false
							})
							this.codeDuration = 60
							this.codeInterVal = setInterval(() => {
								this.codeDuration--
								if (this.codeDuration === 0) {
									if (this.codeInterVal) {
										clearInterval(this.codeInterVal)
										this.codeInterVal = null
									}
								}
							}, 1000)
						} else {
							uni.showModal({
								content: '验证码发送失败：' + e.result.msg,
								showCancel: false
							})
						}

					},
					fail(e) {
						uni.showModal({
							content: '验证码发送失败',
							showCancel: false
						})
					}
				})
			},
			async loginByPwd() {
				/**
				 * 客户端对账号信息进行一些必要的校验。
				 * 实际开发中，根据业务需要进行处理，这里仅做示例。
				 */
				if (this.username.length < 3) {
					uni.showToast({
						icon: 'none',
						title: '账号最短为 3 个字符'
					});
					return;
				}
				if (this.password.length < 6) {
					uni.showToast({
						icon: 'none',
						title: '密码最短为 6 个字符'
					});
					return;
				}
				const data = {
					username: this.username,
					password: this.password
				};
				this.loginBtnLoading = true
				let url = this.globalData.apiHost + 'reader/login';
				const [error, res] = await uni.request({
					url,
					data,
					method: 'POST'
				});
				uni.hideLoading()
				// console.log(res)
				// console.log(res.data)
				// console.log(res.data.code)
				// console.log(res.data.data.token)
				if(res.data.code==200){
					uni.setStorageSync('token', res.data.data.token)
					uni.setStorageSync('username', res.data.data.username)
					this.login(res.data.data.username);
					uni.showToast({
						title:res.data.message
					})
					setTimeout(()=>{
						uni.reLaunch({
						url:'../bookstore/list'	
						})
					},1000)
				}else{
					uni.showModal({
						content:res.data.message,
						showCancel:false
					})
				}
				this.loginBtnLoading = false
				
			},
		}
	}
</script>

<style>
	.login-type {
		display: flex;
		justify-content: center;
	}

	.login-type-btn {
		line-height: 30px;
		margin: 0px 15px;
	}

	.login-type-btn.act {
		color: #0FAEFF;
		border-bottom: solid 1px #0FAEFF;
	}

	.send-code-btn {
		width: 120px;
		text-align: center;
		background-color: #0FAEFF;
		color: #FFFFFF;
	}

	.action-row {
		display: flex;
		flex-direction: row;
		justify-content: center;
	}

	.action-row navigator {
		color: #007aff;
		padding: 0 10px;
	}

	.oauth-row {
		display: flex;
		flex-direction: row;
		align-items: center;
		justify-content: space-around;
		flex-wrap: wrap;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
	}

	.oauth-image {
		position: relative;
		width: 50px;
		height: 50px;
		border: 1px solid #dddddd;
		border-radius: 50px;
		background-color: #ffffff;
	}

	.oauth-image image {
		width: 30px;
		height: 30px;
		margin: 10px;
	}

	.oauth-image button {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		opacity: 0;
	}

	.captcha-view {
		line-height: 0;
		justify-content: center;
		align-items: center;
		display: flex;
		position: relative;
		background-color: #f3f3f3;
	}
	.ext-row{
		display: flex;
		justify-content: space-between;
	}
</style>
