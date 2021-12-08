<template>
	<div class="container">
		<div class="row" @click="go2detail(item.id)" v-for="(item,index) in list" :key="index">
			<div class="cover">
				<image class="logo-img" :src="item.cover"></image>
			</div>
			<div class="info">
				<div class="title">{{item.title}}</div>
				<div class="description">{{item.description}}</div>
				<div class="quantity">数量：{{item.quantity}}</div>
			</div>
		</div>
		<!--
		<div class="row" @click="go2detail(1)">
			<div class="cover">
				<image class="logo-img" src="../../static/img/logo.png"></image>
			</div>
			<div class="info">
				<div class="title">图书名称</div>
				<div class="description">图书简介图书简介</div>
				<div class="quantity">数量：100</div>
			</div>
		</div>
		
		<div class="row" @click="go2detail(1)">
			<div class="cover">
				<image class="logo-img" src="../../static/img/logo.png"></image>
			</div>
			<div class="info">
				<div class="title">图书名称</div>
				<div class="description">图书简介图书简介</div>
				<div class="quantity">数量：100</div>
			</div>
		</div>
		-->
	</div>
</template>

<script>
	export default {
		data() {
			return {
				avatarUrl: "../../static/img/logo.png",
				list: [],
				isHide:false,
			}
		},
		async created() {
			await this.loadData()
		},
		onHide() {
				this.isHide=true;
		},
		async onShow() {
			if(this.isHide){
				await this.loadData()
			}
		},
		methods: {
			go2detail(id) {
				uni.navigateTo({
					url: 'details?id=' + id
				})
			},
			async loadData() {
				uni.showLoading({
					title: 'loading...'
				})
				let data = {}
				data.page = 1
				data.pageSize = 10
				let url = this.globalData.apiHost + 'book/appList';
				const [error, res] = await uni.request({
					url,
					data,
					method: 'POST'
				});
				uni.hideLoading()
				// console.log('============')
				// console.log(url)
				// console.log(res.data)
				// console.log(res.data.code)
				// console.log(res.data.data.list)
				this.list = res.data.data.list
				// console.log(this.list)

			},
		}
	}
</script>

<style lang="scss">
	.container {
		width: 750upx;
		height: auto;

		.row {
			width: 730upx;
			height: 150upx;
			margin: 10upx auto;
			display: flex;
			flex-direction: row;
			border-bottom: 1px solid #C8C7CC;
			padding-bottom: 10upx;

			.cover {
				width: 150upx;
				height: 150upx;

				image {
					width: 100%;
					height: 100%;
				}
			}

			.info {
				width: 560upx;
				height: 150upx;
				margin-left: 20upx;
				font-size: 18upx;
				display: flex;
				flex-direction: column;
				line-height: 30upx;
				align-content: space-around;

				.title {
					font-size: 20upx;
					font-weight: bold;
					flex: 1;
				}

				.description {
					flex: 1;
				}

				.quantity {
					flex: 1;
				}
			}
		}
	}
</style>
