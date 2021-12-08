<template>
	<view class="">
		<view class="center">
			<view class="logo" @click="bindLogin" :hover-class="!hasLogin ? 'logo-hover' : ''">
				<image class="logo-img" :src="avatarUrl"></image>
				<view class="logo-title">
					<text class="uer-name">Hi，{{hasLogin ? userName : '您未登录'}}</text>
					<text class="go-login navigat-arrow" v-if="!hasLogin">&#xe65e;</text>
				</view>
			</view>
			<view class="center-list">
				<view class="center-list-item border-bottom" @click="updatePwd">
					<text class="list-icon">&#xe60f;</text>
					<text class="list-text">修改密码</text>
					<text class="navigat-arrow">&#xe65e;</text>
				</view>
				<view class="center-list-item" @click="borrowList">
					<text class="list-icon">&#xe639;</text>
					<text class="list-text">借阅列表</text>
					<text class="navigat-arrow">&#xe65e;</text>
				</view>
			</view>
			<view class="btn-row" v-if="token.length>0">
				<button class="primary" type="primary" :loading="logoutBtnLoading" @tap="bindLogout">退出登录</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from 'vuex'
	import {
		univerifyLogin
	} from '@/common/univerify.js'

	export default {
		data() {
			return {
				token:'',
				username:'',
				avatarUrl: "../../static/img/logo.png",
				inviteUrl: '',
				logoutBtnLoading: false,
				hasPwd: uni.getStorageSync('uni_id_has_pwd')
			}
		},
		computed: {
			...mapState(['hasLogin', 'forcedLogin', 'userName'])
		},
		created(){
			this.token=uni.getStorageSync('token')
			//this.username=uni.getStorageSync('username')
			//this.hasLogin=true;
		},
		methods: {
			...mapMutations(['logout']),
			bindLogin() {
				if (!this.hasLogin) {
					univerifyLogin().catch(err => {
						if (err === false) return;
						
						uni.navigateTo({
							url: '../login/login',
						});
					})
				}
			},
			bindLogout() {
				this.logout();
				uni.setStorageSync('token','')
				uni.setStorageSync('username','')
				this.logoutBtnLoading = true
				setTimeout(()=>{
					uni.reLaunch({
						url:'../bookstore/list'
					})
					this.logoutBtnLoading = false
				},1000)
			},
			toInvite() {
				uni.navigateTo({
					url: '/pages/invite/invite'
				})
			},
			updatePwd(){
				if(!this.token){
					uni.showModal({
						title:'提示',
						content:'请先登录',
						showCancel:false
					})
					return
				}
				uni.navigateTo({
					url:'../pwd/update-password'
				})
			},
			borrowList(){
				if(!this.token){
					uni.showModal({
						title:'提示',
						content:'请先登录',
						showCancel:false
					})
					return
				}
				uni.navigateTo({
					url: '../borrow/list',
				});
			},
		}
	}
</script>

<style>
	@font-face {
		font-family: texticons;
		font-weight: normal;
		font-style: normal;
		src: url('https://at.alicdn.com/t/font_984210_5cs13ndgqsn.ttf') format('truetype');
	}

	page,
	view {
		display: flex;
	}

	page {
		background-color: #f8f8f8;
	}

	button {
		width: 100%;
	}

	.center {
		flex-direction: column;
	}

	.logo {
		width: 750rpx;
		height: 240rpx;
		padding: 20rpx;
		box-sizing: border-box;
		background-color: #0faeff;
		flex-direction: row;
		align-items: center;
	}

	.logo-hover {
		opacity: 0.8;
	}

	.logo-img {
		width: 120rpx;
		height: 120rpx;
		border-radius: 150rpx;
	}

	.logo-title {
		height: 150rpx;
		flex: 1;
		align-items: center;
		justify-content: space-between;
		flex-direction: row;
		margin-left: 20rpx;
	}

	.uer-name {
		height: 60rpx;
		line-height: 60rpx;
		color: #FFFFFF;
	}

	.go-login.navigat-arrow {
		color: #FFFFFF;
	}

	.login-title {
		height: 150rpx;
		align-items: self-start;
		justify-content: center;
		flex-direction: column;
		margin-left: 20rpx;
	}

	.center-list {
		background-color: #FFFFFF;
		margin-top: 20rpx;
		width: 750rpx;
		flex-direction: column;
	}

	.center-list-item {
		height: 90rpx;
		width: 750rpx;
		box-sizing: border-box;
		flex-direction: row;
		padding: 0rpx 20rpx;
	}

	.border-bottom {
		border-bottom-width: 1rpx;
		border-color: #c8c7cc;
		border-bottom-style: solid;
	}

	.list-icon {
		width: 40rpx;
		height: 90rpx;
		line-height: 90rpx;
		color: #0faeff;
		text-align: center;
		font-family: texticons;
		margin-right: 20rpx;
	}

	.list-text {
		height: 90rpx;
		line-height: 90rpx;
		color: #555;
		flex: 1;
		text-align: left;
	}

	.navigat-arrow {
		height: 90rpx;
		width: 40rpx;
		line-height: 90rpx;
		color: #555;
		text-align: right;
		font-family: texticons;
	}
</style>
