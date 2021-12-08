<template>
	<div class="container">
		<div class="cover">
			<image class="logo-img" :src="item.cover"></image>
		</div>
		<div class="info">
			<div class="details">
				<div class="date">
					借出:{{item.created_at}}<br />
					应还:{{item.shourld_return}}
				</div>
				<div class="status">{{item.status}}</div>
			</div>
			<div class="title">{{item.title}}</div>
			<div class="description">{{item.description}}</div>
		</div>
		<div class="btn">
			<button class="primary" type="primary" v-if="item.status!='已还'" @click="returnBook">还书</button>
			<button class="primary" disabled="true" plain="true" v-if="item.status=='已还'">已还</button>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				id: 0,
				item: {}
			}
		},
		async onLoad(options) {
			console.log(options)
			this.id = parseInt(options.id)
			await this.loadData()
		},
		methods: {
			async returnBook() {
				uni.showLoading({
					title: 'loading...'
				})
				let data = {}
				data.id = this.id
				let url = this.globalData.apiHost + 'bookOrder/returnBook';
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
			async loadData() {
				uni.showLoading({
					title: 'loading...'
				})
				let data = {}
				data.id = this.id
				let url = this.globalData.apiHost + 'bookOrder/borrowDetail';
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
				this.item = res.data.data.item
				console.log(this.item)
			},
		}
	}
</script>

<style lang="scss">
	.container {
		width: 750upx;
		height: auto;

		.cover {
			width: 750upx;
			height: 400upx;
			text-align: center;
			border-bottom: 1px dashed #C8C7CC;

			image {
				width: 400upx;
				height: 400upx;
			}
		}

		.info {
			width: 700upx;
			margin: 20upx auto;
			display: flex;
			flex-direction: column;
			font-size: 18upx;

			.title {
				font-size: 20upx;
				font-weight: bold;
			}

			.details {
				border-bottom: 1upx dashed #0FAEFF;
				padding-bottom: 20upx;
				margin-bottom: 20upx;
				display: flex;
				flex-direction: row;
				justify-content: space-between;

				.date {
					align-self: flex-end;
				}

				.status {
					align-self: flex-end;
				}
			}
		}

		.btn {
			width: 700upx;
			position: fixed;
			bottom: 20upx;
			left: 25upx
		}
	}
</style>
