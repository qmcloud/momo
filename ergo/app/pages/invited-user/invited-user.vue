<template>
	<view class="content">
		<view class="invite-qrcode">
			<view class="invited-users">
				<view class="invited-users-title">我邀请的用户</view>
				<view class="level-selector">
					<text v-for="(item,index) in levelArr" :class="{act: index === selectedLevel}" :key="index" @click="changeLevel(index)">{{item}}级下线</text>
				</view>
				<view class="invited-users-list">
					<view class="invited-users-item" v-for="(item,index) in invitedUser" :key="index">
						<text class="username">{{item.username || '新用户'}}</text>
						<text class="mobile">{{item.mobile}}</text>
					</view>
				</view>
				<uni-load-more :status="status"></uni-load-more>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				invitedUser: [],
				loading: true,
				levelArr: ['一', '二', '三'],
				selectedLevel: 0,
				status: 'more',
				pageSize: 40,
				current: 1
			}
		},
		onLoad() {
			this.getList()
		},
		onReachBottom() {
			if (this.status !== 'loading') {
				this.getList()
			}
		},
		methods: {
			changeLevel(index) {
				this.selectedLevel = index
				this.getList(true)
			},
			getList(refresh) {
				this.status = 'loading'
				uniCloud.callFunction({
					name: 'user-center',
					data: {
						action: 'getInvitedUser',
						params: {
							level: this.selectedLevel + 1,
							offset: (this.current - 1) * this.pageSize,
							limit: this.pageSize,
							needTotal: false
						}
					},
					success: (res) => {
						console.log(res);
						if (res.result.code === 0) {
							// 这里请修改为真实的邀请页面url
							const tempList = res.result.invitedUser
							this.invitedUser = refresh ? tempList : this.invitedUser.concat(tempList)
							if (tempList.length < this.pageSize) {
								this.status = 'noMore'
							} else {
								this.status = 'more'
							}

						} else {
							this.status = 'more'
							uni.showModal({
								content: '获取被邀请用户列表失败:' + res.result.msg,
								showCancel: false
							})
						}
					},
					fail: (err) => {
						uni.showModal({
							content: '获取被邀请用户列表失败，请稍后再试',
							showCancel: false
						})
					},
					complete: () => {
						this.loading = false
					}
				})
			}
		}
	}
</script>

<style>
	.invited-users {
		background-color: #FFFFFF;
		margin-top: 10px;
		border-radius: 5px;
	}

	.invited-users-title {
		text-align: center;
		padding-top: 10px;
		margin-bottom: 10px;
	}

	.level-selector {
		display: flex;
		padding: 10px;
	}

	.level-selector text {
		flex: 1;
		line-height: 30px;
		padding-bottom: 5px;
		border-bottom: solid 2px transparent;
		text-align: center;
	}

	.level-selector text.act {
		border-bottom-color: #007AFF;
	}

	.invited-users-empty {
		font-size: 12px;
		color: #999999;
		text-align: center;
	}

	.invited-users-item {
		display: flex;
		flex-direction: row;
		border-bottom: solid 1px #DDDDDD;
	}

	.invited-users-item:last-child {
		border-bottom: none;
	}

	.invited-users-item .username,
	.invited-users-item .mobile {
		flex: 1;
		text-align: center;
		font-size: 14px;
		color: #666666;
		line-height: 24px;
	}
</style>
