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
<style>
.table img{
	max-width:100px;
	max-height:100px;
}
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
			<li class="active"><a >下架视频列表</a></li>
			
		</ul>
		
		<form class="well form-search" method="post" action="<?php echo U('Video/lowervideo');?>">
			排序：
			<select class="select_2" name="ordertype">
				<option value="">默认</option>
				<option value="1" <?php if($formget["ordertype"] == '1'): ?>selected<?php endif; ?> >评论数排序</option>
				<option value="2" <?php if($formget["ordertype"] == '2'): ?>selected<?php endif; ?> >点赞数排序</option>			
				<option value="3" <?php if($formget["ordertype"] == '3'): ?>selected<?php endif; ?> >分享数排序</option>			
			</select>
			
			
			关键字： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入视频ID、会员ID">
			<input type="text" name="keyword1" style="width: 200px;" value="<?php echo ($formget["keyword1"]); ?>" placeholder="请输入视频标题">
			<input type="text" name="keyword2" style="width: 200px;" value="<?php echo ($formget["keyword2"]); ?>" placeholder="请输入用户名称">
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>		
		
		<form method="post" class="js-ajax-form">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th>会员昵称（ID）</th>
						<th style="max-width: 300px;">标题</th>
						<th>图片</th>
						<th>点赞数</th>
						<th>评论数</th>
						<th>分享数</th>
						<!-- <th>催更单价</th>
						<th>已催更金额</th> -->
						<th>视频状态</th>
						<th>发布时间</th>
						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php $isdel=array("0"=>"上架","1"=>"下架");$status=array("0"=>"待审核","1"=>"通过","2"=>"不通过"); ?>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<td><?php echo ($vo['userinfo']['user_nicename']); ?> (<?php echo ($vo['uid']); ?>)</td>
						<td style="max-width: 300px;"><?php echo ($vo['title']); ?></td>
						<td><img src="<?php echo ($vo['thumb']); ?>" /></td>
						<td><?php echo ($vo['likes']); ?></td>
						<td><?php echo ($vo['comments']); ?></td>
						<td><?php echo ($vo['shares']); ?></td>
						<!-- <td><?php echo ($vo['urge_money']); ?></td>
						<td><?php echo ($vo['hasurgemoney']); ?></td> -->
						<td><?php echo ($status[$vo['status']]); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>
						<td align="center">	
							<a href="javascript:void(0)" onclick="videoListen(<?php echo ($vo['id']); ?>)" >观看</a>
							|
							<a href="<?php echo U('Video/edit',array('id'=>$vo['id'],'from'=>'lower'));?>" >编辑</a>
							|
							 <?php if($vo['isdel'] == '1'): ?><a href="<?php echo U('Video/set_shangjia',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要将该视频上架吗？">上架</a>
							 |<?php endif; ?>
							<!-- <a href="<?php echo U('Video/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a> -->
							<a href="javascript:void (0)" onclick="del(<?php echo ($vo['id']); ?>)" >删除</a>
							
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


		
		var del_status=0;

		
		function del(id){
			var p=<?php echo ($p); ?>;

			layer.open({
			  type: 1,
			  title:"是否确定将该视频删除",
			  skin: 'layui-layer-rim', //加上边框
			  area: ['30%', '30%'], //宽高
			  content: '<div class="textArea"><textarea id="del_reason" maxlength="50" placeholder="请输入删除原因,最多50字" /> </div><div class="textArea_btn" ><input type="button" id="delete" value="删除" onclick="del_submit('+id+','+p+')" /><input type="button" id="cancel" onclick="layer.closeAll();" value="取消" /></div>'
			});
		}

		function del_submit(id,p){

			var reason=$("#del_reason").val();

			if(del_status==1){
				return;
			}

			del_status=1;

			$.ajax({
				url: '/index.php?g=Admin&m=Video&a=del',
				type: 'POST',
				dataType: 'json',
				data: {id:id,reason: reason},
				success:function(data){
					var code=data.code;
					if(code!=0){
						layer.msg(data.msg);
						return;
					}

					del_status=0;
					//设置按钮不可用
					$("#delete").attr("disabled",true);
					$("#cancel").attr("disabled",true);

					layer.msg("删除成功",{icon: 1,time:1000},function(){
						layer.closeAll();
						location.reload();
					});
				},
				error:function(e){
					$("#delete").attr("disabled",false);
					$("#cancel").attr("disabled",false);

					console.log(e);
				}
			});
			
			
		}

		/*获取视频评论列表*/
		function commentlists(videoid){
			layer.open({
				type: 2,
				title: '视频评论列表',
				shadeClose: true,
				shade: 0.8,
				area: ['60%', '90%'],
				content: '/index.php?g=Admin&m=Video&a=commentlists&videoid='+videoid 
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