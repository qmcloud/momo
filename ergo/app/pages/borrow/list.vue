<template>
	<div class="container">
		<div class="row" @click="go2detail(item.id)" v-for="(item,index) in list" :key="index">
			<div class="cover">
				<image class="logo-img" :src="item.cover"></image>
			</div>
			<div class="info">
				<div class="title">{{item.title}}</div>
				<div class="description">{{item.description}}</div>
				<div class="details">
					<div class="date">
						借出:{{item.created_at}}<br/>
						应还:{{item.shourld_return}}
					</div>
					<div class="status" :class="{'status-active':item.status=='待还'}">{{item.status}}</div>
				</div>
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
				<div class="details">
					<div class="date">
						借出:2020-12-12<br/>
						应还:2020-12-12
					</div>
					<div class="status">已还</div>
				</div>
			</div>
		</div>
		
		<div class="row" @click="go2detail(1)">
			<div class="cover">
				<image class="logo-img" src="../../static/img/logo.png"></image>
			</div>
			<div class="info">
				<div class="title">图书名称</div>
				<div class="description">图书简介图书简介</div>
				<div class="details">
					<div class="date">
						借出:2020-12-12<br/>
						应还:2020-12-12
					</div>
					<div class="status">已还</div>
				</div>
			</div>
		</div>
		-->
		
		
	</div>
</template>

<script>
	export default{
		data() {
			return {
				avatarUrl: "../../static/img/logo.png",
				list:[],
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
		methods:{
			go2detail(id){
				uni.navigateTo({
					url:'details?id='+id
				})
			},
			async loadData() {
				uni.showLoading({
					title: 'loading...'
				})
				let data = {}
				data.page = 1
				data.pageSize = 10
				let url = this.globalData.apiHost + 'bookOrder/borrowList';
				const [error, res] = await uni.request({
					url,
					data,
					method: 'POST',
					header: {
						'authorization': uni.getStorageSync('token')
					},
				});
				uni.hideLoading()
				// console.log('============')
				// console.log(url)
				// console.log(res.data)
				// console.log(res.data.code)
				// console.log(res.data.data.list)
				this.list = res.data.data.list
				console.log(this.list)

			},
		}
	}
</script>

<style lang="scss">
	.container{
		width: 750upx;
		height: auto;
		.row{
			width: 730upx;
			height: 150upx;
			margin: 10upx auto;
			display: flex;
			flex-direction: row;
			border-bottom: 1px solid #C8C7CC;
			padding-bottom: 10upx;
			.cover{
				width: 150upx;
				height: 150upx;
				image{
					width:100%;
					height:100%;
				}
			}
			.info{
				width:560upx;
				height: 150upx;
				margin-left: 20upx;
				font-size: 18upx;
				display: flex;
				flex-direction: column;
				line-height: 30upx;
				align-content: space-around;
				.title{
					font-size: 20upx;
					font-weight: bold;
					flex: 1;
				}
				.description{
					flex:1;
				}
				.details{
					flex:1;
					display: flex;
					flex-direction: row;
					justify-content: space-between;
					.date{
						align-self: flex-end;
					}
					.status{
						align-self: flex-end;
						color:#C8C7CC;
					}
					.status-active{
						color: #0FAEFF;
					}
				}
			}
		}
	}
</style>
