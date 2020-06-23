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
			<li class="active"><a><?php echo L('USER_INDEXADMIN_INDEX');?></a></li>
			<li ><a href="<?php echo U('indexadmin/add');?>">新增会员</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('indexadmin/index');?>">
			僵尸粉开关： 
			<select class="select_2" name="iszombie">
				<option value="">全部</option>
				<option value="1" <?php if($formget["iszombie"] == '1'): ?>selected<?php endif; ?> >开启</option>
				<option value="0" <?php if($formget["iszombie"] == '0'): ?>selected<?php endif; ?> >关闭</option>			
			</select> &nbsp;&nbsp;
			僵尸粉： 
			<select class="select_2" name="iszombiep">
				<option value="">全部</option>
				<option value="1" <?php if($formget["iszombiep"] == '1'): ?>selected<?php endif; ?> >是</option>
				<option value="0" <?php if($formget["iszombiep"] == '0'): ?>selected<?php endif; ?> >否</option>
	
			</select> &nbsp;&nbsp;	
			禁用： 
			<select class="select_2" name="isban">
				<option value="">全部</option>
				<option value="0" <?php if($formget["isban"] == '0'): ?>selected<?php endif; ?> >是</option>
				<option value="1" <?php if($formget["isban"] == '1'): ?>selected<?php endif; ?> >否</option>
			</select> &nbsp;&nbsp;	
			热门： 
			<select class="select_2" name="ishot">
				<option value="">全部</option>
				<option value="1" <?php if($formget["ishot"] == '1'): ?>selected<?php endif; ?> >是</option>
				<option value="0" <?php if($formget["ishot"] == '0'): ?>selected<?php endif; ?> >否</option>
			</select> &nbsp;&nbsp;	
			超管： 
			<select class="select_2" name="issuper">
				<option value="">全部</option>
				<option value="1" <?php if($formget["issuper"] == '1'): ?>selected<?php endif; ?> >是</option>
				<option value="0" <?php if($formget["issuper"] == '0'): ?>selected<?php endif; ?> >否</option>
			</select> &nbsp;&nbsp;
            <br>设备来源： 
			<select class="select_2" name="source">
				<option value="">全部</option>
				<option value="pc" <?php if($formget["source"] == 'pc'): ?>selected<?php endif; ?> >PC</option>
				<option value="android" <?php if($formget["source"] == 'android'): ?>selected<?php endif; ?> >安卓APP</option>
				<option value="ios" <?php if($formget["source"] == 'ios'): ?>selected<?php endif; ?> >苹果APP</option>
			</select> &nbsp;&nbsp;
			注册时间：
			<input type="text" name="start_time" class="js-date date" value="<?php echo ($formget["start_time"]); ?>" style="width: 80px;" autocomplete="off">-
			<input type="text" class="js-date date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;
			关键字： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入会员id、用户名或者昵称...">
			<input type="submit" class="btn btn-primary" value="搜索">
            <div>
                <br>
                会员数量： <?php echo ($count); ?>
            </div>
		</form>
        
		<form method="post" class="js-ajax-form" >
			<div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('indexadmin/zombiepbatch',array('iszombiep'=>'1'));?>" data-subcheck="true">批量设置为僵尸粉</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('indexadmin/zombiepbatch',array('iszombiep'=>'0'));?>" data-subcheck="true">批量取消僵尸粉</button>
				
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('indexadmin/zombieall',array('iszombie'=>'1'));?>" >一键开启僵尸粉</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('indexadmin/zombieall',array('iszombie'=>'0'));?>" >一键关闭僵尸粉</button>
				<!-- <button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('indexadmin/recordall',array('isrecord'=>'1'));?>" >一键开启录播</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('indexadmin/recordall',array('isrecord'=>'0'));?>" >一键关闭录播</button> -->
			</div>
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th>
						<th align="center">ID</th>
						<?php if($_SESSION["ADMIN_ID"] == '1'){ ?>
						<th><?php echo L('USERNAME');?></th>
						<?php } ?>
						<th><?php echo L('NICENAME');?></th>
						<th><?php echo L('AVATAR');?></th>
						<!-- <th><?php echo L('EMAIL');?></th> -->
						<th>余额</th>
						<th>累计消费</th>
						<th>映票</th>
						<th>累计映票</th>
						<th>邀请码</th>
						<th>注册设备</th>
						<th><?php echo L('REGISTRATION_TIME');?></th>
						<th><?php echo L('LAST_LOGIN_TIME');?></th>
						<th><?php echo L('LAST_LOGIN_IP');?></th>
						<th><?php echo L('STATUS');?></th>
						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php $user_statuses=array("0"=>L('USER_STATUS_BLOCKED'),"1"=>L('USER_STATUS_ACTIVATED'),"2"=>L('USER_STATUS_UNVERIFIED')); ?>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($vo["id"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<?php if($_SESSION["ADMIN_ID"] == '1'){ ?>
						<td><?php echo ($vo['user_login']?$vo['user_login']:L('THIRD_PARTY_USER')); ?></td>
						<?php } ?>
						<td><?php echo ($vo['user_nicename']?$vo['user_nicename']:L('NOT_FILLED')); ?></td>
						<td><img width="25" height="25" src="<?php echo ($vo['avatar']); ?>" /></td>
						<!-- <td><?php echo ($vo["user_email"]); ?></td> -->
						<td><?php echo ($vo["coin"]); ?></td>
						<td><?php echo ($vo["consumption"]); ?></td>
						<td><?php echo ($vo["votes"]); ?></td>
						<td><?php echo ($vo["votestotal"]); ?></td>
						<td><?php echo ($vo["code"]); ?></td>
						<td><?php echo ($vo["source"]); ?></td>
						<td><?php echo ($vo["create_time"]); ?></td>
						<td><?php echo ($vo["last_login_time"]); ?></td>
						<td><?php echo ($vo["last_login_ip"]); ?></td>
						<td><?php echo ($user_statuses[$vo['user_status']]); ?></td>
						<td align="center">
								<?php if($vo["user_status"] == '1'): ?><a href="<?php echo U('indexadmin/ban',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="<?php echo L('BLOCK_USER_CONFIRM_MESSAGE');?>">禁用</a> |
								<?php else: ?>							
										<a href="<?php echo U('indexadmin/cancelban',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="<?php echo L('ACTIVATE_USER_CONFIRM_MESSAGE');?>"><?php echo L('ACTIVATE_USER');?></a> |<?php endif; ?>		
								<?php if($vo["issuper"] == '1'): ?><a href="<?php echo U('indexadmin/cancelsuper',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要取消超管吗？">取消超管</a> |		
								<?php else: ?>							
									<a href="<?php echo U('indexadmin/super',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要设置超管吗？">设置超管</a> |<?php endif; ?>
								<?php if($vo["ishot"] == '1'): ?><a href="<?php echo U('indexadmin/cancelhot',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要取消热门吗？">取消热门</a> |		
								<?php else: ?>							
									<a href="<?php echo U('indexadmin/hot',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要设置热门吗？">热门</a> |<?php endif; ?>		
								<?php if($vo["isrecommend"] == '1'): ?><a href="<?php echo U('indexadmin/cancelrecommend',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要取消推荐吗？">取消推荐</a> |		
								<?php else: ?>							
									<a href="<?php echo U('indexadmin/recommend',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要推荐此用户吗？">推荐</a> |<?php endif; ?>			
								<?php if($vo["iszombie"] == '1'): ?><a href="<?php echo U('indexadmin/cancelzombie',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要关闭僵尸粉吗？">关闭僵尸粉</a> |		
								<?php else: ?>							
									<a href="<?php echo U('indexadmin/zombie',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要开启僵尸粉吗？">开启僵尸粉</a> |<?php endif; ?>	
								<?php if($vo["iszombiep"] == '1'): ?><a href="<?php echo U('indexadmin/cancelzombiep',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要取消设置僵尸粉吗？">取消设置僵尸粉</a> |		
								<?php else: ?>							
									<a href="<?php echo U('indexadmin/zombiep',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要设置为僵尸粉吗？">设置为僵尸粉</a> |<?php endif; ?>	
								<!-- <?php if($vo["isrecord"] == '1'): ?><a href="<?php echo U('indexadmin/cancelrecord',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要关闭录播吗？">关闭录播</a> |		
								<?php else: ?>							
									<a href="<?php echo U('indexadmin/record',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要开启录播吗？">开启录播</a> |<?php endif; ?>	 -->	
								<a href="<?php echo U('indexadmin/edit',array('id'=>$vo['id']));?>">编辑</a> | 
								<a href="<?php echo U('indexadmin/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除此用户吗？">删除</a>
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