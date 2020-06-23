<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="Generator" content="EditPlus®">
    <meta name="Author" content="">
    <meta name="Keywords" content="">
    <meta name="Description" content="">
    <meta name="referrer" content="origin">
    <meta http-equiv="X-UA-Compatible"content="IE=edge">
    <meta content="telephone=no" name="format-detection" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
    <title><?php echo ($config['sitename']); ?></title>
	<link rel="stylesheet" href="/wxshare/Public/share/css/index.css">
    <script>
        function setRemFontSize(baseSize,baseWidth){
            var baseSize = baseSize||20,baseWidth = baseWidth||375,
                clientWidth = document.documentElement.clientWidth<=480?document.documentElement.clientWidth: 480 ;
            document.getElementsByTagName('html')[0].style.fontSize = clientWidth*baseSize/baseWidth+'px'
        }
        setRemFontSize();
        window.addEventListener("resize",function(){
            setTimeout(function(){setRemFontSize();},200)
        });
    </script>
</head>
<body>
	<div class="all-wrap" id="js-all-wrap">
		<div class="down-top" onclick="downurl()">
			<img src="/wxshare/Public/share/images/down2.png">
		</div>
		<div class="bottom-list-container" >
			<div class="tab-con-list slide-page-list" >
				<div class="one-page hots active" id="host-list">
					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="/wxshare/index.php/Share/show?roomnum=<?php echo ($v['uid']); ?>">
						<div class="lists" data-liveid="<?php echo ($v['uid']); ?>" data-url="/wxshare/index.php/Share/show?roomnum=<?php echo ($v['uid']); ?>">
							<div class="l-top" >
								<img src="<?php echo ($v['thumb']); ?>" onerror="this.src='/default.jpg'" class="icon-big" >
								<div class="h-info">
									<span class="fans"><?php echo ($v['user_nicename']); ?></span>
									<span class="live-icon">&nbsp;</span>
								</div>
							</div>
						</div>
					</a><?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
		</div>
	</div> 
	<script>
		var isiPad = /iPad/i.test(navigator.userAgent);
		var isiPhone = /iPhone|iPod/i.test(navigator.userAgent);
		var isAndroid = /Android/i.test(navigator.userAgent);
		var isWeixin = /MicroMessenger/i.test(navigator.userAgent);
		var isQQ = /QQ/i.test(navigator.userAgent);
		var isIOS = (isiPad || isiPhone);
		var isWeibo = /Weibo/i.test(navigator.userAgent);
		var isApp = (isAndroid || isIOS);
		
		function downurl(){
			var href='';
			if(isIOS){
				href='<?php echo ($config['app_ios']); ?>';
			}else{
				href='<?php echo ($config['app_android']); ?>';
			}
			location.href=href;
			return !1;
		}
	</script>
</body>
</html>