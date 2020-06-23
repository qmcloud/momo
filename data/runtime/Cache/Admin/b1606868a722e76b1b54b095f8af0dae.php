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
			<li class="active"><a >直播记录</a></li>
			<li><a href="<?php echo U('Liveing/add');?>">添加视频</a></li>
		</ul>
		
		<form class="well form-search" method="post" action="<?php echo U('Liveing/index');?>">
			时间：
			<input type="text" name="start_time" class="js-date date" value="<?php echo ($formget["start_time"]); ?>" style="width: 80px;" autocomplete="off">-
			<input type="text" class="js-date date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;
			关键字： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入会员id...">
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>		
		<form method="post" class="js-ajax-form" >

			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">编号</th>
						<th>会员昵称</th>
						<th>直播ID</th>
						<th>直播状态</th>
						<th>直播开始时间</th>
						<th>直播分类</th>
						<th>房间类型</th>
						<th>密码/价格</th>
						<th>视频类型</th>
						<th>在线人数</th>
						<th>本场收益</th>
						<th>打赏人数</th>
						<th>人均打赏</th>
						<th>播流地址</th>
						<th>设备信息</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php $islive=array("0"=>"直播结束","1"=>"直播中");$type=array("0"=>"普通房间","1"=>"密码房间","2"=>"门票房间","3"=>"计时房间"); ?>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo ($vo["uid"]); ?></td>					
						<td><?php echo ($vo['userinfo']['user_nicename']); ?> </td>
						<td><?php echo ($vo['showid']); ?></td>
						<td><?php echo ($islive[$vo['islive']]); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["starttime"])); ?></td>
						<td><?php echo ($liveclass[$vo['liveclassid']]); ?></td>
						<td><?php echo ($type[$vo['type']]); ?></td>
						<td><?php echo ($vo['type_val']); ?></td>
						<td><?php if($vo['anyway'] == 0): ?>竖屏<?php else: ?>横屏<?php endif; ?></td>
                        <td><?php echo ($vo['nums']); ?></td>
                        <td><?php echo ($vo['totalcoin']); ?></td>
                        <td><?php echo ($vo['total_nums']); ?></td>
                        <td><?php echo ($vo['total_average']); ?></td>
                        <td><?php echo ($vo['pull']); ?></td>
                        <td><?php echo ($vo['deviceinfo']); ?></td>
						<?php if($vo['isvideo']==1): ?><td>
							<a href="<?php echo U('Liveing/edit',array('uid'=>$vo['uid']));?>" >编辑</a>
							 |
							<a href="<?php echo U('Liveing/del',array('uid'=>$vo['uid']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a>
						</td>
						<?php else: ?>
                        <td></td><?php endif; ?>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>

		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>