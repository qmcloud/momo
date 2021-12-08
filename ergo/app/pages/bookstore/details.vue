<template>
	<div class="container">
			<div class="cover">
				<image class="logo-img" :src="item.cover"></image>
			</div>
			<div class="info">
				<div class="title">{{item.title}}</div>
				<div class="quantity">数量：{{item.quantity}}</div>
				<div class="description">{{item.description}}</div>				
			</div>
			<div class="btn">
				<button class="primary" disabled="true" plain="true" v-if="item.quantity==0">已借完</button>
				<button class="primary" type="primary" @click="borrow" v-else-if="item.quantity>0 && canBorrow">借书</button>
				<button class="primary" disabled="true" plain="true" v-else>已借，待归还</button>
			</div>
	</div>
</template>

<script>
	export default{
		data(){
			return {
				canBorrow:false,
				token:'',
				id:0,
				item:{}
			}
		},
		async onLoad(options) {
			console.log(options)
			this.id=parseInt(options.id)
			await this.loadData()
			this.token=uni.getStorageSync('token')
			if(this.token){
				await this.checkBorrow()
			}
		},
		methods:{
			async loadData() {
				uni.showLoading({
					title: 'loading...'
				})
				let data = {}
				data.id = this.id
				let url = this.globalData.apiHost + 'book/appFind';
				const [error, res] = await uni.request({
					url,
					data,
					method: 'POST'
				});
				uni.hideLoading()
				console.log(res)
				this.item=res.data.data.item
				console.log(this.item)			
			},
			async borrow(){
				// 检查登录
				if(!this.token){
					uni.showModal({
						title:'提示',
						content:'请先登录',
						showCancel:true,
						success: function (res) {
						        if (res.confirm) {
						            console.log('用户点击确定');
									uni.navigateTo({
										url:'../login/login'
									})
						        } else if (res.cancel) {
						            console.log('用户点击取消');
						        }
						    }
					})
					return
				}
				uni.showLoading({
					title: 'loading...'
				})
				let data = {}
				data.id = this.id
				let url = this.globalData.apiHost + 'bookOrder/borrowBook';
				const [error, res] = await uni.request({
					url,
					data,
					method: 'POST',
					header: {
						'authorization': uni.getStorageSync('token')
					},
				});
				uni.hideLoading()
				console.log(res)
				uni.showToast({
					title: res.data.message
				})
				if (res.data.code == 200) {
					setTimeout(() => {
						uni.navigateBack({
				
						})
					}, 1000)
				}
			},
			// 检查是否可借
			// 存在未归还的，不能借
			async checkBorrow(){
				console.log('--------checkBorrow-------')
				let data = {}
				data.id = this.id
				let url = this.globalData.apiHost + 'bookOrder/checkBorrow';
				const [error, res] = await uni.request({
					url,
					data,
					method: 'POST',
					header: {
						'authorization': uni.getStorageSync('token')
					},
				});
				console.log(res)
				console.log(res.data.code)
				console.log(res.data.data)
				this.canBorrow=res.data.data
			}
		}
	}
</script>

<style lang="scss">
	.container{
		width: 750upx;
		height: auto;
		.cover{
			width:750upx;
			height: 150upx;
			text-align: center;
			border-bottom: 1px dashed   #C8C7CC;
			image{
				width: 150upx;
				height: 150upx;
			}
		}
		.info{
			width: 730upx;
			margin: 20upx auto;
			display: flex;
			flex-direction: column;
			font-size: 18upx;
			.title{
				font-size: 20upx;
				font-weight: bold;
			}
		}
		.btn{
			width: 700upx;
			position:fixed;			
			bottom:20upx;
			left:25upx
		}
	}
</style>
