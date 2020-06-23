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
<style type="text/css">
.textArea textarea{
	width:90%;padding:3%;height:80%;margin:0 auto;margin-top:30px;
	margin-left: 2%;
}
.textArea_btn{
	text-align: right;
	margin-top: 30px;
}
.textArea_btn input{
	margin-right: 30px;
}
</style>
</head>
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a >列表</a></li>

		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Video/reportlist');?>">
		  状态：
			<select class="select_2" name="status">
				<option value="">全部</option>
				<option value="0" <?php if($formget["status"] == '0'): ?>selected<?php endif; ?> >处理中</option>
				<option value="1" <?php if($formget["status"] == '1'): ?>selected<?php endif; ?> >已处理</option>			
			</select>
			提交时间：
			<input type="text" name="start_time" class="js-date date" value="<?php echo ($formget["start_time"]); ?>" style="width: 80px;" autocomplete="off">-
			<input type="text" class="js-date date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;
			关键字： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入会员ID">
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>				
		<form method="post" class="js-ajax-form" >

		
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th>举报人</th>						
						<th>被举报人</th>
						<th>被举报视频ID</th>
						<th>举报内容</th>
						<th>状态</th>
						<th>提交时间</th>
						<th>处理时间</th>

						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php $status=array("0"=>"处理中","1"=>"已处理", "2"=>"审核失败"); ?>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<td><?php echo ($vo['userinfo']['user_nicename']); ?> ( <?php echo ($vo['uid']); ?> )</td>	
						<td><?php echo ($vo['touserinfo']['user_nicename']); ?> ( <?php echo ($vo['touid']); ?> )</td>		
						<td><?php echo ($vo['videoid']); ?></td>		
						<td><?php echo nl2br($vo['content']);?></td>				
						<td><?php echo ($status[$vo['status']]); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>						
						<td>
						 <?php if($vo['status'] == '0'): ?>处理中
						 <?php else: ?>
						     <?php echo (date("Y-m-d H:i:s",$vo["uptime"])); endif; ?>						
						 </td>

						<td align="center">
							<a href="javascript:void(0)" onclick="videoListen(<?php echo ($vo['videoid']); ?>)" >观看视频</a> |
							<?php if($vo['status'] == '0'): ?><a href="<?php echo U('Video/setstatus',array('id'=>$vo['id']));?>" >标记处理</a>  |<?php endif; ?>
							
								<a href="javascript:void (0)" onclick="xiajia(<?php echo ($vo['videoid']); ?>)" >下架视频</a>
							|
							<a href="<?php echo U('Video/report_del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a>
						</td>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>

		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script src="/public/layer/layer.js"></script>

	<script type="text/javascript">


		var xiajia_status=0;

		function xiajia(id){
			var p=<?php echo ($p); ?>;

			layer.open({
			  type: 1,
			  title:"是否确定将该视频下架",
			  skin: 'layui-layer-rim', //加上边框
			  area: ['30%', '30%'], //宽高
			  content: '<div class="textArea"><textarea id="xiajia_reason" maxlength="50" placeholder="请输入下架原因,最多50字" /> </div><div class="textArea_btn" ><input type="button" id="xiajia" value="下架" onclick="xiajia_submit('+id+','+p+')" /><input type="button" onclick="layer.closeAll();" value="取消" /></div>'
			});
		}

		function xiajia_submit(id,p){

			var reason=$("#xiajia_reason").val();
			if(xiajia_status==1){
				return;
			}
			xiajia_status=1;
			$.ajax({
				url: '/index.php?g=Admin&m=Video&a=setXiajia',
				type: 'POST',
				dataType: 'json',
				data: {id:id,reason: reason},
				success:function(data){
					var code=data.code;
					if(code!=0){
						layer.msg(data.msg);
						return;
					}

					xiajia_status=0;
					$("#xiajia").attr("disabled",true);
					layer.msg("下架成功",{icon: 1,time:1000},function(){
						layer.closeAll();
						location.href='/index.php?g=Admin&m=Video&a=reportlist&p='+p;
					});
				},
				error:function(e){
					$("#xiajia").attr("disabled",false);
					console.log(e);
				}
			});
			
			
		}
	</script>


	<script type="text/javascript">
		function videoListen(id){
			layer.open({
			  type: 2,
			  title: '观看视频',
			  shadeClose: true,
			  shade: 0.8,
			  area: ['500px', '750px'],
			  content: '/index.php?g=Admin&m=Video&a=video_listen&id='+id
			}); 
		}
	</script>
</body>
</html>