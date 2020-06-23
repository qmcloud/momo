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
</style>
</head>
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a >背景音乐列表</a></li>
			<li><a href="<?php echo U('Music/music_add');?>">添加</a></li>
		</ul>
		
		<form class="well form-search" method="post" action="<?php echo U('Music/index');?>">
			选择分类：
			<select class="select_2" name="classify_id">
				<option value="">全部</option>
				<?php if(is_array($classify)): $i = 0; $__LIST__ = $classify;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>" <?php if($formget["classify_id"] == $vo['id']): ?>selected<?php endif; ?> ><?php echo ($vo['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
					
			</select>

			<!-- 选择上传类型：
			<select class="select_2" name="upload_type">
				<option value="0">全部</option>
				
				<option value="1" <?php if($formget["upload_type"] == 1): ?>selected<?php endif; ?> >管理员上传</option>		
				<option value="2" <?php if($formget["upload_type"] == 2): ?>selected<?php endif; ?> >用户上传</option>
					
			</select> -->
			关键字： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入音乐名称">
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>		
		
		<form method="post" class="js-ajax-form" >
		
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th>音乐名称</th>
						<th>演唱者</th>
						<th>上传类型</th>
						<th>上传者</th>
						<th>封面</th>
						<th>音乐长度</th>
						<th>音乐地址</th>
						<th>使用次数</th>
						<th>是否删除</th>
						<th>所属分类</th>
						<th>添加时间</th>
						<th>修改时间</th>
						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php $upload_type=array("1"=>"管理员","2"=>"用户");$isdel=array("0"=>"否","1"=>"是"); ?>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<td><?php echo ($vo['title']); ?></td>
						<td><?php echo ($vo['author']); ?></td>
						<td><?php echo ($upload_type[$vo['upload_type']]); ?></td>
						<td><?php echo ($vo['uploader_nicename']); ?>(<?php echo ($vo['uploader']); ?>)</td>
						<td><img src="<?php echo ($vo['img_url']); ?>" width="50" height="50"></td>
						<td><?php echo ($vo['length']); ?></td>
						<td style="max-width: 300px;word-break:break-all;"><?php echo ($vo['file_url']); ?></td>
						<td><?php echo ($vo['use_nums']); ?></td>
						<td><?php echo ($isdel[$vo['isdel']]); ?></td>
						<td><?php echo ($vo['classify_title']); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>
						<td><?php if($vo['updatetime'] != '0'): echo (date("Y-m-d H:i:s",$vo["updatetime"])); else: ?>--<?php endif; ?></td>
						<td align="center">
							<a href="javascript:void(0)" onclick="musicListen(<?php echo ($vo['id']); ?>)" >试听</a>
							|
							<a href="<?php echo U('Music/music_edit',array('id'=>$vo['id']));?>" >编辑</a>
							 |
							 <?php if($vo['isdel'] == '0'): ?><a href="<?php echo U('Music/music_del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a>
							 <?php else: ?>
							 <a href="<?php echo U('Music/music_canceldel',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要取消删除吗？">取消删除</a><?php endif; ?>
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
		function musicListen(id){
			layer.open({
			  type: 2,
			  title: '音乐试听',
			  shadeClose: true,
			  shade: 0.8,
			  area: ['500px', '140px'],
			  content: 'Admin/Music/music_listen/id/'+id
			}); 
		}
	</script>
</body>
</html>