<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:67:"/www/wwwroot/live/public/../application/admin/view/addon/index.html";i:1671020443;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
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
                                <style type="text/css">
    .layui-layer-pay .layui-layer-content {
        padding: 0;
        height: 600px !important;
    }

    .layui-layer-pay {
        border: none;
    }

    .payimg {
        position: relative;
        width: 800px;
        height: 600px;
    }

    .payimg .alipaycode {
        position: absolute;
        left: 265px;
        top: 442px;
    }

    .payimg .wechatcode {
        position: absolute;
        left: 660px;
        top: 442px;
    }

    .thumbnail img {
        width: 100%;
    }

    .fixed-table-toolbar .pull-right.search {
        min-width: 300px;
    }

    a.title {
        color: #444;
    }

    .releasetips {
        position: relative;
    }

    .releasetips i {
        display: block;
        background: #f00;
        border-radius: 50%;
        width: 0.3em;
        height: 0.3em;
        top: 0px;
        right: -8px;
        position: absolute;
        box-shadow: 0px 0px 2px #f11414;
    }

    .form-userinfo .breadcrumb {
        margin-bottom: 10px;
    }

    .btn-toggle {
        padding: 0;
    }

</style>
<div class="panel panel-default panel-intro">
    <div class="panel-heading">
        <?php echo build_heading(null,FALSE); if(\think\Config::get('fastadmin.api_url')): ?>
        <ul class="nav nav-tabs nav-category">
            <li class="active"><a href="javascript:;" data-id=""><?php echo __('All'); ?></a></li>
            <li><a href="javascript:;" data-id="0"><?php echo __('Uncategoried'); ?></a></li>
        </ul>
        <?php endif; ?>
    </div>

    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding">
                    <div id="toolbar" class="toolbar">
                        <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" data-force-refresh="false"><i class="fa fa-refresh"></i> </a>
                        <?php if(\think\Config::get('fastadmin.api_url')): ?>
                        <button type="button" id="faupload-addon" class="btn btn-danger faupload btn-mini-xs" data-url="addon/local" data-chunking="false" data-mimetype="zip,fastaddon" data-multiple="false"><i class="fa fa-upload"></i>
                            <?php echo __('Local install'); ?>
                        </button>
                        <div class="btn-group">
                            <a href="#" class="btn btn-info btn-switch active btn-mini-xs" data-type="all"><i class="fa fa-list"></i> <?php echo __('All'); ?></a>
                            <a href="#" class="btn btn-info btn-switch btn-mini-xs" data-type="free"><i class="fa fa-gift"></i> <?php echo __('Free'); ?></a>
                            <a href="#" class="btn btn-info btn-switch btn-mini-xs" data-type="price"><i class="fa fa-rmb"></i> <?php echo __('Paying'); ?></a>
                            <a href="#" class="btn btn-info btn-switch btn-mini-xs" data-type="local" data-url="addon/downloaded"><i class="fa fa-laptop"></i> <?php echo __('Local addon'); ?></a>
                        </div>
                        <a class="btn btn-primary btn-userinfo btn-mini-xs" href="javascript:;"><i class="fa fa-user"></i> <?php echo __('Userinfo'); ?></a>
                        <?php endif; ?>
                    </div>
                    <table id="table" class="table table-striped table-bordered table-hover" width="100%">

                    </table>

                </div>
            </div>

        </div>
    </div>
</div>
<script id="searchformtpl" type="text/html">
    <form action="" class="form-commonsearch hide">
        <div class="well" style="box-shadow:none;border-radius:2px;margin-bottom:10px;">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo __('Title'); ?></label>
                        <input class="operate" type="hidden" data-name="title" value="like"/>
                        <input class="form-control" type="text" name="title" placeholder="" value=""/>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo __('Type'); ?></label>
                        <input class="operate" type="hidden" data-name="type" value="="/>
                        <input class="form-control" type="text" name="type" placeholder="all" value=""/>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo __('Category'); ?></label>
                        <input type="hidden" class="operate" data-name="category_id" value="="/>
                        <input class="form-control" name="category_id" type="text" value="">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"><?php echo __('Version'); ?></label>
                        <input type="hidden" class="operate" data-name="faversion" value="="/>
                        <input class="form-control" name="faversion" type="text" value="<?php echo \think\Config::get('fastadmin.version'); ?>">
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="form-group">
                        <label class="control-label"></label>
                        <div class="row">
                            <div class="col-xs-6">
                                <input type="submit" class="btn btn-success btn-block" value="<?php echo __('Submit'); ?>"/>
                            </div>
                            <div class="col-xs-6">
                                <input type="reset" class="btn btn-primary btn-block" value="<?php echo __('Reset'); ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</script>
<script id="logintpl" type="text/html">
    <div>
        <form class="form-horizontal">
            <fieldset>
                <div class="alert alert-dismissable alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong><?php echo __('Warning'); ?></strong><br/><?php echo __('Login tips'); ?>
                </div>
                <div class="form-group">
                    <div class="col-lg-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" id="inputAccount" value=""
                                   placeholder="<?php echo __('Your username or email'); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            <input type="password" class="form-control" id="inputPassword" value=""
                                   placeholder="<?php echo __('Your password'); ?>">
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</script>
<script id="userinfotpl" type="text/html">
    <div>
        <form class="form-horizontal form-userinfo">
            <fieldset>
                <div class="alert alert-dismissable alert-info-light">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong><?php echo __('Warning'); ?></strong><br/><?php echo __('Logined tips', '<%=username%>'); ?>
                </div>
            </fieldset>
            <div class="breadcrumb"><a href="https://www.fastadmin.net/user/myaddon.html" target="_blank"><i class="fa fa-cube"></i> <?php echo __('My addons'); ?></a></div>
        </form>
    </div>
