<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:78:"/www/wwwroot/live/public/../application/admin/view/general/attachment/add.html";i:1671020443;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
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
                                <form id="add-form" class="form-horizontal form-ajax" role="form" data-toggle="validator" method="POST" action="">
    <?php if($config['upload']['cdnurl']): ?>
    <div class="form-group">
        <label for="c-third" class="control-label col-xs-12 col-sm-2"><?php echo __('Upload to third'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" name="row[third]" id="c-third" class="form-control"/>
            <ul class="row list-inline faupload-preview" id="p-third"></ul>
        </div>
    </div>

    <div class="form-group">
        <label for="c-third" class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <div style="width:180px;display:inline-block;">
                <select name="category-third" id="category-third" class="form-control selectpicker">
                    <option value=""><?php echo __('Please select category'); ?></option>
                    <?php if(is_array($categoryList) || $categoryList instanceof \think\Collection || $categoryList instanceof \think\Paginator): if( count($categoryList)==0 ) : echo "" ;else: foreach($categoryList as $key=>$item): ?>
                    <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
            <button type="button" id="faupload-third" class="btn btn-danger faupload" data-multiple="true" data-input-id="c-third" data-preview-id="p-third"><i class="fa fa-upload"></i> <?php echo __("Upload to third"); ?></button>
            <?php if($config['upload']['chunking']): ?>
            <button type="button" id="faupload-third-chunking" class="btn btn-danger faupload" data-chunking="true" data-maxsize="1gb" data-multiple="true" data-input-id="c-third" data-preview-id="p-third"><i class="fa fa-upload"></i> <?php echo __("Upload to third by chunk"); ?></button>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="c-local" class="control-label col-xs-12 col-sm-2"><?php echo __('Upload to local'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" name="row[local]" id="c-local" class="form-control"/>
            <ul class="row list-inline faupload-preview" id="p-local"></ul>
        </div>
    </div>

    <div class="form-group">
        <label for="c-local" class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <div style="width:180px;display:inline-block;">
                <select name="category-local" id="category-local" class="form-control selectpicker">
                    <option value=""><?php echo __('Please select category'); ?></option>
                    <?php if(is_array($categoryList) || $categoryList instanceof \think\Collection || $categoryList instanceof \think\Paginator): if( count($categoryList)==0 ) : echo "" ;else: foreach($categoryList as $key=>$item): ?>
                    <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
            <button type="button" id="faupload-local" class="btn btn-primary faupload" data-input-id="c-local" data-multiple="true" data-preview-id="p-local" data-url="<?php echo url('ajax/upload'); ?>"><i class="fa fa-upload"></i> <?php echo __("Upload to local"); ?></button>
            <?php if($config['upload']['chunking']): ?>
            <button type="button" id="faupload-local-chunking" class="btn btn-primary faupload" data-chunking="true" data-maxsize="1gb" data-input-id="c-local" data-multiple="true" data-preview-id="p-local" data-url="<?php echo url('ajax/upload'); ?>"><i class="fa fa-upload"></i> <?php echo __("Upload to local by chunk"); ?></button>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group hidden layer-footer">
        <div class="col-xs-2"></div>
        <div class="col-xs-12 col-sm-8">
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
