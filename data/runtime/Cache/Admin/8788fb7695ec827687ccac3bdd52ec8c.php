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
			<li><a href="<?php echo U('Liang/add');?>">添加</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Liang/index');?>">
			状态： 
			<select class="select_2" name="status">
				<option value="-1">全部</option>
				<?php if(is_array($status)): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($formget["status"] == $key): ?>selected<?php endif; ?> ><?php echo ($v); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>

			</select> &nbsp;&nbsp;
			位数： 
			<select class="select_2" name="length">
				<option value="-1">全部</option>
				<?php if(is_array($length)): $i = 0; $__LIST__ = $length;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v['length']); ?>" <?php if($formget["length"] == $v['length']): ?>selected<?php endif; ?> ><?php echo ($v['length']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select> &nbsp;&nbsp;
			会员： 
			<input type="text" name="uid" style="width: 200px;" value="<?php echo ($formget["uid"]); ?>" placeholder="请输入会员ID值...">
			
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>	
		<form method="post" class="js-ajax-form" action="<?php echo U('Liang/listorders');?>">
			<div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit"><?php echo L('SORT');?></button>
			</div>
		
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th>排序</th>
						<th align="center">ID</th>
						<th>靓号</th>
						<th>所需点数</th>
						<th>位数</th>
						<th>状态</th>
						<th>发布时间</th>
						<th>购买人</th>
						<th>购买时间</th>
						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
					   <td><input name="listorders[<?php echo ($vo['id']); ?>]" type="text" size="3" value="<?php echo ($vo['orderno']); ?>" class="input input-order"></td>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<td><?php echo ($vo['name']); ?></td>
						<td><?php echo ($vo['coin']); ?></td>
						<td><?php echo ($vo['length']); ?></td>
						<td><?php echo ($status[$vo['status']]); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>
						<?php if($vo['uid'] == '0'): ?><td>未出售</td>
							<td>未出售</td>
						<?php else: ?>
							<td><?php echo ($vo['userinfo']['user_nicename']); ?> (<?php echo ($vo['uid']); ?>)</td>
							<td><?php echo (date("Y-m-d H:i:s",$vo["buytime"])); ?></td><?php endif; ?>

						<td align="center">	
							
							<?php if($vo['status'] != '1'): ?><a href="<?php echo U('Liang/edit',array('id'=>$vo['id']));?>" >编辑</a> 
							| 
							<a href="<?php echo U('Liang/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a><?php endif; ?>
							<?php if($vo['status'] == '0'): ?>| <a href="<?php echo U('Liang/setStatus',array('id'=>$vo['id'],'status'=>'2' ));?>" class="js-ajax-dialog-btn" data-msg="您确定要停售吗？">停售</a>
							<?php elseif($vo['status'] == '2'): ?>
							 | <a href="<?php echo U('Liang/setStatus',array('id'=>$vo['id'],'status'=>'0'));?>" class="js-ajax-dialog-btn" data-msg="您确定要出售吗？">出售</a><?php endif; ?>
							

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