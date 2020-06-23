<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta name="referrer" content="origin">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/public/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/public/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/public/simpleboot/font-awesome/4.7.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
		.length_3{width: 180px;}
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
<script type="text/javascript">
//全局变量
var GV = {
    DIMAUB: "/",
    JS_ROOT: "public/js/",
    TOKEN: ""
};
</script>
<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/public/js/jquery.js"></script>
    <script src="/public/js/wind.js"></script>
    <script src="/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>
</head>
<body>
<style>
input{
  width:500px;
}
.form-horizontal textarea{
 width:500px;
}
.nav-tabs>.current>a{
    color: #95a5a6;
    cursor: default;
    background-color: #fff;
    border: 1px solid #ddd;
    border-bottom-color: transparent;
}
.nav li
{
	cursor:pointer
}
.nav li:hover
{
	cursor:pointer
}
.hide{
	display:none;
}
</style>


	<div class="wrap js-check-wrap">

		<ul class="nav nav-tabs">
			<li class="active"><a>配置</a></li>
            <li><a href="<?php echo U('Guide/index');?>">管理</a></li>
		</ul>
		
		<form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Guide/set_post');?>">
			<div class="js-tabs-content">
				<!-- 网站信息 -->
				<div>
					<fieldset>
						<div class="control-group">
							<label class="control-label">引导页开关</label>
							<div class="controls">				
								<label class="radio inline"><input type="radio" value="0" name="post[switch]" <?php if(($config['switch']) == "0"): ?>checked="checked"<?php endif; ?>>关闭</label>
								<label class="radio inline"><input type="radio" value="1" name="post[switch]" <?php if(($config['switch']) == "1"): ?>checked="checked"<?php endif; ?>>开启</label>
								<label class="checkbox inline"></label>
							</div>
						</div>
                        <div class="control-group">
							<label class="control-label">引导页类型</label>
							<div class="controls" id="type">				
								<label class="radio inline"><input type="radio" value="0" name="post[type]" <?php if(($config['type']) == "0"): ?>checked="checked"<?php endif; ?>>图片</label>
								<label class="radio inline"><input type="radio" value="1" name="post[type]" <?php if(($config['type']) == "1"): ?>checked="checked"<?php endif; ?>>视频</label>
								<label class="checkbox inline"></label>
							</div>
						</div>

						<div class="control-group" id="type_0" <?php if($config['type'] == 1): ?>style="display:none;"<?php endif; ?> >
							<label class="control-label">图片展示时间</label>
							<div class="controls">				
								<input type="text" name="post[time]" value="<?php echo ($config['time']); ?>">秒 当类型选择图片时，每张图片的展示时间
							</div>
						</div>
					</fieldset>
				</div>

			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('SAVE');?></button>
			</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
    <script>
    (function(){
        $("#type label.radio").on('click',function(){
            var v=$("input",this).val();
            if(v==1){
                $("#type_0").hide();
            }else{
                $("#type_0").show();
            }
            
        })
        
    })()  
    </script>
</body>
</html>