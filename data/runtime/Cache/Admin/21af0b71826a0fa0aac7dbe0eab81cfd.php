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
			<li class="active"><a >音乐分类</a></li>
			<li><a href="<?php echo U('Music/classify_add');?>">添加</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Music/classify');?>">

			关键字： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入分类名称">
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>
		<form method="post" class="js-ajax-form" action="<?php echo U('Music/classify_listorders');?>">
			<div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit"><?php echo L('SORT');?></button>
			</div>
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th>序号</th>
						<th align="center">ID</th>
						<th>分类名称</th>
						<th>分类图标</th>
						<th>添加时间</th>
						<th>修改时间</th>
						<th>是否删除</th>
						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php $isdel=array("0"=>"否","1"=>"是"); ?>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td><input name="listorders[<?php echo ($vo['id']); ?>]" type="text" size="3" value="<?php echo ($vo['orderno']); ?>" class="input input-order"></td>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<td><?php echo ($vo['title']); ?></td>
						<td><img src="<?php echo ($vo['img_url']); ?>" width="50px" height="50px"></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>
						<td><?php if($vo['updatetime'] != '0'): echo (date("Y-m-d H:i:s",$vo["updatetime"])); else: ?>--<?php endif; ?></td>
						
						<td><?php echo ($isdel[$vo['isdel']]); ?></td>
						<td align="center">
							<a href="<?php echo U('Music/classify_edit',array('id'=>$vo['id']));?>" >编辑</a>|
							<?php if($vo['isdel'] == '0'): ?><a href="<?php echo U('Music/classify_del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？删除后对应分类下的音乐将不显示">删除</a>
							<?php else: ?>
							<a href="<?php echo U('Music/classify_canceldel',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要取消删除吗？">取消删除</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>
			<div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit"><?php echo L('SORT');?></button>
			</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>