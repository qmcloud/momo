<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:70:"/www/wwwroot/live/public/../application/admin/view/user/user/edit.html";i:1671020443;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta name="referrer" content="never">
<meta name="robots" content="noindex, nofollow">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<?php if(\think\Config::get('fastadmin.adminskin')): ?>
<link href="/assets/css/skins/<?php echo \think\Config::get('fastadmin.adminskin'); ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
<?php endif; ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>

    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav') && \think\Config::get('fastadmin.breadcrumb')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <?php if($auth->check('dashboard')): ?>
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                    <?php endif; ?>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
    <?php echo token(); ?>
    <input type="hidden" name="row[id]" value="<?php echo $row['id']; ?>">
    <div class="form-group">
        <label for="c-group_id" class="control-label col-xs-12 col-sm-2"><?php echo __('Group'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <?php echo $groupList; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="c-username" class="control-label col-xs-12 col-sm-2"><?php echo __('Username'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-username" data-rule="required" class="form-control" name="row[username]" type="text" value="<?php echo htmlentities($row['username']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-nickname" class="control-label col-xs-12 col-sm-2"><?php echo __('Nickname'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-nickname" data-rule="required" class="form-control" name="row[nickname]" type="text" value="<?php echo htmlentities($row['nickname']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-password" class="control-label col-xs-12 col-sm-2"><?php echo __('Password'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-password" data-rule="password" class="form-control" name="row[password]" type="password" value="" placeholder="<?php echo __('Leave password blank if dont want to change'); ?>" autocomplete="new-password" />
        </div>
    </div>
    <div class="form-group">
        <label for="c-email" class="control-label col-xs-12 col-sm-2"><?php echo __('Email'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-email" data-rule="" class="form-control" name="row[email]" type="text" value="<?php echo htmlentities($row['email']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-mobile" class="control-label col-xs-12 col-sm-2"><?php echo __('Mobile'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-mobile" data-rule="" class="form-control" name="row[mobile]" type="text" value="<?php echo htmlentities($row['mobile']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-avatar" class="control-label col-xs-12 col-sm-2"><?php echo __('Avatar'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-avatar" data-rule="" class="form-control" size="50" name="row[avatar]" type="text" value="<?php echo $row['avatar']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="faupload-avatar" class="btn btn-danger faupload" data-input-id="c-avatar" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-avatar"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-avatar" class="btn btn-primary fachoose" data-input-id="c-avatar" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-avatar"></span>
            </div>
            <ul class="row list-inline faupload-preview" id="p-avatar"></ul>
        </div>
    </div>
    <div class="form-group">
        <label for="c-level" class="control-label col-xs-12 col-sm-2"><?php echo __('Level'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-level" data-rule="required" class="form-control" name="row[level]" type="number" value="<?php echo $row['level']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-gender" class="control-label col-xs-12 col-sm-2"><?php echo __('Gender'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[gender]', ['1'=>__('Male'), '0'=>__('Female')], $row['gender']); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="c-birthday" class="control-label col-xs-12 col-sm-2"><?php echo __('Birthday'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-birthday" data-rule="" class="form-control datetimepicker" data-date-format="YYYY-MM-DD" data-use-current="true" name="row[birthday]" type="text" value="<?php echo $row['birthday']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-bio" class="control-label col-xs-12 col-sm-2"><?php echo __('Bio'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bio" data-rule="" class="form-control" name="row[bio]" type="text" value="<?php echo htmlentities($row['bio']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-money" class="control-label col-xs-12 col-sm-2"><?php echo __('Money'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-money" data-rule="required" class="form-control" name="row[money]" type="number" value="<?php echo $row['money']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-score" class="control-label col-xs-12 col-sm-2"><?php echo __('Score'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-score" data-rule="required" class="form-control" name="row[score]" type="number" value="<?php echo $row['score']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-successions" class="control-label col-xs-12 col-sm-2"><?php echo __('Successions'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-successions" data-rule="required" class="form-control" name="row[successions]" type="number" value="<?php echo $row['successions']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-maxsuccessions" class="control-label col-xs-12 col-sm-2"><?php echo __('Maxsuccessions'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-maxsuccessions" data-rule="required" class="form-control" name="row[maxsuccessions]" type="number" value="<?php echo $row['maxsuccessions']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-prevtime" class="control-label col-xs-12 col-sm-2"><?php echo __('Prevtime'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-prevtime" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[prevtime]" type="text" value="<?php echo datetime($row['prevtime']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-logintime" class="control-label col-xs-12 col-sm-2"><?php echo __('Logintime'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-logintime" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[logintime]" type="text" value="<?php echo datetime($row['logintime']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-loginip" class="control-label col-xs-12 col-sm-2"><?php echo __('Loginip'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-loginip" data-rule="required" class="form-control" name="row[loginip]" type="text" value="<?php echo $row['loginip']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-loginfailure" class="control-label col-xs-12 col-sm-2"><?php echo __('Loginfailure'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-loginfailure" data-rule="required" class="form-control" name="row[loginfailure]" type="number" value="<?php echo $row['loginfailure']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-joinip" class="control-label col-xs-12 col-sm-2"><?php echo __('Joinip'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-joinip" data-rule="required" class="form-control" name="row[joinip]" type="text" value="<?php echo $row['joinip']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="c-jointime" class="control-label col-xs-12 col-sm-2"><?php echo __('Jointime'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input id="c-jointime" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[jointime]" type="text" value="<?php echo datetime($row['jointime']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="content" class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[status]', ['normal'=>__('Normal'), 'hidden'=>__('Hidden')], $row['status']); ?>
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-primary btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
