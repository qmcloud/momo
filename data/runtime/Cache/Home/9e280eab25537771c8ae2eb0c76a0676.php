<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!--[if lt IE 7]>
<html class="ie oldie ie6" lang="zh">
<![endif]-->
<!--[if IE 7]>
<html class="ie oldie ie7" lang="zh">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" lang="zh">
<![endif]-->
<!--[if IE 9]>
<html class="ie ie9" lang="zh">
<![endif]-->
<!--[if gt IE 10]><!-->
<html lang="zh">
<!--<![endif]-->
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>	
	
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">

	<!-- No Baidu Siteapp-->
	<meta http-equiv="Cache-Control" content="no-siteapp"/>
    
    <meta name="referrer" content="origin">

	<!-- HTML5 shim for IE8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<![endif]-->
	<link rel="icon" href="/public/images/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/public/images/favicon.ico" type="image/x-icon">
	
	<link type="text/css" rel="stylesheet" href="/public/home/css/common.css?t=1542606715"/>
	<link type="text/css" rel="stylesheet" href="/public/home/css/login.css"/>
	<link type="text/css" rel="stylesheet" href="/public/home/css/layer.css"/>

	<meta name="keywords" content="<?php echo ($site_seo_keywords); ?>"/>
	<meta name="description" content="<?php echo ($site_seo_description); ?>"/>


<title><?php echo ($site_seo_title); ?> - <?php echo ($site_name); ?></title>

<link type="text/css" rel="stylesheet" href="/public/home/css/index.css"/>
<style type="text/css">
	.feed-list .feed{
		height: 360px;
	}
</style>
</head>
<body>
<div class="wrapper">
		<div id="doc-hd" class="header double">
		<div class="topbar">
			<div class="container clearfix">
				<div class="hd-logo">
					<a href="#" class="links"></a>
				</div>
				<ul class="hd-nav">
					<li class="item"><a href="/" <?php if($current == 'index'): ?>class="current"<?php endif; ?> >首页</a></li>
<!-- 					<li class="item"><a href="#"  <?php if($current == 'follow'): ?>class="current"<?php endif; ?> >我的关注</a></li> -->
					<li class="item"><a href="/index.php?m=Category&a=index&cat=2"  <?php if($current == '2'): ?>class="current"<?php endif; ?> >女神驾到</a></li>
					<li class="item"><a href="/index.php?m=Category&a=index&cat=1"  <?php if($current == '1'): ?>class="current"<?php endif; ?> >国民男神</a></li>
					<li class="item"><a href="/index.php?m=App&a=programe"  <?php if($current == 'download'): ?>class="current"<?php endif; ?> >APP</a></li>
					
				</ul>
				<div class="hd-login">
				  <?php if(!$user): ?><div class="no-login">
						<i class="icon-avatar"></i>
						<a href="###" class="tologin">登录/注册</a>
						<i class="icon-level"></i>
						<i class="icon-more"></i>
					</div>
					<?php else: ?>
					<div class="already-login">
						<a class="link" href="#"><i class="icon-avatar"><img src="<?php echo ($user['avatar']); ?>" alt=""/></i><span class="nickname"><?php echo ($user['user_nicename']); ?></span></a>
						<i class="icon-level"></i>
						<i class="icon-more"></i>
						<div class="userinfo">
							<div class="userinfo_up">
							</div>
							<div class="userinfo_down">
								<div class="userinfo_name">
									 <div class="live">
										<a href="./<?php echo ($user['id']); ?>">我的直播</a>
									</div>
									<div class="live">
										<a href="./index.php?m=Personal&a=index">个人中心</a>
									</div>									
									<div class="logout">
										【退出登录】
									</div>
								</div>
							</div>
						</div>
					</div><?php endif; ?>
					<div class="huajiaodou">
					  <?php if(!$user): ?><a ></a> 
					    <?php else: ?>
						 <a class="btn-huajiaodou" href="./index.php?m=Payment&a=index" target="_blank">充值</a><?php endif; ?>
						<!-- <a class="btn-huajiaodou" href="http://www.huajiao.com/economic/pc/cash.html" target="_blank">提现</a> -->
					</div> 
				</div>
				
				<div class="search-bar">
					<div class="search-hd">
					</div>
					<div class="search-bd">
						<form class="search-form" action="index.php?m=Index&a=translate" target="_top" method="post" name="search-form">
							<div class="search-input-wrap">
								<input  class="search-input" name="keyword" id="keyword" placeholder="请输入用户名或用户ID"/>
								<input type="submit" class="search-submit-btn"/>
							</div>
						</form>
					</div>
					<div class="search-ft">
						<div id="suggest-container" class="suggest-container" style="display:none;">
							<div class="suggest-bd">
							</div>
							<div class="suggest-ft">
							</div>
						</div>
					</div>
				</div>
				<!--
