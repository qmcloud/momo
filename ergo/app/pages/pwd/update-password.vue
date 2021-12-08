<template>
	<view style="padding: 20px;flex: 1;">
		<view class="uni-header">
			<view class="uni-group">
				<view class="uni-title">修改密码</view>
			</view>
		</view>
		<uni-forms ref="form" validateTrigger="bind" :rules="rules" @submit="submit">
			<uni-forms-item label="旧密码" name="oldPassword" labelWidth="85">
				<input class="uni-input-border" type="password" placeholder="旧密码" @blur="binddata('oldPassword',$event.detail.value)" />
			</uni-forms-item>

			<uni-forms-item label="新密码" name="newPassword" labelWidth="85">
				<input class="uni-input-border" :password="showPassword" placeholder="新密码" @blur="binddata('newPassword',$event.detail.value)" />
				<text class="uni-icon-password-eye pointer" :class="[!showPassword ? 'uni-eye-active' : '']" @click="changePassword">&#xe568;</text>
			</uni-forms-item>

			<uni-forms-item label="确认新密码" name="passwordConfirmation" labelWidth="85" :errorMessage="errorMessage">
				<input @confirm="confirmForm('passwordConfirmation',$event.detail.value)" class="uni-input-border" :password="showPasswordAgain"
				 placeholder="确认新密码" @blur="binddata('passwordConfirmation',$event.detail.value)" />
				<text class="uni-icon-password-eye pointer" :class="[!showPasswordAgain ? 'uni-eye-active' : '']" @click="changePasswordAgain">&#xe568;</text>
			</uni-forms-item>
			<view class="uni-button-group pointer">
				<button class="uni-button uni-button-full" type="primary" @click="submitForm">保存</button>
			</view>
		</uni-forms>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				showPassword: true,
				showPasswordAgain: true,
				errorMessage: '',
				password: {
					oldPassword: '',
					newPassword: '',
					passwordConfirmation: ''
				},
				rules: {
					oldPassword: {
						rules: [{
							required: true,
							errorMessage: '请输入旧密码'
						}]
					},
					newPassword: {
						rules: [{
								required: true,
								errorMessage: '请输入新密码'
							},
							{
								minLength: 6,
								errorMessage: '密码长度最小{minLength}个字符'
							}
						]
					},
					passwordConfirmation: {
						rules: [{
								required: true,
								errorMessage: '请确认新密码'
							},
							{
								minLength: 6,
								errorMessage: '密码长度最小{minLength}个字符'
							}
						]
					}
				}
			}
		},
		props: {
			hasBackButton: {
				type: Boolean,
				default: false
			},
			isPhone: {
				type: Boolean,
				default: true
			}
		},
		computed: {
			...mapState('user', ['userInfo'])
		},
		methods: {
			...mapMutations(['logout']),
			submit(event) {
				const {
					errors,
					value
				} = event.detail
				if (errors) return
				if (value.newPassword !== value.passwordConfirmation) {
					this.errorMessage = '两次输入密码不相同'
					return
				}
				this.save(value)
			},
			confirmForm(name, value) {
				this.binddata(name, value)
				this.submitForm()
			},
			submitForm() {
				this.errorMessage = ''
				this.$refs.form.submit()
			},
			async save(formData) {
				let that = this
				uni.showLoading()
				console.log(formData)
				let data={}
				data.password=formData.newPassword
				data.oldPassword=formData.oldPassword
				console.log(data)
				let url = this.globalData.apiHost + 'reader/updatePwd';
				const [error, res] = await uni.request({
					url,
					data,
					method: 'PUT',
					header: {
						'authorization': uni.getStorageSync('token')
					},
				});
				uni.hideLoading()
				console.log(res)
				console.log(res.data)
				console.log(res.data.code)
				if(res.data.code==200){
					this.logout()
					uni.setStorageSync('token','')
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
				// uniCloud.callFunction({
				// 	name: 'user-center',
				// 	data: {
				// 		action: 'updatePwd',
				// 		params: {
				// 			...formData
				// 		}
				// 	},
				// 	success: (res) => {
				// 		uni.hideLoading()
				// 		if (res.result.code === 0) {
				// 			uni.showModal({
				// 				title: '提示',
				// 				content: res.result.msg,
				// 				showCancel: false,
				// 				success: (res) => {
				// 					if (res.confirm) {
				// 						that.logout();
				// 						uni.removeStorageSync('uni_id_token')
				// 						uni.removeStorageSync('username')
				// 						uni.reLaunch({
				// 							url: '/pages/login/login'
				// 						})
				// 					}
				// 				}
				// 			});
				// 		} else {
				// 			uni.showToast({
				// 				title: res.result.msg,
				// 				icon: 'none',
				// 				duration: 2000
				// 			})
				// 		}
				// 	},
				// 	fail: (e) => {
				// 		uni.hideLoading()
				// 		uni.showModal({
				// 			content: '修改密码失败',
				// 			showCancel: false
				// 		})
				// 	}
				// })
			},
			changePassword: function() {
				this.showPassword = !this.showPassword;
			},
			changePasswordAgain: function() {
				this.showPasswordAgain = !this.showPasswordAgain;
			}
		}
	}
</script>


<style>
	/* 标题栏 */
	.uni-header {
		padding: 0 15px;
		display: flex;
		height: 55px;
		align-items: center;
		justify-content: space-between;
		border-bottom: 1px #f5f5f5 solid;
	}

	.uni-title {
		margin-right: 10px;
		font-size: 16px;
		font-weight: 500;
		color: #333;
	}

	.uni-group {
		display: flex;
		align-items: center;
		justify-content: center;
		word-break: keep-all;
	}

	/* 容器 */
	.uni-container {
		padding: 15px;
		box-sizing: border-box;
	}

	/* 按钮样式 */
	.uni-button-group {
		margin-top: 30px;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.pointer {
		cursor: pointer;
	}

	.uni-input-border,
	.uni-textarea-border {
		width: 100%;
		font-size: 14px;
		color: #666;
		border: 1px #e5e5e5 solid;
		border-radius: 5px;
		box-sizing: border-box;
	}

	.uni-input-border {
		padding: 0 10px;
		height: 35px;

	}

	.uni-icon-password-eye {
		position: absolute;
		right: 8px;
		top: 6px;
		font-family: uniicons;
		font-size: 20px;
		font-weight: normal;
		font-style: normal;
		width: 24px;
		height: 24px;
		line-height: 24px;
		color: #999999;
	}

	.uni-eye-active {
		color: #007AFF;
	}

	.uni-button {
		padding: 10px 20px;
		font-size: 14px;
		border-radius: 4px;
		line-height: 1;
		margin: 0;
		box-sizing: border-box;
		overflow: initial;
	}


	.uni-button-full {
		width: 100%;
	}
</style>
