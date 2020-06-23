<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0">
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="referrer" content="origin">
		<meta name="renderer" content="webkit">
		<meta name="title" content="" />
		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<link rel="Short Icon" href="./favicon.ico"/>
		<title><?php echo ($anchorinfo["user_nicename"]); ?>的直播频道-<?php echo ($site_name); ?></title>
		
		<link rel="stylesheet" type="text/css" href="/public/home/show/css/show.css?t=1542013297"/>
		<link rel="stylesheet" type="text/css" href="/public/home/css/login.css"/>
		<link rel="stylesheet" type="text/css" href="/public/home/css/layer.css"/>
		<?php if($user): ?><link rel="stylesheet" href="/public/home/hxChat/css/webim.css" />
		 <!-- 环信私信功能调用样式和js start --><?php endif; ?>
	</head>
	<body>
		<div class="selectPlay" id="selectPlay"></div>
		<div class="dds-dialog-bg" id="ds-dialog-bg"></div>
		<div class="SR-pager" id="LF-pager">
			<div class="SR-nav" id="LF-nav">
				<!--左侧导航-->
				<div class="nav-fg">
					<h1>
						<a href="./" target="_blank">
							<img src="/logo.png" alt="<?php echo ($config['sitename']); ?>"/>
						</a>
					</h1>
					<ul class="nav" id="LF-nav-fg">
						<li class="user" id="LF-user">
							<div class="MR-user">
								<?php if($user): ?><div class="login">
									<div class="photoer" id="M-user-name-hook">
										<a href="./index.php?m=Personal&a=index" target="_blank"><img src="<?php echo ($user["avatar"]); ?>" /></a>
										
									</div>
									<div class="money">
										<label><i class="ICON-coins"></i><?php echo ($config['name_coin']); ?></label>
										<cite title="<?php echo ($user["coin"]); echo ($config['name_coin']); ?>"><?php echo ($user["coin"]); ?></cite>
									</div>
									<a href="./index.php?m=Payment&a=index" class="recharge" target="_blank">充值</a>
								</div>									
								<?php else: ?>
								<div class="hd-login">
								<div class="no-login">
									<a href="javascript:void(0);" class="login" id="LF_login">登录</a>
									<a href="javascript:void(0);" class="reg" id="LF_reg">注册</a>
								</div>
								</div><?php endif; ?>
							</div>								
						</li>
						<li>
								<a href="./" class="linked" target="_blank">首页</a>
						</li>
<!-- 						<li class="had-dialog" id="LF-toggle-news">
								<label class="fun">消息</label>
						</li> -->
