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
		
		<form class="well form-search" method="post" action="<?php echo U('Agent/index');?>">

			会员： 
			<input type="text" name="uid" style="width: 200px;" value="<?php echo ($formget["uid"]); ?>" placeholder="请输入会员id...">&nbsp;&nbsp;
			上一级： 
			<input type="text" name="one_uid" style="width: 200px;" value="<?php echo ($formget["one_uid"]); ?>" placeholder="请输入上一级用户id...">
			<input type="submit" class="btn btn-primary" value="搜索">
            
            <br>
            <br>
            <div>
                说明：C用户填写B的邀请码，B用户填写A用户的邀请码，那么C的上一级用户为B，上二级用户为A 
            </div>
		</form>		
		<form method="post" class="js-ajax-form" >

			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">编号</th>
						<th>会员（ID）</th>
						<th>上一级（ID）</th>
						<th>上二级（ID）</th>
						<th>添加时间</th>
					<!-- 	<th align="center"><?php echo L('ACTIONS');?></th> -->
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo ($vo["id"]); ?></td>					
						<td><?php echo ($vo['userinfo']['user_nicename']); ?> ( <?php echo ($vo['uid']); ?> ) </td>
						<td><?php echo ($vo['oneuserinfo']['user_nicename']); ?> ( <?php echo ($vo['one_uid']); ?> ) </td>
						<td><?php echo ($vo['twouserinfo']['user_nicename']); ?> ( <?php echo ($vo['two_uid']); ?> ) </td>

						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>
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