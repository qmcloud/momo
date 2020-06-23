<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>國際熱女孩 <?php echo L('ADMIN_CENTER');?></title>
		<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge" />
		<meta name="renderer" content="webkit|ie-comp|ie-stand">
		<meta name="robots" content="noindex,nofollow">
		<link href="/admin/themes/simplebootx/Public/assets/css/admin_login2.css" rel="stylesheet" />
		<script>
			if (window.parent !== window.self) {
					document.write = '';
					window.parent.location.href = window.self.location.href;
					setTimeout(function () {
							document.body.innerHTML = '';
					}, 0);
			}
		</script>
		
	</head>
<body>
    <div class="wraps">
        <div class="wraps-1">
            <div class="wraps-1-1"><img src="/admin/themes/simplebootx/Public/assets/images/wel.png"></div>
            <div class="wraps-1-2"><img src="/admin/themes/simplebootx/Public/assets/images/log.png"></div>
            <div class="wraps-1-3">
                <!-- <div class="wraps-1-4">开发：泰安金钱豹网络科技有限公司</div> -->
                <!-- <div class="wraps-1-4">官网：www.yunbaokj.com</div> -->
                <!-- <div class="wraps-1-4">咨询电话：0538-8270220</div> -->
            </div>
        </div>
        <div class="wraps-2">
            <form method="post" name="login" action="<?php echo U('public/dologin');?>" autoComplete="off" class="js-ajax-form">
                <div class="wraps-2-1">
                    <img class="img" src="/admin/themes/simplebootx/Public/assets/images/user.jpg">
                    <input class="input1" id="js-admin-name" type="text" name="username" required placeholder="<?php echo L('USERNAME_OR_EMAIL');?>" title="<?php echo L('USERNAME_OR_EMAIL');?>" value="<?php echo ($_COOKIE['admin_username']); ?>">
                    <div class="line"></div>
                    <img class="img" src="/admin/themes/simplebootx/Public/assets/images/pwd.jpg">
                    <input class="input1" id="admin_pwd" type="password" name="password" required placeholder="<?php echo L('PASSWORD');?>" title="<?php echo L('PASSWORD');?>">
                    <div class="line"></div>
                    <img class="img" src="/admin/themes/simplebootx/Public/assets/images/code.jpg">
                    <span class="code"><?php echo sp_verifycode_img('length=4&font_size=15&width=100&height=42&use_noise=1&use_curve=0','style="cursor: pointer;" title="点击获取"');?></span>
                    <input class="input2" type="text" name="verify" placeholder="<?php echo L('ENTER_VERIFY_CODE');?>" />
                    <div class="line"></div>
					<div  id="login_btn_wraper">
                        <button type="submit" id="sub" name="submit" class="btn js-ajax-submit" ><?php echo L('LOGIN');?></button>
                        <div data-loadingmsg="<?php echo L('LOADING');?>" id="msg"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
	</div>

<script>
var GV = {
	DIMAUB: "",
	JS_ROOT: "/public/js/",//js版本号
	TOKEN : ''	//token ajax全局
};
</script>
<script src="/public/js/wind.js"></script>
<script src="/public/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/common.js"></script>
<script>
;(function(){
	document.getElementById('js-admin-name').focus();
})();
</script>
</body>
</html>