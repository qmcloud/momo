<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:67:"/www/wwwroot/live/public/../application/admin/view/portal/edit.html";i:1685038727;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
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

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Parent_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-parent_id" data-rule="required" min="0" data-source="parent/index" class="form-control selectpage" name="row[parent_id]" type="text" value="<?php echo htmlentities($row['parent_id']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_type" data-rule="required" min="0" class="form-control" name="row[post_type]" type="number" value="<?php echo htmlentities($row['post_type']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_format'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_format" data-rule="required" min="0" class="form-control" name="row[post_format]" type="number" value="<?php echo htmlentities($row['post_format']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('User_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-user_id" data-rule="required" min="0" data-source="user/user/index" data-field="nickname" class="form-control selectpage" name="row[user_id]" type="text" value="<?php echo htmlentities($row['user_id']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_status" data-rule="required" min="0" class="form-control" name="row[post_status]" type="number" value="<?php echo htmlentities($row['post_status']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Comment_status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-comment_status" data-rule="required" min="0" class="form-control" name="row[comment_status]" type="number" value="<?php echo htmlentities($row['comment_status']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Is_top'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-is_top" data-rule="required" min="0" class="form-control" name="row[is_top]" type="number" value="<?php echo htmlentities($row['is_top']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Recommended'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-recommended" data-rule="required" min="0" class="form-control" name="row[recommended]" type="number" value="<?php echo htmlentities($row['recommended']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_hits'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_hits" data-rule="required" min="0" class="form-control" name="row[post_hits]" type="number" value="<?php echo htmlentities($row['post_hits']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_favorites'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_favorites" data-rule="required" min="0" class="form-control" name="row[post_favorites]" type="number" value="<?php echo htmlentities($row['post_favorites']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_like'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_like" data-rule="required" min="0" class="form-control" name="row[post_like]" type="number" value="<?php echo htmlentities($row['post_like']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Comment_count'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-comment_count" data-rule="required" min="0" class="form-control" name="row[comment_count]" type="number" value="<?php echo htmlentities($row['comment_count']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Create_time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-create_time" data-rule="required" min="0" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[create_time]" type="text" value="<?php echo $row['create_time']?datetime($row['create_time']):''; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Update_time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-update_time" data-rule="required" min="0" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[update_time]" type="text" value="<?php echo $row['update_time']?datetime($row['update_time']):''; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Published_time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-published_time" data-rule="required" min="0" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[published_time]" type="text" value="<?php echo $row['published_time']?datetime($row['published_time']):''; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Delete_time'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-delete_time" data-rule="required" min="0" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[delete_time]" type="text" value="<?php echo $row['delete_time']?datetime($row['delete_time']):''; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_title'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_title" data-rule="required" class="form-control" name="row[post_title]" type="text" value="<?php echo htmlentities($row['post_title']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_keywords'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_keywords" data-rule="required" class="form-control" name="row[post_keywords]" type="text" value="<?php echo htmlentities($row['post_keywords']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_excerpt'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_excerpt" data-rule="required" class="form-control" name="row[post_excerpt]" type="text" value="<?php echo htmlentities($row['post_excerpt']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_source'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-post_source" data-rule="required" class="form-control" name="row[post_source]" type="text" value="<?php echo htmlentities($row['post_source']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Thumbnail'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-thumbnail" data-rule="required" class="form-control" name="row[thumbnail]" type="text" value="<?php echo htmlentities($row['thumbnail']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_content'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-post_content" class="form-control editor" rows="5" name="row[post_content]" cols="50"><?php echo htmlentities($row['post_content']); ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Post_content_filtered'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-post_content_filtered" class="form-control " rows="5" name="row[post_content_filtered]" cols="50"><?php echo htmlentities($row['post_content_filtered']); ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('More'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-more" class="form-control " rows="5" name="row[more]" cols="50"><?php echo htmlentities($row['more']); ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-type" data-rule="required" class="form-control" name="row[type]" type="number" value="<?php echo htmlentities($row['type']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('List_order'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-list_order" data-rule="required" class="form-control" name="row[list_order]" type="number" value="<?php echo htmlentities($row['list_order']); ?>">
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
