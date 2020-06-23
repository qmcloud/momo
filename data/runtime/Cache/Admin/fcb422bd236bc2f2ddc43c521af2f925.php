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
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="<?php echo U('menu/index');?>"><?php echo L('ADMIN_MENU_INDEX');?></a></li>
			<li><a href="<?php echo U('menu/add');?>"><?php echo L('ADMIN_MENU_ADD');?></a></li>
			<!-- <li><a href="<?php echo U('menu/spmy_import_menu');?>"><?php echo L('ADMIN_MENU_SPMY_IMPORT_MENU');?></a></li> -->
			<!-- <?php if(APP_DEBUG): ?><li><a href="<?php echo U('menu/lists');?>"><?php echo L('ADMIN_MENU_LISTS');?></a></li>
			<li><a href="<?php echo U('menu/spmy_export_menu');?>">备份菜单</a></li>
			<li><a href="<?php echo U('menu/spmy_export_menu_lang');?>">生成菜单多语言包</a></li><?php endif; ?> -->
		</ul>
		<form class="js-ajax-form" action="<?php echo U('Menu/listorders');?>" method="post">
			<div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit"><?php echo L('SORT');?></button>
			</div>
			<table class="table table-hover table-bordered table-list" id="menus-table">
				<thead>
					<tr>
						<th width="80"><?php echo L('SORT');?></th>
						<th width="50">ID</th>
						<th><?php echo L('APP');?></th>
						<th><?php echo L('NAME');?></th>
						<th width="80"><?php echo L('STATUS');?></th>
						<th width="180"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php echo ($categorys); ?>
				</tbody>
				<tfoot>
					<tr>
						<th width="80"><?php echo L('SORT');?></th>
						<th width="50">ID</th>
						<th><?php echo L('APP');?></th>
						<th><?php echo L('NAME');?></th>
						<th width="80"><?php echo L('STATUS');?></th>
						<th width="180"><?php echo L('ACTIONS');?></th>
					</tr>
				</tfoot>
			</table>
			<div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit"><?php echo L('SORT');?></button>
			</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script>
		$(document).ready(function() {
			Wind.css('treeTable');
			Wind.use('treeTable', function() {
				$("#menus-table").treeTable({
					indent : 20
				});
			});
		});

		setInterval(function() {
			var refersh_time = getCookie('refersh_time_admin_menu_index');
			if (refersh_time == 1) {
				reloadPage(window);
			}
		}, 1000);
		setCookie('refersh_time_admin_menu_index', 0);
	</script>
</body>
</html>