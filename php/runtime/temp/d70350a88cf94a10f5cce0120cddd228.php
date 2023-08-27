<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"/www/wwwroot/live/public/../application/admin/view/example/tabletemplate/index.html";i:1684603773;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
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
                                <div class="panel panel-default panel-intro">
    <?php echo build_heading(); ?>

    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding">
                    <div id="toolbar" class="toolbar">
                        <?php echo build_toolbar('refresh,delete'); ?>
                        <a class="btn btn-info btn-disabled disabled btn-selected" href="javascript:;"><i class="fa fa-leaf"></i> 获取选中项</a>
                        <a class="btn btn-success btn-toggle-view" href="javascript:;"><i class="fa fa-leaf"></i> 切换视图</a>
                    </div>
                    <table id="table" class="table table-striped table-hover" width="100%">

                    </table>

                </div>
            </div>

        </div>
    </div>
</div>
<style type="text/css">
    .example {
        height:100%;position: relative;
    }
    .example > span {
        position:absolute;left:15px;top:15px;
    }
</style>

<script id="itemtpl" type="text/html">
    <!--
    如果启用了templateView,默认调用的是itemtpl这个模板，可以通过设置templateFormatter来修改
    在当前模板中可以使用三个变量(item:行数据,i:当前第几行,data:所有的行数据)
    此模板引擎使用的是art-template的native,可参考官方文档
    -->

    <div class="col-sm-4 col-md-3">
        <!--下面四行是为了展示随机图片和标签，可移除-->
        <% var imagearr = ['https://cdn.fastadmin.net/uploads/addons/blog.png', 'https://cdn.fastadmin.net/uploads/addons/cms.png', 'https://cdn.fastadmin.net/uploads/addons/vote.png', 'https://cdn.fastadmin.net/uploads/addons/blog.png', 'https://cdn.fastadmin.net/uploads/addons/alisms.png']; %>
        <% var image = imagearr[item.id % 5]; %>
        <% var labelarr = ['primary', 'success', 'info', 'danger', 'warning']; %>
        <% var label = labelarr[item.id % 5]; %>
        <div class="thumbnail example">
            <span class="btn btn-<%=label%>">ID:<%=item.id%></span>
            <img src="<%=image%>" style="width:100%;" alt="<%=item.title%>">
            <div class="caption">
                <h4><%=item.title?item.title:'无'%></h4>
                <p class="text-muted">操作者IP:<%=item.ip%></p>
                <p class="text-muted">操作时间:<%=Moment(item.createtime*1000).format("YYYY-MM-DD HH:mm:ss")%></p>
                <p>
                    <!--详情的事件需要在JS中手动绑定-->
                    <a href="#" class="btn btn-primary btn-success btn-detail" data-id="<%=item.id%>"><i class="fa fa-camera"></i> 详情</a> 

                    <!--如果需要响应编辑或删除事件，可以给元素添加 btn-edit或btn-del的类和data-id这个属性值-->
                    <a href="#" class="btn btn-primary btn-edit" data-id="<%=item.id%>"><i class="fa fa-pencil"></i> 编辑</a> 
                    <a href="#" class="btn btn-danger btn-del" data-id="<%=item.id%>"><i class="fa fa-times"></i> 删除</a>
                    <span class="pull-right" style="margin-top:10px;">
                        <!--如果需要多选操作，请确保有下面的checkbox元素存在,可移除-->
                        <input name="checkbox" data-id="<%=item.id%>" type="checkbox" />
                    </span>
                </p>
            </div>
        </div>
    </div>
</script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
