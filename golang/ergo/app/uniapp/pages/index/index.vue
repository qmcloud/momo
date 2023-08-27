<template>
	<view>
		<!-- 轮播图 -->
		<swiper :indicator-dots="true" :autoplay="true" :interval="3000" :duration="200" style="width: 750rpx;height: 250rpx;">
			<swiper-item>
				<image src="../../static/demo/banner/1.jpg" style="width: 750rpx;height: 250rpx;"></image>
			</swiper-item>
			<swiper-item>
				<image src="../../static/demo/banner/2.jpg" style="width: 750rpx;height: 250rpx;"></image>
			</swiper-item>
		</swiper>
		
		<!-- 列表 -->
		<view class="flex flex-wrap">
			
			<view class="list-item" v-for="(item,index) in list" :key="index" @click="openLive(item.id)">
				<image :src="item.cover || '/static/demo/1.jpg'" style="width: 365rpx;height: 365rpx;" class="rounded" mode="aspectFill"></image>
				
				<view class="rounded-circle px-2 flex align-center" style="position: absolute;left: 15rpx;top: 15rpx;background-color: rgba(0,0,0,0.4);">
					<text class="iconfont iconbizhongguanli text-warning mr-1"></text><text class="text-white font">{{ item.coin }}</text>
				</view>
				
				<view class="rounded-circle px-2 flex align-center" style="position: absolute;right: 15rpx;top: 15rpx;background-color: rgba(0,0,0,0.4);">
					<text class="font-sm text-white">人气：</text><text class="text-white font-sm">{{ item.look_count }}</text>
				</view>
				
				<view class="rounded-circle flex align-center" style="position: absolute;left: 15rpx;bottom: 15rpx;">
					<text class="text-white font">{{ item.title }}</text>
				</view>
				
				<view class="rounded-circle px-2 flex align-center" style="position: absolute;right: 15rpx;bottom: 15rpx;background-color: rgba(0,0,0,0.4);">
					<text style="width: 20rpx;height: 20rpx;" class="rounded-circle mr-1" :class=" 'bg-' + (item.status === 1 ? 'success' : 'danger') "></text><text class="text-white font-sm">{{ item.status | formatStatus }}</text>
				</view>
			</view>

			
		</view>
		
		<view class="f-divider"></view>
		<view class="flex align-center justify-center py-3">
			<text class="font-md text-secondary">{{ loadText }}</text>
		</view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list:[],
				page:1,
				loadText:"上拉加载更多"
			}
		},
		filters: {
			formatStatus(value) {
				let o = {
					0:"未开始",
					1:"直播中",
					2:"暂停",
					3:"已结束",
				}
				return o[value];
			}
		},
		onLoad() {
			this.getData()
		},
		onPullDownRefresh() {
			this.page = 1
			this.getData().then(res=>{
				uni.showToast({
					title: '刷新成功',
					icon: 'none'
				});
				uni.stopPullDownRefresh()
			}).catch(err=>{
				uni.stopPullDownRefresh()
			})
		},
		onReachBottom() {
			if(this.loadText !== '上拉加载更多'){
				return
			}
			this.loadText = '加载中...'
			this.page++
			this.getData()
		},
		methods: {
			getData(){
				let page = this.page
				return this.$H.get('/live/list/'+page).then(res=>{
					this.list = page === 1 ? res : [...this.list,...res],
					this.loadText = res.length < 10 ? '没有更多了' : '上拉加载更多'
				}).catch(err=>{
					if(this.page > 1){
						this.page--
						this.loadText = '上拉加载更多'
					}
				})
			},
			openLive(id){
				uni.navigateTo({
					url: '../live/live?id='+id
				});
			}
		}
	}
</script>

<style>
	.list-item{
		width: 375rpx;height: 375rpx;padding: 5rpx;box-sizing: border-box;position: relative;
	}
</style>
