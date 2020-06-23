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
			<li class="active"><a >礼物列表</a></li>
			<li><a href="<?php echo U('Gift/add');?>">礼物添加</a></li>
		</ul>
		<form method="post" class="js-ajax-form" action="<?php echo U('Gift/listorders');?>">
			<div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit"><?php echo L('SORT');?></button>
			</div>
		
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
					  	<th>排序</th>
						<th align="center">ID</th>
						<th>类型</th>
						<!-- <th>分类</th> -->
						<th>标识</th>
						<th>名称</th>
						<th>所需点数</th>
						<!-- <th>礼物小图 （25 X 25）</th> -->
						<th>图片</th>
						<th>动画类型</th>
						<th>动画</th>
						<th>动画时长</th>
						<th>发布时间</th>

						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
					   <td><input name="listorders[<?php echo ($vo['id']); ?>]" type="text" size="3" value="<?php echo ($vo['orderno']); ?>" class="input input-order"></td>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<td><?php echo ($type[$vo['type']]); ?></td>
						<!-- <td><?php echo ($gift_sort[$vo['sid']]); ?></td> -->
						<td><?php echo ($mark[$vo['mark']]); ?></td>
						<td><?php echo ($vo['giftname']); ?></td>
						<td><?php echo ($vo['needcoin']); ?></td>
						<!-- <td><img width="25" height="25" src="<?php echo ($vo['gifticon_mini']); ?>" /></td> -->
						<td><img width="25" height="25" src="<?php echo ($vo['gifticon']); ?>" /></td>
						<td><?php if($vo['type'] == 1): echo ($swftype[$vo['swftype']]); endif; ?></td>
						<td><?php if($vo['swf']): if($vo['swftype'] == 1): echo ($vo['swf']); ?>
                                <?php else: ?>
                                    <img width="100" height="100" src="<?php echo ($vo['swf']); ?>" /><?php endif; endif; ?>
                        </td>
                        <td><?php echo ($vo['swftime']); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>

						<td align="center">	
                            <?php if($vo['type'] == 0 && $vo['mark'] == 3): ?><a href="<?php echo U('Luckrate/index',array('giftid'=>$vo['id']));?>" >中奖设置</a>
							 |
                             <a href="<?php echo U('Jackpotrate/index',array('giftid'=>$vo['id']));?>" >奖池设置</a>
							 |<?php endif; ?>
							<a href="<?php echo U('Gift/edit',array('id'=>$vo['id']));?>" >编辑</a>
							 |
                            <a href="<?php echo U('Gift/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a>
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