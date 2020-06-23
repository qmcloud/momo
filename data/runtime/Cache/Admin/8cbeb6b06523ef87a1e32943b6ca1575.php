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
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li ><a href="<?php echo U('Liveing/index');?>">列表</a></li>
			<li class="active"><a >添加</a></li>
		</ul>
		<form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Liveing/add_post');?>">
			<fieldset>
				<div class="control-group">
					<label class="control-label">用户ID</label>
					<div class="controls">
						<input type="text" name="uid" value="<?php echo ($live['uid']); ?>">
						<span class="form-required">*</span>
					</div>
				</div>
                <div class="control-group">
					<label class="control-label">直播分类</label>
					<div class="controls">
						<select name="liveclassid">
						    <option value="0">默认分类</option>
						   <?php if(is_array($liveclass)): $i = 0; $__LIST__ = $liveclass;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>			
							 
						</select>
						<span class="form-required">*</span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">房间类型</label>
					<div class="controls" id="cdn">				
						<label class="radio inline"><input type="radio" value="0" name="type" checked="checked">普通房间</label>
						<label class="radio inline"><input type="radio" value="1" name="type">密码房间</label>
						<label class="radio inline"><input type="radio" value="2" name="type">门票房间</label>
						<label class="radio inline"><input type="radio" value="3" name="type">计时房间</label>
					</div>
				</div>
				<div>
					<div id="cdn_switch_1" class="hide">
						<div class="control-group">
							<label class="control-label">密码或价格</label>
							<div class="controls">				
								<input type="text" name="type_val" value="<?php echo ($live['type_val']); ?>" id="type_val"> 
							</div>
						</div>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">视频地址</label>
					<div class="controls">
						<input type="text" name="pull" value="<?php echo ($live['pull']); ?>">
						<span class="form-required">*</span> 视频格式：MP4 *
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">视频类型</label>
					<div class="controls">				
						<label class="radio inline"><input type="radio" value="1" name="anyway" checked="checked">横屏</label>
						<label class="radio inline"><input type="radio" value="0" name="anyway">竖屏</label>
						
					</div>
				</div>

			</fieldset>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('ADD');?></button>
				<a class="btn" href="<?php echo U('Liveing/index');?>"><?php echo L('BACK');?></a>
			</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script>
	(function(){
		$("#cdn label.radio").on('click',function(){
			var v=$("input",this).val();
			var b=$("#cdn_switch_1");
			if(v==0){
				b.hide();
				$("#type_val").val('');
			}else{
				b.show();
			}
		})
	})()
	</script>
</body>
</html>