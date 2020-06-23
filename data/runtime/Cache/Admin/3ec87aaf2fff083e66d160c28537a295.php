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
			
			<li class="active"><a >视频添加</a></li>
		</ul>
		<form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Video/add_post');?>">
			<fieldset>
				<div class="control-group">
					<label class="control-label">视频状态</label>
					
						<div class="controls">
							<label class="radio inline" ><input type="radio" name="status" value="1" checked  />通过</label>
							<label class="radio inline" ><input type="radio" name="status" value="2" >不通过</label>
						</div>
					
				</div>
				<div class="control-group">
					<label class="control-label">上下架状态</label>
					<div class="controls">
						<label class="radio inline" ><input type="radio" name="isdel" value="0" checked  />上架</label>
						<label class="radio inline" ><input type="radio" name="isdel" value="1" >下架</label>
					</div>
				</div>

				<div class="control-group" style="display: none;">
					<label class="control-label">所有者</label>
					<div class="controls">
						<label class="radio inline" ><input type="radio" name="owner" value="0"   />管理员</label>
						<label class="radio inline" ><input type="radio" name="owner" value="1"  checked>用户</label>
					</div>
				</div>

				<div class="control-group" id="owner_uid">
					<label class="control-label">视频所有者用户</label>
					<div class="controls">
						<input type="text" name="owner_uid" onkeyup="this.value=this.value.replace(/[^0-9-]+/,'');">
						<span class="form-required">* 请填写用户id</span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">视频标题</label>
					<div class="controls">
						<input type="text" name="title">
						<span class="form-required">*</span>
					</div>
				</div>
				
				
				<div class="control-group">
					<label class="control-label">图片</label>
					<div class="controls">
								<div >
									<input type="hidden" name="thumb" id="thumb2" value="">
									<a href="javascript:void(0);" onclick="flashupload('thumb_images', '附件上传','thumb2',thumb_images,'1,jpg|jpeg|gif|png|bmp,1,,,1','','','');return false;">
										    <img src="/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png" id="thumb2_preview" width="135" style="cursor: hand" />
									</a>
									<input type="button" class="btn btn-small" onclick="$('#thumb2_preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb2').val('');return false;" value="取消图片">
								</div>
						<span class="form-required"></span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">视频上传型式</label>
					<div class="controls">
						<label class="radio inline" ><input type="radio" name="video_upload_type" value="0" checked  />视频链接</label>
						<label class="radio inline" ><input type="radio" name="video_upload_type" value="1" >视频文件</label>
					</div>
				</div>

				<div class="control-group video_url_area">
					<label class="control-label">视频链接</label>
					<div class="controls">
						<input type="text" name="href">
						<span class="form-required">* 以http://或https://开头</span>
					</div>
				</div>

				<div class="control-group upload_video_area" style="display: none;">
					<label class="control-label">上传视频</label>
					<div class="controls">
						<input type="file" name="file" />
						<span class="form-required"></span>
					</div>
				</div>

			</fieldset>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('ADD');?></button>
				<a class="btn" href="<?php echo U('Video/passindex');?>"><?php echo L('BACK');?></a>
			</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script type="text/javascript" src="/public/js/content_addtop.js"></script>
	<script type="text/javascript">
		$(function(){

			$("input[name='video_upload_type']").click(function(){
				var val=$("input[name='video_upload_type']:checked").val();
				console.log(val);
				if(val==0){
					$(".video_url_area").show();
					$(".upload_video_area").hide();
				}else{
					$(".video_url_area").hide();
					$(".upload_video_area").show();
					$("input[name='href']").val('');
				}
			});

			$("input[name='owner']").on("click",function(){
				var val=$(this).val();
				if(val==1){ //所有者为用户
					$("#owner_uid").show();
				}else{
					$("#owner_uid").hide();
				}
			});

		});
	</script>
</body>
</html>