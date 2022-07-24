<template>
	<view>
		
		<!-- 加载中 -->
		<view v-if="showLoading" class="position-fixed top-0 bottom-0 left-0 right-0 bg-light flex align-center justify-center" style="z-index: 100;">
			<text class="text-muted font">加载中...</text>
		</view>
		
		<view class="flex align-center justify-center" style="height: 350rpx;">
			<text style="font-size: 50rpx;">YOU-LOGO</text>
		</view>
		<view class="px-3">
			<input type="text" v-model="form.username" class="bg-light px-3 mb-3 font" placeholder="请输入用户名" style="height: 100rpx;"/>
			<input type="text" v-model="form.password" class="bg-light px-3 mb-3 font" placeholder="请输入密码" style="height: 100rpx;"/>
			<input v-if="type != 'login'" type="text" v-model="form.repassword" class="bg-light px-3 mb-3 font" placeholder="请输入确认密码" style="height: 100rpx;"/>
		</view>
		
		<view class="p-3 flex align-center justify-center">
			<view class="bg-main rounded p-3 flex align-center justify-center flex-1 " hover-class="bg-main-hover" @click="submit">
				<text class="text-white font-md">{{ type === 'login'?'登 录' : '注 册' }}</text>
			</view>
		</view>
		
		<view class="flex align-center justify-center">
			<text class="text-light-muted font p-2" @click="changeType">{{ type === 'login'?'注册账号' : '去登录' }}</text>
		</view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				type:"login",
				form:{
					username:"",
					password:"",
					repassword:""
				},
				showLoading:true
			}
		},
		created() {
			let token = uni.getStorageSync('token')
			if(!token){
				// 用户未登录
				uni.showToast({
					title: '请先登录',
					icon: 'none'
				});
				return this.showLoading = false
			}
			// 用户已登录
			uni.switchTab({
				url:"../index/index"
			})
		},
		methods: {
			changeType(){
				this.type = this.type === 'login' ? 'reg' : 'login'
			},
			submit(){
				let msg = this.type === 'login' ? '登录' : '注册'
				this.$H.post('/'+ this.type,this.form).then(res=>{
					uni.showToast({
						title: msg + '成功',
						icon: 'none'
					});
					if(this.type === 'reg'){
						this.changeType()
						this.form = {
							username:"",
							password:"",
							repassword:""
						}
					} else {
						this.$store.dispatch('login',res)
						uni.switchTab({
							url:"../index/index"
						})
					}
				})
			}
		}
	}
</script>

<style>

</style>
