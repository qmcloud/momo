<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <meta name="referrer" content="origin">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<meta content="telephone=no" name="format-detection" />
		<title>我的账号</title>
		<style>
			*{
				margin:0;
				padding:0;
				font:16px/1.5 Arial, '\5FAE\8F6F\96C5\9ED1';
				color:#000;
			}
			img{
				border:none;
				vertical-align:middle;
			}
			a{
				text-decoration:none;
			}
			a:hover{
				text-decoration:none;
			}
			body{
				background:#fff;
			}
			ul,li{list-style:none;}
			.header{
				padding:10px 5px 10px 15px;
				font-weight:bold;
			}
			.header img{
				width: 40px;
				height: 40px;
				border-radius: 50%;
			}
			.header .coin{
				font-weight:bold;
			}
			
			.header .user-id{
				float:right;
				line-height:40px;
				margin-right:15px;
			}
			.line{
				background:#eee;
				height:10px;
			}
			.content{
			
			}
			.list{
			
			}
			.list li{
				padding:10px 15px;
				border-bottom:1px solid #eee;
			}
			.list li.on{
				border:1px solid #FED25C;
			}
			
			.list li:after{
				content:".";
				display:block;
				height:0;
				clear:both;
				visibility:hidden;
				overflow:hidden;		
			}
			.list li .li-left{
				float:left;
				line-height:30px;
				font-size:16px;
			}
			.list li .li-left img{
				width:20px;
				height:20px;
				margin-right:5px;
			}
			.list li .li-left span{
				font-size:14px;
			}
			.list li .li-right{
				float:right;
			}
			.list li .li-right a{
				display:block;
				background:#FED25C;
				border-radius:4px;
				padding:5px 10px;
				text-align:center;
				font-weight:bold;
				width:70px;
			}

			.footer{
				text-align:center;
				
			}
			.footer a{
				color:#3e97f8;
				line-height:50px;
				margin-left:3px;
				margin-right:3px;
			}
		</style>
	</head>
<body >
	<div class="header">
		充值账号：<img src="<?php echo ($userinfo['avatar_thumb']); ?>"> <span class="coin"><?php echo ($userinfo['user_nicename']); ?></span> <span class="user-id">ID:<?php echo ($userinfo['id']); ?></span>
	</div>
	<div class="header">
		余额： <span class="coin"><?php echo ($userinfo['coin']); ?></span>
	</div>
	<div class="line"></div>

	<div class="line"></div>
	
	<div class="content">
		<ul class="list">
			<?php if(is_array($chargelist)): $i = 0; $__LIST__ = $chargelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li data-price="<?php echo ($v['money']); ?>" data-id="<?php echo ($v['id']); ?>">
				<div class="li-left">
					<img src="/wxshare/Public/share/images/coin.png"> <?php echo ($v['coin']); ?> <?php if($v['give'] > 0): ?><span>赠送<?php echo ($v['give']); ?></span><?php endif; ?>
				</div>
				<div class="li-right">
					<a href="javascript:void(0);" data-price="<?php echo ($v['money']); ?>">
						￥<?php echo ($v['money']); ?>
					</a>
				</div>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
	<div class="footer">
		
	</div>
	
	<script src="/wxshare/Public/share/js/jquery-1.10.1.min.js"></script>
	<script>
		$(function(){
			$(".content .list li").on("click",function(){
				
				var money=$(this).attr("data-price");
				var chargeid=$(this).attr("data-id");
				$.ajax({
					url:'/wxshare/index.php/Share/getOrderId',
					data:{ chargeid: chargeid },
					dataType:'json',
					success:function(data){
						console.log(data);
						if(data.code == 0) {
							location.href='/wxpay/pay/jsapi-wx.php?uid='+data.data.uid+'&money='+data.data.money+'&orderid='+data.data.orderid;
							return !1;
						} else {
							alert(data.msg);
						}
					},
					error:function(e){
						console.log(e);
					}
					
				})
			})
		})
	</script>
</body>
</html>