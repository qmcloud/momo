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
			<li class="active"><a >充值记录</a></li>

		</ul>
		<form class="well form-search" name="form1" method="post" style="float:left" action="">
            订单状态：
			<select class="select_2" name="status">
				<option value="">全部</option>
				<option value="1" <?php if($formget["status"] == '1'): ?>selected<?php endif; ?> >已完成</option>
				<option value="0" <?php if($formget["status"] == '0'): ?>selected<?php endif; ?> >未支付</option>			
			</select>
			提交时间：
			<input type="text" name="start_time" class="js-date date" id="start_time" value="<?php echo ($formget["start_time"]); ?>" style="width: 80px;" autocomplete="off">-
			<input type="text" class="js-date date" name="end_time" id="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;
			关键字： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入会员id、订单号...">
			<input type="button" class="btn btn-primary" value="搜索" onclick="form1.action='<?php echo U('Charge/index');?>';form1.submit();"/>
			<input type="button" class="btn btn-primary" style="background-color: #1dccaa;" value="导出" onclick="form1.action='<?php echo U('Charge/export');?>';form1.submit();"/>
			<div style="margin-top:10px">
				人民币金额统计：<?php echo ((isset($moneysum) && ($moneysum !== ""))?($moneysum):0); ?> 元 (根据筛选条件统计)
			</div>		
		</form>	
    	
		<form method="post" class="js-ajax-form" >

		
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th>会员</th>
						<th>人民币金额</th>
						<th>兑换<?php echo ($configpub['name_coin']); ?></th>
						<th>赠送<?php echo ($configpub['name_coin']); ?></th>
						<th>商户订单号</th>
						<th>支付类型</th>
						<th>支付环境</th>
						<th>第三方支付订单号</th>
						<th>订单状态</th>
						<th>提交时间</th>

						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<td><?php echo ($vo['userinfo']['user_nicename']); ?> ( <?php echo ($vo['uid']); ?> )</td>	
						<td><?php echo ($vo['money']); ?></td>
						<td><?php echo ($vo['coin']); ?></td>
						<td><?php echo ($vo['coin_give']); ?></td>
						<td><?php echo ($vo['orderno']); ?></td>
						<td><?php echo ($type[$vo['type']]); ?></td>
						<td><?php echo ($ambient[$vo['type']][$vo['ambient']]); ?></td>
						<td><?php echo ($vo['trade_no']); ?></td>
						<td><?php echo ($status[$vo['status']]); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>
						<td align="center">	
                            <?php if($vo['status'] == 0 && $isshowset == 1): ?><a href="<?php echo U('Charge/setPay',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定确认支付吗？">确认支付</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>