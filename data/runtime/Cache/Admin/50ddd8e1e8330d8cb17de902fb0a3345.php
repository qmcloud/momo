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
			<li class="active"><a >列表</a></li>
		</ul>
		
		<form class="well form-search" method="post" action="<?php echo U('Agent/index2');?>">
			会员： 
			<input type="text" name="uid" style="width: 200px;" value="<?php echo ($formget["uid"]); ?>" placeholder="请输入会员id...">&nbsp;&nbsp;
			<input type="submit" class="btn btn-primary" value="搜索">
            <br>
            <br>
            <div>
                说明：当下级和下二级用户消费后，平台会给用户以奖励，这里将用户的所有下级用户和所有下二级用户的奖励进行统计
            </div>
		</form>		
		<form method="post" class="js-ajax-form" >

			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">编号</th>
						<th>会员（ID）</th>
						<th>一级收益</th>
						<th>二级收益</th>

					<!-- 	<th align="center"><?php echo L('ACTIONS');?></th> -->
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo ($vo["id"]); ?></td>					
						<td><?php echo ($vo['userinfo']['user_nicename']); ?> ( <?php echo ($vo['uid']); ?> ) </td>
						<td><?php echo ($vo['one_profit']); ?> </td>
						<td><?php echo ($vo['two_profit']); ?> </td>
						<!-- <td align="center">	
						 <a href="<?php echo U('Agent/edit',array('id'=>$vo['id']));?>" >编辑</a> | 
							<a href="<?php echo U('Agent/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a>
						</td> -->
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>

		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>