下线时将下面div元素的style改为"display:none;"
上线时将下面div元素的style改为"display:block;"X35
图片尺寸120X35
-->
				<!-- <div id="top-header-position" class="top-header-position" style="display:none;">
					<a target="_blank" href="#"><img src="http://p0.qhimg.com/t0135077f9010b04266.jpg"/></a>
				</div> -->
			</div>
		</div>
	</div>


	<div id="doc-bd">
		<div class="container clearfix">
			<div class="main clearfix">
				<!-- 分类推荐 -->
				<div class="g-box feed-list" >

					<div class="box-bd">
						<ul class="list">

							<?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li class="feed <?php if($v['islive'] == '1'): ?>live<?php else: endif; ?>"><a class="link" href="./<?php echo ($v['uid']); ?>" target="_blank"><img class="screenshot thumb" src="/public/home/images/lazyload.png" data-original="<?php echo ($v['thumb']); ?>"/>
							<p class="user">
								<img class="avatar thumb" src="/public/home/images/lazyload.png" data-original="<?php echo ($v['avatar']); ?>"/><span class="username"><?php echo ($v['user_nicename']); ?></span>
							</p>
							<div class="comment">
								<div class="comment-inner">
									<?php if($v['title']): echo ($v['title']); ?>
									<?php else: ?>
										此处应该有互动<?php endif; ?>
								</div>
							</div>
							</a></li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<?php echo ($page); ?>
				</div>
			</div>
			<!-- <div class="side clearfix">
				<div class="download">
					<div class="headline">
						<a href="#" target="_blank"><img src="http://p0.qhimg.com/t012fba3e01107b75a7.jpg" width="100%" alt=""/>
						<div class="logo-slogan">
						</div>
						</a>
					</div>
					<div class="btns clearfix">
						<a href="<?php echo ($config['app_android']); ?>" class="btn-android" target="_blank"></a>
						<a href="<?php echo ($config['app_ios']); ?>" class="btn-iphone" target="_blank"></a>
					</div>
					<dl class="qrcode">
						<dt class="title">
						<span>或扫描二维码下载直播APP</span>
						</dt>
						<dd class="pic">
						<img src="<?php echo ($config['qr_url']); ?>" alt=""/>
						</dd>
					</dl>
				</div>
				<div class="sidebar">
					<div class="g-box" id="topic-plan">
						<div class="box-hd">
							<h2 class="box-title"><span class="icon"></span>专题策划1</h2>
							<a href="" class="box-more"></a>
						</div>
						<div class="box-bd">
							<div class="topic">
								<div class="pic">
									<a href="<?php echo ($ads[0]['url']); ?>" target="_blank"><img src="<?php echo ($ads[0]['thumb']); ?>" width="245" height="130" alt=""/><span class="mask"></span></a>
								</div>
								<div class="content">
									<h3 class="title"><a href="<?php echo ($ads[0]['url']); ?>" target="_blank"><?php echo ($ads[0]['name']); ?></a></h3>
									<p class="desc">
										<?php echo ($ads[0]['des']); ?>
									</p>
									<p class="more">
										<a href="<?php echo ($ads[0]['url']); ?>" target="_blank">查看详情</a>
									</p>
								</div>
							</div>
							<ul class="topics">
							  <?php if(is_array($ads)): $i = 0; $__LIST__ = array_slice($ads,1,6,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($v['url']); ?>" target="_blank"><img src="<?php echo ($v['thumb']); ?>"/></a></li><?php endforeach; endif; else: echo "" ;endif; ?>

							</ul>
						</div>
					</div>
				</div>
			</div> -->
		</div>
	</div>
	<div class="area-ft">
		<div class="down-ft">
			<div class="down-ft_one fl">
				<div class="guan_wei">
					<?php if($config['sina_url'] != ''): ?><a href="<?php echo ($config['sina_url']); ?>" target="_blank"><?php endif; ?>
						<div class="guan_wei_icon fl">
							<img src="<?php echo ($config['sina_icon']); ?>">
						</div>
						<div class="guan_wei_con fl">
							<p class="guan_wei_title"><?php echo ($config['sina_title']); ?></p>
							<p class="guan_wei_desc"><?php echo ($config['sina_desc']); ?></p>
						</div>
					<?php if($config['sina_url'] != ''): ?></a><?php endif; ?>
					<div class="clearboth"></div>
				</div>

				<div class="guan_wei mar_top15">
					<?php if($config['qq_url'] != ''): ?><a href="<?php echo ($config['qq_url']); ?>" target="_blank"><?php endif; ?>
						<div class="guan_wei_icon fl">
							<img src="<?php echo ($config['qq_icon']); ?>">
						</div>
						<div class="guan_wei_con fl">
							<p class="guan_wei_title"><?php echo ($config['qq_title']); ?></p>
							<p class="guan_wei_desc"><?php echo ($config['qq_desc']); ?></p>
						</div>
					<?php if($config['qq_url'] != ''): ?></a><?php endif; ?>
					<div class="clearboth"></div>
				</div>
				
			</div>
			<div class="down-ft_two fl">
				<ul class="ewm_list">
					<li>
						<p class="ewm_title">微信公众号</p>
						<p class="ewm_icon"><img src="<?php echo ($config['wechat_ewm']); ?>"></p>
					</li>
					<li>
						<p class="ewm_title">android版下载</p>
						<p class="ewm_icon"><img src="<?php echo ($config['apk_ewm']); ?>"></p>
					</li>
					<li>
						<p class="ewm_title">iPhone版下载</p>
						<p class="ewm_icon"><img src="<?php echo ($config['ipa_ewm']); ?>"></p>
					</li>
					<div class="clearboth"></div>
				</ul>
			</div>
			<div class="down-ft_three fl">
				<ul class="href_list fl mar_left50">
					<p><?php echo ($config['sitename']); ?></p>
					<!-- <li><a href="/index.php?m=Shop&a=index">商城</a></li> -->
					<!-- <li><a href="/index.php?m=Order&a=index">排行</a></li> -->
				</ul>
				<ul class="href_list fl">
					<p>新手帮助</p>
					<li><a >新手指引</a></li>
					<li><a >赞助中心</a></li>
					<li><a >资费介绍</a></li>
				</ul>
				<div class="clearboth"></div>
			</div>
			<div class="down-ft_four fl">
				<p class="company_mobile"><?php echo ($mobile); ?></p>
				<p>客服热线(服务时间:8:00-16:00)</p>
				<p>地址:<?php echo ($config['address']); ?></p>
			</div>
			<div class="clearboth"></div>
		</div>
	</div>
	<div id="doc-ft">
		<div class="container">
			<p class="footer">
				<?php echo nl2br($config['copyright']);?>
			</p>
		</div>
	</div>
		
	  <script src="/public/home/js/jquery.1.10.2.js"></script> 
	  <script src="/public/home/js/jquery.lazyload.min.js"></script>
		<script type="text/javascript">
			window._DATA = window._DATA || {};
			window._DATA.user = <?php echo ($userinfo); ?>;
		</script> 
		<script type="text/javascript" src="/public/home/js/login.js"></script> 
		<script type="text/javascript" src="/public/home/js/layer.js"></script> 


	<!--
下线时将下面div元素的style改为"display:none;"
上线时将下面div元素的style改为"display:block;"
图片尺寸100X100
-->
	<div id="right-fixed-position" class="right-fixed-position" style="display:none;" >
		<a href="#" class="close"></a>
		<a href="#" class="link" target="_blank"><img src="#"/></a>
	</div>
</div>
<script>
$(function(){
	//图片延迟加载
	$("img.thumb").lazyload({effect: "fadeIn"});		
})
</script>

</body>
</html>