</script>
<script id="uninstalltpl" type="text/html">
    <div class="">
        <div class=""><%=#__("Are you sure you want to unstall %s?", addon['title'])%>
            <p class="text-danger"><?php echo __('Delete all the addon file and cannot be recovered!'); ?> </p>
                      <?php if(config('app_debug')): ?>
            <p class="text-danger"><input type="checkbox" name="droptables" id="droptables" data-name="<%=addon['name']%>"/> <?php echo __('Delete all the addon database and cannot be recovered!'); ?> </p>
                      <?php endif; ?>
            <p class="text-danger"><?php echo __('Please backup important data manually before uninstall!'); ?></p>
        </div>
    </div>
</script>
<script id="conflicttpl" type="text/html">
    <div class="alert alert-dismissable alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong><?php echo __('Warning'); ?></strong> <?php echo __('Conflict tips'); ?>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo __('File'); ?></th>
        </tr>
        </thead>
        <tbody>
        <%for(var i=0;i < conflictlist.length;i++){%>
        <tr>
            <th scope="row"><%=i+1%></th>
            <td><%=conflictlist[i]%></td>
        </tr>
        <%}%>
        </tbody>
    </table>
</script>
<!--@formatter:off-->
<script id="operatetpl" type="text/html">
    <% var labelarr = ['primary', 'success', 'info', 'danger', 'warning']; %>
    <% var label = labelarr[item.id % 5]; %>
    <% var addon = item.addon; %>

    <span class="operate" data-id="<%=item.id%>" data-name="<%=item.name%>">
        <% if(!addon){ %>
            <% if(typeof item.releaselist !="undefined" && item.releaselist.length>1){%>
                <span class="btn-group">
                    <a href="javascript:;" class="btn btn-xs btn-primary btn-success btn-install"
                       data-type="<%=item.price<=0?'free':'price';%>"
                       data-version="<%=item.version%>"><i class="fa fa-cloud-download"></i> <?php echo __('Install'); ?></a>
                    <a class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" href="javascript:;">
                        <span class="fa fa-caret-down"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <% for(var j=0;j< item.releaselist.length;j++){ %>
                        <li><a href="javascript:;" class="btn-install" data-type="<%=item.price<=0?'free':'price';%>"
                               data-version="<%=item.releaselist[j].version%>"><%=item.releaselist[j].version%></a></li>
                        <% } %>
                    </ul>
                </span>
            <% }else if(typeof item.releaselist !="undefined" && item.releaselist.length>0){%>
                <a href="javascript:;" class="btn btn-xs btn-primary btn-success btn-install"
                   data-type="<%=item.price<=0?'free':'price';%>"
                   data-version="<%=item.version%>"><i class="fa fa-cloud-download"></i> <?php echo __('Install'); ?></a>
            <% } %>

            <% if(item.demourl){ %>
                <a href="<%=item.demourl%>" class="btn btn-xs btn-primary btn-info btn-demo" target="_blank">
                    <i class="fa fa-flash"></i> <?php echo __('Demo'); ?>
                </a>
            <% } %>

            <% if(item.button){ %>
                <a href="<%=item.url%>" class="btn btn-xs btn-primary btn-info" target="_blank">
                    <%=item.button%>
                </a>
            <% } %>
        <% } else {%>
            <% if(addon.version!=item.version){%>
                <% if(typeof item.releaselist !="undefined" && item.releaselist.length>1){%>
                    <span class="btn-group">
                        <a href="javascript:;" class="btn btn-xs btn-info btn-success btn-upgrade"
                           data-version="<%=item.version%>"><i class="fa fa-cloud"></i> <?php echo __('Upgrade'); ?></a>
                        <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown"
                           href="javascript:;">
                            <span class="fa fa-caret-down"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <% for(var j=0;j< item.releaselist.length;j++){ %>
                            <li><a href="javascript:;" class="btn-upgrade"
                                   data-version="<%=item.releaselist[j].version%>"><%=item.releaselist[j].version%></a></li>
                            <% } %>
                        </ul>
                    </span>
                <% }else{%>
                    <a href="javascript:;" class="btn btn-xs btn-info btn-upgrade" title="<?php echo __('Upgrade'); ?>" data-version="<%=item.version%>"><i
                        class="fa fa-cloud"></i> <?php echo __('Upgrade'); ?></a>
                <% }%>
            <% }%>
            <% if(addon.config){ %>
                <a href="javascript:;" class="btn btn-xs btn-primary btn-config" title="<?php echo __('Setting'); ?>"><i class="fa fa-pencil"></i>
                    <?php echo __('Setting'); ?></a>
            <% } %>
            <a href="javascript:;" class="btn btn-xs btn-danger btn-uninstall" title="<?php echo __('Uninstall'); ?>"><i class="fa fa-times"></i>
                <?php echo __('Uninstall'); ?></a>
        <% } %>
    </span>
</script>
<!--@formatter:on-->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
