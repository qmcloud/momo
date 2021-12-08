<template>
	<view class="content">
		<view class="input-group">
			<view class="input-row border">
				<text class="title">用户名：</text>
				<m-input type="text" focus clearable v-model="username" placeholder="请输入用户名"></m-input>
			</view>
			<view class="input-row border">
				<text class="title">邮箱：</text>
				<m-input type="text" focus clearable v-model="email" placeholder="请输入账号"></m-input>
			</view>
			<view class="input-row border">
				<text class="title">验证码：</text>
				<m-input type="text" v-model="verificationCode" placeholder="请输入验证码"></m-input>
				<view class="send-code-btn" @click="sendSmsCode">{{codeDuration ? codeDuration + 's' : '发送验证码' }}</view>
			</view>
			<view class="input-row border">
				<text class="title">密码：</text>
				<m-input type="password" displayable v-model="password" placeholder="请输入密码"></m-input>
			</view>
			<view class="input-row">
				<text class="title">确认密码：</text>
				<m-input type="password" displayable v-model="confirmPassword" placeholder="请确认密码"></m-input>
			</view>
		</view>
		<view class="btn-row">
			<button type="primary" class="primary" @tap="register">注册</button>
		</view>
	</view>
</template>

<script>
	import mInput from '../../components/m-input.vue';

	export default {
		components: {
			mInput
		},
		data() {
			return {
				username: '',
				email: '',
				password: '',
				confirmPassword: '',
				verificationCode: '',

				codeDuration: 0,
			}
		},
		methods: {
			async register() {
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
				if (this.email.length < 1) {
					uni.showToast({
						icon: 'none',
						title: '请输入邮箱'
					});
					return;
				}
				if (this.verificationCode.length < 1) {
					uni.showToast({
						icon: 'none',
						title: '请输入验证码'
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
				if (this.password !== this.confirmPassword) {
					uni.showToast({
						icon: 'none',
						title: '两次密码输入不一致'
					});
					return;
				}

				const data = {
					username: this.username,
					password: this.password,
					email: this.email,
					verificationCode: this.verificationCode
				}
				uni.showLoading({
					title: 'loading...'
				})
				let url = this.globalData.apiHost + 'reader/register';
				const [error, res] = await uni.request({
					url,
					data,
					method: 'POST'
				});
				uni.hideLoading()
				console.log(res)
				console.log(res.data)
				console.log(res.data.code)
				if (res.data.code == 200) {
					uni.showToast({
						title: '注册成功'
					})
					setTimeout(() => {
						uni.redirectTo({
							url: '../login/login'
						})
					}, 1000)
				} else {
					uni.showModal({
						content: res.data.message,
						showCancel: false
					})
				}
				// uniCloud.callFunction({
				// 	name: 'user-center',
				// 	data: {
				// 		action: 'register',
				// 		params: data
				// 	},
				// 	success(e) {
				// 		console.log("注册", e);

				// 		if (e.result.code === 0) {
				// 			uni.showToast({
				// 				title: '注册成功'
				// 			});
				// 			uni.setStorageSync('uni_id_token', e.result.token)
				// 			uni.setStorageSync('username', e.result.username)
				// 			uni.reLaunch({
				// 				url: '../main/main',
				// 			});
				// 		} else {
				// 			uni.showModal({
				// 				content: JSON.stringify(e.result),
				// 				showCancel: false
				// 			})
				// 		}
				// 	},
				// 	fail(e) {
				// 		uni.showModal({
				// 			content: JSON.stringify(e),
				// 			showCancel: false
				// 		})
				// 	}
				// })
			},
			async sendSmsCode() {
				console.log('sendSmsCode')
				if (this.email.length < 1) {
					uni.showToast({
						icon: 'none',
						title: '请输入邮箱'
					});
					return;
				}
				
				// 倒计时
				if (this.codeDuration) {
					uni.showModal({
						content: `请在${this.codeDuration}秒后重试`,
						showCancel: false
					})
					return
				}
				const data = {
					account: this.email,
					type: 1,
				}
				uni.showLoading({
					title: 'loading...'
				})
				let url = this.globalData.apiHost + 'reader/sendVerifyCode';
				const [error, res] = await uni.request({
					url,
					data,
					method: 'POST'
				});
				uni.hideLoading()
				console.log(res)
				console.log(res.data)
				console.log(res.data.code)
				if (res.data.code == 200) {
					uni.showToast({
						title: '发送成功'
					})
					// 倒计时begin
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
					// 倒计时end
				} else {
					uni.showModal({
						content: res.data.message,
						showCancel: false
					})
				}
			},
		}
	}
</script>

<style>
	.send-code-btn {
		width: 120px;
		text-align: center;
		background-color: #0FAEFF;
		color: #FFFFFF;
	}
</style>