<!-- 						<li class="had-dialog" id="LF-toggle-square">
								<label class="fun">广场</label>
						</li> -->
						<!-- <li class="had-dialog" id="LF-toggle-online">
								<label class="fun">用户</label>
						</li> -->
					</ul>
				</div>
				
				<!--左侧底部导航-->
				<div class="nav-link " id="LF-nav-link">
					<ul>
							<li class="share">
									<div class="icon">
											<dl>
													<dt><i class="ICON-nav-share"></i></dt>
													<dd><span class="desc"><?php if($isplay): ?>分享<?php else: ?>打开<?php endif; ?></span></dd>
											</dl>
									</div>
									<div class="info" id="LF-share">
										<div class="MR-share">
											<div class="M-bubble">
												<div class="arrow-left"></div>
												<div class="detail">
													<a class="weibo" href="javascript:void(0)"  title="可在微博中打开" target="_blank" data-target="weibo"><i class="ICON-weibo"></i><label>微博</label></a>
													<a class="qq" href="javascript:void(0)"  title="可在QQ中打开" target="_blank" data-target="qq"><i class="ICON-qq"></i><label>QQ</label></a>
													<div class="qr">
														<img src="/index.php?g=Home&m=show&a=qrcode&roomid=<?php echo ($anchorinfo['id']); ?>">
													<!-- 	<img src="<?php echo ($config['qr_url']); ?>"> -->
													</div>
												</div>
											</div>
										</div>
									</div>
							</li>
							<li class="app">
								<a href="<?php echo ($config['app_android']); ?>" target="_blank">
									<div class="icon">
											<dl>
													<dt><i class="ICON-nav-app"></i></dt>
													<dd><span class="desc">APK下载</span></dd>
											</dl>
									</div>
									<div class="info">
											<div class="app-ma"></div>
									</div>
								</a>	
							</li>
							<li class="app">
								<a href="<?php echo ($config['app_ios']); ?>" target="_blank">
									<div class="icon">
											<dl>
													<dt><i class="ICON-nav-app"></i></dt>
													<dd><span class="desc">IOS下载</span></dd>
											</dl>
									</div>
									<div class="info">
											<div class="app-ma"></div>
									</div>
								</a>	
							</li>
							<li class="help">
									<a href="javascript:void(0);" target="_blank">
											<i class="ICON-nav-help"></i>
											<span class="desc">帮助</span>
									</a>
							</li>
					</ul>
				</div>
			</div>
			<!-- 主体区域 -->
			<div class="SR-stager clearfix" id="LF-stager">
				
				<!-- 信息通知 -->
				<div class="SR-area-chat" id="LF-area-chat">
					<ul class="SR-area-Tab">
						<li class="on" style="width: 60px;">送礼榜</li>
						<li>本场贡献榜</li>
						<li>全站贡献榜</li>
						<li style="width: 120px;">在线用户(<cite>0</cite>)</li>
						<div class="clearboth"></div>
					</ul>

					<div class="chat-msg" id="LF-chat-msg">
						<div class="MR-msg-list">

							<!-- 礼物记录 -->
							<div class="MR-msg tab-con">
								<div class="msg-gift player-main" id="msg_gift">
									<div class="MR-chat">
										<div class="boarder">
											<ul class="clearfix">
												
											</ul>
										</div>
									<!-- 	<div class="scroller"></div> -->
									</div>
									<!-- <div class="hjPopbox"> 
									</div> --> 
								</div>
								<span class="MR-sound-toggle hide">声效开</span>
								<div class="MR-sound-tip hide"></div>
							</div>
							<!-- 本场贡献榜 -->
							<div class="MR-rank now_Rank tab-con">

							</div>
							<!-- 全站贡献榜 -->
							<div class="MR-rank total_Rank tab-con">
								
							</div>

							<!--用户列表-->
							<div class="nav-bg tab-con" id="LF-nav-bg">
								<div class="nav-panel hide" id="LF-panel-news"></div>
								<div class="nav-panel hide" id="LF-panel-square"></div>
								<div class="nav-panel" id="LF-panel-online">
									<div class="MR-online">
										<div class="dialog">
											<!-- <ul class="nav-tab">
												<li class="end user on">在线用户(<cite><?php echo ((isset($liveinfo["nums"]) && ($liveinfo["nums"] !== ""))?($liveinfo["nums"]):0); ?></cite>)</li>
												<li class="admin">社团管理员(<cite>0</cite>)</li>
											</ul> -->
											<div class="nav-con" id="nav_con">
												<div class="user-con">
													<div class="MR-chat">
														<div class="boarder">
															<ul class="clearfix">
															
															</ul>
														</div>
														<div class="scroller"></div>
													</div>
													<!-- <div class="more-toggle"></div> -->
												</div>
												<!-- <div class="admin-con" style="display: none;">
													<div class="MR-chat">
														<div class="boarder">
															<ul class="clearfix">
																
															</ul>
														</div>
														<div class="scroller"></div>
													</div>
												</div> -->
											</div>
										</div>
									</div>
								</div>
								
							</div>

						</div>

						<!-- 聊天记录 -->
						<div id="LF-chat-msg-area" class="MR-msg">
							<div class="msg-chat">
								<div class="MR-chat">
									<div class="boarder">
										<ul class="clearfix">
											<!-- <li><span class="fake-name">管理小助手：</span>亲，想知道播客最新消息么？请“关注”Ta。</li> -->
										</ul>
									</div>
									<!-- <div class="scroller"></div> -->
									<span class="ICON-lock-screen hide"></span>
								</div>
							</div>
							<div class="MR-msg-notice clearfix hide">
								<span class="title">弹幕</span>
								<div class="msg-content"></div>
							</div>
						</div>
						<div class="control-bar">
							<cite></cite>
							<span></span>
						</div>
						<div id="MR-brand"></div>
					</div>


					<div class="chat-talk" id="LF-chat-talk">
						<!-- 聊天发送 -->
						<div class="MR-talk">
							<span class="send-btn">发送</span>
							<div class="speaker">
								<input type="text" value="和大家聊会儿天？" />
								<cite>30</cite>
							</div>
							<div class="emoticon-toggle-panel">
								<!-- <a href="javascript:void(0);" class="BTN-face-toggle"></a> -->
							</div>
						</div>
						<!-- 喇叭 -->
						<div class="MR-horn">
							<div class="toggle"></div>
							<div class="selector M-bubble hide">
								<div class="arrow-right"></div>
								<span class="closed"></span>
								<div class="detail">
									<div class="btn gold hide" data-gid="" data-num="0">
										<p class="name">金喇叭<span class="num"></span></p>
										<p class="price"><i class="ICON-coins">200</i></p>
									</div>
									<div class="btn site" data-gid="" data-num="0">
										<p class="name">弹幕<span class="num"></span></p>
										<p class="price"><i class="ICON-coins"><?php echo ($getConfigPri['barrage_fee']); ?></i></p>
									</div>
								</div>
							</div>
							<div class="dialog hide">
								<h4 class="gold"><i></i></h4>
								<div class="detail">
									<textarea maxlength='10'>请输入...</textarea>
									<div class="opt clearfix">
										<span></span>
										<span class="num" style="display: none;">，本次免费</span>
										<input type="button" value="发送" class="horn-send" />
									</div>
									<span class="closed"></span>
								</div>
							</div>
						</div>
					</div>
					
					<!-- 头条 -->
					<div class="MR-big-gift">
						<div class="out-boxer" style="opacity: 1; top: 0px;">
							<div class="high-boxer" style="margin-left: -149.5px;">
								<a href="/room/67656" target="_blank">
									<cite class="photoer"><img src="http://static.youku.com/ddshow/img/avatar/80/33.jpg" /></cite>
									<cite class="desc">
										<span class="user" title="榴莲的小狮子">榴莲的小狮子</span>
										<span class="txt">送给</span>
										<span class="name" title="大大元气满满">大大元气满满</span>心心相印
									</cite>
									<span class="gift-pic"><img src="http://static.youku.com/ddshow/img/lfgift/xxxy5_80_80.png" /></span>
									<cite class="desc gift-num">x188</cite>
								</a>
							</div>
						</div>
					</div>					
				</div>

				<!-- 直播 -->
				<div class="SR-area-video" id="LF-area-video">
					<!-- 直播 -->
					<?php if($isplay): ?><div class="channel-player-v2" chromeq="1" id="container">
						<div class="player" id="webplayer" >
							 
						</div> 
					</div>
					<?php else: ?>
					<div class="channel-player-v2" chromeq="1">
                        <div id="playerzmblbkjP" style="width:100%;height:100%;"></div>
						
					</div><?php endif; ?>

					<div class="gift_effect_area">
						<div class="hjPopbox"> 
						</div>
					</div>
					<!-- 主播信息 -->
					<div class="MR-about">
						<dl class="clearfix">
							<dt>
								<div class="photoer">
									<img class="anchor-head" src="<?php echo ($anchorinfo["avatar"]); ?>" alt="<?php echo ($anchorinfo["user_nicename"]); ?>" data-id="<?php echo ($anchorinfo["id"]); ?>" />
								</div>
							</dt>
							<dd>
								<span class="name"><?php echo ($anchorinfo["user_nicename"]); ?><span class="level"><!-- <span class="ICON-anchor-level ICON-nl-10<?php echo ($anchorinfo["level"]); ?>"></span> --></span></span>
								<!-- <div class="dou-prog">
									<div class="M-progress">
										<div class="slider"></div>
										<div class="num"></div>
									</div>
								</div>
								<div class="star-con clearfix">
									<p class="beans">差791641星豆升级</p>
									<p class="star" title="星星数">6681</p>
								</div> -->
								<div class="medal-con"></div>
							</dd>
						</dl>
						<p class="fans-num" title="粉丝数"><?php echo ($anchorinfo["fans"]); ?></p>
						
						<p class="M-attention"><a class="BTN-add-attention" href="javascript:void(0)"><?php echo ($attention_type); ?></a></p>
	
						<?php if($isplay): ?><p class="closeroom_icon" onclick="Interface.stopRoom()">关闭直播</p>
							<!-- <a class="info usertype" data-type="0" id="admin_change" onclick="liveType.changeTypeValue()">切换收费金额</a> -->
							<?php if($isplay and $liveinfo['type'] != 1): ?><p class="roomchange_icon" data-type="0" id="changetype" style="display: none;" onclick="liveType.changeTypeValue()">切换收费金额</p><?php endif; ?>
						<?php else: ?>
							<?php if($usertype == 60): ?><p class="closeroom_icon" data-type="0" id="admin_close">关闭直播</p>	
								<p class="forbidroom_icon" data-type="1" id="admin_stopit">禁止直播</p><?php endif; ?>
							<?php if($usertype != 50 and $usertype > 10): ?><p class="report_icon" id="admin_report">举报</p><?php endif; endif; ?>
						
							
						
						<p class="share_icon">分享</p>
					<!-- 	<span class="M-siliao" id="DDS_siliao_enter" data-id="<?php echo ($anchorinfo["id"]); ?>" data-name="<?php echo ($anchorinfo["fans"]); ?>" data-faceurl="<?php echo ($anchorinfo["avatar"]); ?>">私聊</span> -->
						<div class="signs-con clearfix">
							<div class="signs">
								<?php echo ($anchorinfo["signature"]); ?>
							</div>
						</div>
					</div>

					<!-- 分享 -->
					<div class="share_area">
						<div>
							<div class="share_area_left fl">
								<p>分享</p>
								<div class="share_icon_list detail">
									
									<a class="weibo" href="javascript:void(0)" title="可在微博中打开" target="_blank" data-target="weibo">
										<img src="/public/home/show/images/weibo_icon.png">
									</a>
								
								
									<a class="weibo" href="javascript:void(0)" title="可在QQ中打开" target="_blank" data-target="qq">
										<img src="/public/home/show/images/qq_icon.png">
									</a>
									
								</div>
								<div>
									<span>直播间地址</span>
									<span><input class="share_url" id="share_url" type="text" value="<?php echo ($config['site']); ?>/<?php echo ($anchorinfo['id']); ?>" >
									</span>
									<span>
										<input class="share_copy_btn" type="button" onclick="copyinput(this,'share_url')" value="复制链接">
									</span>
								</div>
							</div>
							<div class="share_area_right fr">
								<p>二维码分享</p>
								<p><img src="/index.php?g=Home&m=show&a=qrcode&roomid=<?php echo ($anchorinfo['id']); ?>" width="100px"></p>
							</div>
						</div>
						
					</div>
					<!-- 弹幕 -->
					<div class="MR-horn-show hide">
						<div class="bg"></div>
					</div>
					<!--点亮start-->
					<div id="player-praises" data-bk="player-praises"> 
						<div class="bubble"> 
						</div> 
							<a href="###" class="praises">
								<span></span>
							</a> 
					</div>
					<!--点亮end-->
					<!-- 主播推荐 -->
					<div class="SR-video-rec-con" id="SR-video-rec-con">
						<span class="rec-title">主播推荐</span>
						<div class="video-rec-con">
							<ul>
							</ul>
						</div>
					</div>

					<!-- 礼物 -->
					<div class="chat-gift" id="LF-chat-gift">
						<div class="MR-gift">
							<div class="gift-panel">
								<ul class="gift-tab">
									<li data-index="0" class="on">热门<i class="dot-new"></i></li>
									<!-- <li data-index="1">豪华</li>
									<li data-index="2">特殊</li>
									<li data-index="3">专属</li>
									<li data-index="4">我的包裹<i class="dot-tip hide"></i></li> -->
								</ul>
								<div class="gift-con">
									<div class="gift-group">
									
										<div class="gift-wrap">
											<div class="M-arrow-scroll">
												<span class="left-arrow hide"></span>
												<span class="right-arrow"></span>
												<div class="con">
													<div class="scroll">
														<ul class="clearfix">
															<?php if(is_array($giftinfo)): $i = 0; $__LIST__ = $giftinfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li class="gift " data-locked="0" data-title="<?php echo ($v['giftname']); ?>" data-name="<?php echo ($v['giftname']); ?>" data-id="<?php echo ($v['id']); ?>" data-price="<?php echo ($v['needcoin']); ?>">
																<div class="gift-pic">
																	<img src="<?php echo ($v['gifticon']); ?>" />
																</div>
																<?php if($v['mark'] == '0'): if($v['type'] == '1'): ?><div class="mark gift-lian">
																	豪
																	</div>
                                                                    <?php else: ?>
                                                                    <div class="mark gift-lian">
																	连
																	</div><?php endif; ?>
                                                                <?php elseif($v['mark'] == '1'): ?>
                                                                    <div class="mark gift-ren">
																	热
																	</div>
                                                                <?php elseif($v['mark'] == '2'): ?>
                                                                    <div class="mark gift-shou">
																	守
																	</div>
                                                                <?php elseif($v['mark'] == '3'): ?>
                                                                    <div class="mark gift-xing">
																	幸
																	</div><?php endif; ?>
															</li><?php endforeach; endif; else: echo "" ;endif; ?>
														</ul>
													</div>
												</div>
											</div>
										</div>
										
										<div class="MR-gift-tip hide">
											<div class="tip-content">
												<img class="tip-img" src="" >
												<div class="gift-tip-con">
													<span class="gift-tip-name"></span>
													<span class="gift-tip-price"></span>
												</div>
												<div class="gift-tip-desc"></div>
											</div>
										</div>
									</div>
									<div class="gift-footer clearfix">
										<!-- 星星 -->
										<div class="MR-free-gift hide">
											<div class="MR-star">
												<div class="progress"></div>
												<div class="icon"></div>
												<div class="count">
													40
												</div>
											</div>
											<ul class="freegift-more clrearfix">
												<li class="more-btn star-0">
													<div class="icon"></div>
												</li>
												<li class="more-btn star-1">
													<div class="icon"></div>
												</li>
												<li class="more-btn star-10">
													<div class="icon"></div>
													<div class="bg">
														<div class="tips ">
															星动
														</div>
													</div>
												</li>
											</ul>
										</div>
										<!-- 点歌 -->
										<div class="MR-app MR-app-content hide">
											<div class="MR-app-item M-app-UI-enable">
												<span class="icon app-icon-song"></span>
												<span class="title">点歌</span>
											</div>
										</div>
										<!-- 礼物赠送数量 -->
										<div class="MR-moregift">
											<a class="send-btn" href="javascript:void(0);">赠送</a>
										</div>
									</div>
								</div>
							</div>
						</div>					
					</div>
				</div>


				<!-- 右侧 -->
				<!-- <div class="SR-area-info" id="LF-area-info">
					<div class="info-count" id="LF-info-count">
							<div class="MR-count">
								<?php if($isplay): ?><div class="guanbi">
									<a class="info usertype" data-type="0" onclick="Interface.stopRoom()">关闭直播</a>
								</div>
								<div class="guanbi" id="changetype" style="display:none;">
									<a class="info usertype" data-type="0" id="admin_change" onclick="liveType.changeTypeValue()">切换收费金额</a>
								</div>
								<?php else: ?>
								<?php if($usertype == 60): ?><div class="gift">
										<a class="info usertype" data-type="0" id="admin_close">
											关闭直播
										</a>
									</div>
									<div class="gift">
										<a class="info usertype" data-type="1" id="admin_stopit">
											禁止直播
										</a>
									</div><?php endif; ?>
								<?php if($usertype != 50 and $usertype > 10): ?><div class="online">
										<a class="info report" id="admin_report">
											举报
										</a>
									</div><?php endif; endif; ?>
							</div>					
					</div>
					
					<div class="info-rank" id="LF-info-rank">
						<div class="MR-rank">
							<ul class="tab">
								<li class="on"><span><cite>本场贡献榜</cite><i></i></span></li>
								<li class="end mode-sns"><span><cite>全站贡献榜</cite><i></i></span></li>
							</ul>
							<div class="con">
							</div>
						</div>					
					</div>					
					<div class="info-community" id="LF-info-community">	
					</div>
				</div> -->
				<!-- 礼物动画 -->
				<div class="MR-gift-flash" id="LF-gift-container">
					<div id="LF-gift-flash"></div>
				</div>
				<!-- 坐骑 -->
				<div class="MR-enter-fx" id="LF-enter-fx">
					<div id="LF-enter-flash">
						<img src="/public/home/images/lazyload.png">
					</div>
				</div>
				
			</div>
		</div>
		<script>
			var _DATA = {};
			_DATA.config=<?php echo ($configj); ?>;
			_DATA.anchor=<?php echo ($anchorinfoj); ?>;
			_DATA.live=<?php echo ($liveinfoj); ?>;
            _DATA.level=<?php echo ($levellistj); ?>;
			_DATA.level_anchor=<?php echo ($levelanchorlistj); ?>;
			_DATA.gift=<?php echo ($giftinfoj); ?>;
			_DATA.user=<?php echo ($userinfo); ?>;
            _DATA.usertype='<?php echo ($usertype); ?>';
			var charge_interval = null;
			var giftQueue = new Array(); 
			var giftPlayState = 0;
			var carQueue = new Array(); 
			var carPlayState = 0;
			var barrage_fee=<?php echo ($getConfigPri['barrage_fee']); ?>;

			var timeout; //点击用户列表时5秒消失产生的id
		</script>
		<script src="/public/js/jquery.js"></script>
		<script src="/public/js/md5.js"></script>
		<script src="/public/home/js/layer.js"></script>
		
		
		<script src="/public/home/js/Ku6SubField.js"></script>
		<script src="/public/home/js/swfobject-2.3.js"></script> 

		
		
		
		
		
		<!-- 视频播放start -->
        <script src="/public/xigua/xgplayer.js?t=1574906138" type="text/javascript"></script>
        <script src="/public/xigua/xgplayer-flv.js.js" type="text/javascript"></script>
        <script src="/public/xigua/xgplayer-hls.js.js" type="text/javascript"></script>
        <script src="/public/xigua/player.js" type="text/javascript"></script>
		
		
		<script src="/public/home/js/login.js"></script> 
        
		<script src="/public/home/js/socket.io.js?t=1571709925"></script> 
		<script>var socket = new io("<?php echo ($configpri['chatserver']); ?>");</script>
		<script src="/public/home/js/event.js?t=1567474342"></script> 
		<script src="/public/home/js/eventListen.js"></script> 
		<script src="/public/home/show/js/chat.js"></script>
		
		<?php if($isplay): ?><script type="text/javascript">
			function getSwfObject(container) {
					var id = $("#" + container + " object").attr("id");
					if (navigator.appName.indexOf("Microsoft") != -1) {
					return window[id]
			} else {
					return document[id]
			 }
			}
			
			function stopPublish()
			{
						location.href='./';
						getSwfObject("container").stopPublish();
			}
			
			function gotoPlayVideo()
			{
				 getSwfObject("container").gotoPlayVideo();
			}
			function alertMessage(msg)
			{
				 getSwfObject("container").alertMessage(msg);	
			}	
			(function(){
					
						var  script_text= "swfobject.embedSWF(\"/public/home/js/5ShowCamLivePlayer.swf?uid="+_DATA.anchor.id+"&token=<?php echo ($token); ?>&roomId=<?php echo ($push['stream']); ?>&stream="+_DATA.anchor.stream+"&cdn=<?php echo ($push['cdn']); ?>&keyframe=<?php echo ($config['keyframe']); ?>&fps=<?php echo ($config['fps']); ?>&bandwidth=0&width=<?php echo ($config['live_width']); ?>&height=<?php echo ($config['live_height']); ?>&quality=<?php echo ($config['quality']); ?>&wy_cid=<?php echo ($wy_cid); ?>\" ,\"webplayer\",\"100%\",\"100%\",\"10.0\", \"\",{},{quality:\"high\",wmode:\"opaque\",allowscriptaccess:\"always\"})";
						$("#webplayer").html("<script>"+script_text+"<\/script>");
						Socket.nodejsInit();
			})()
		</script>
		<?php else: ?>
		<script type="text/javascript">
			$(function(){
				liveType.checkLive();
			})
		</script><?php endif; ?>
		
		<script type="text/javascript" src="/public/home/js/common.js?t=1542013297"></script>



	<?php if($user): ?><!-- 环信私信功能代码start -->
	<!--如果登录了账号，才调用环信相关代码-->
	<!-- 聊天窗口start -->
    <div class="hxChatWindow">
        <div class="Chatcontent" id="content">
            <div class="leftcontact" id="leftcontact">
                <div id="headerimg" class="leftheader">
                    <span> <img src="<?php echo ($user['avatar']); ?>" alt="logo" class="img-circle" width="50px" height="50px" style="margin-top: 5px; float:left;margin-left: 5px;border-radius: 30px;" /></span> 
					<span id="login_user" class="login_user_title"> <a class="leftheader-font" href="#"><?php echo ($user['user_nicename']); ?></a></span> 
					<span></span>
                </div>
                <div id="leftmiddle">
                   <div>
                        <input type="text" id="searchfriend" value="请输入用户id" onFocus="if(value==defaultValue){value='';}"  onBlur="if(!value){value=defaultValue;}" />
						<button id="searchFriend" onclick="beforeSearch()">查询</button>
						<div class="clearboth"></div>
						<input type="hidden" id="currentChatUid" value="">
                   </div>
                    <div class="searchResult"></div>
                </div>
                <div id="contractlist11">
                    <div class="accordion" id="accordionDiv">
                        <div class="accordion-group">
                            <div id="collapseThree" class="accordion-body collapse">
                                <div class="accordion-inner" id="momogrouplist">
                                    <ul id="momogrouplistUL" class="chat03_content_ul"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div id="rightTop" style="height: 78px;"></div> -->
            <!-- 聊天页面 -->
            <div class="chatRight">
                <div id="chat01">
                    <div class="chat01_title">
                        <ul class="talkTo">
                        	<li id="recycle" style="margin-left:5px;padding-left: 0;width: 65px;">
                                <a title="清除聊天" onclick="clearCurrentChat();">清除聊天</a>
                            </li>
                            <li id="talkTo"><a href="#"></a></li>
                            <li style="float: right;width: 25px;">
                                <img src="/public/home/hxChat/images/close.png" onclick="closeHxChat();"  style="margin-right: 15px; cursor: hand; width: 18px;" title="关闭窗口" />
                            </li>
                        </ul>
                    </div>
                    <div id="null-nouser" class="chat01_content"></div>
                </div>

                <div class="chat02">
                    <!-- 表情按钮行start -->
                    <div class="chat02_title">
                        <!-- <a class="chat02_title_btn ctb01" onclick="showEmotionDialog()" title="选择表情"></a>
                        <input id='sendPicInput' style='display:none'/>
                        <label id="chat02_title_t"></label>
                        <div id="wl_faces_box" class="wl_faces_box">
                            <div class="wl_faces_content">
                                <div class="title">
                                    <ul>
                                        <li class="title_name">常用表情</li>
                                        <li class="wl_faces_close"><span
                                            onclick='turnoffFaces_box()'>&nbsp;</span></li>
                                    </ul>
                                </div>
                                <div id="wl_faces_main" class="wl_faces_main">
                                    <ul id="emotionUL">
                                    </ul>
                                </div>
                            </div>
                            <div class="wlf_icon"></div>
                        </div> -->
                    </div>
                    <!-- 表情按钮行end -->
                     <!-- 文本输入区域start -->
                    <div id="input_content" class="chat02_content">
                        <textarea id="talkInputId" style="resize: none;"></textarea>
                    </div>
                    <!-- 文本输入区域end -->
                    <!-- 发送按钮行start -->
                    <div class="chat02_bar">
                        <ul>
                            <li style="right: 5px; top: 5px;"><img src="/public/home/hxChat/img/send_btn.jpg" onclick="sendText()" /></li>
                        </ul>
                    </div>
                    <!-- 发送按钮行end -->
                    <div style="clear: both;"></div>
                </div>
            </div>
            <input type="hidden" id="HxChatUid" value="<?php echo ($user['id']); ?>">
            <input type="hidden" id="CurrentUid" value="<?php echo ($user['id']); ?>">
        </div>
    </div>
    <!-- 聊天窗口end -->
    <!-- <div class="MinChat" onclick="ShowhxChatWindow()"></div> --><?php endif; ?>

	<script type="text/javascript">
		var intervaltime1,intervaltime2;

		function show_nowOther(){
			if(intervaltime1){
				clearTimeout(intervaltime1);
			}
			$(".now_other").show();
		}

		function hide_nowOther(){
			
			intervaltime1=setTimeout(function(){
				$(".now_other").hide();
			},1000);
		}

		function show_allOther(){
			if(intervaltime2){
				clearTimeout(intervaltime2);
			}
			$(".all_other").show();
		}

		function hide_allOther(){
			
			intervaltime2=setTimeout(function(){
				$(".all_other").hide();
			},1000);
		}

		function copyinput(event,copyid){
		    document.getElementById(copyid).select();
		    document.execCommand("copy",false,null);
		    layer.msg("已复制到粘贴板");
		};
	</script>
	</body>
</html>