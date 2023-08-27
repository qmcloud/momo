<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:78:"/www/wwwroot/live/public/../application/admin/view/example/cxselect/index.html";i:1684603773;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
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
                                <style>#cxselect-example textarea{margin:10px 0;}</style>
<div class="panel panel-default panel-intro">
    <?php echo build_heading(); ?>

    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding" id="cxselect-example">
                    <form id="cxselectform" action="">
                        <div class="row">
                            <div class="col-md-6">

                                <div class="panel panel-default">
                                    <div class="panel-heading"><b>省市区联动</b>(通过AJAX读取数据)</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-9">
                                                <div class="form-inline" data-toggle="cxselect" data-selects="province,city,area">
                                                    <select class="province form-control" name="province" data-url="ajax/area"></select>
                                                    <select class="city form-control" name="city" data-url="ajax/area"></select>
                                                    <select class="area form-control" name="area" data-url="ajax/area"></select>
                                                </div>
                                            </div>
                                            <div class="col-xs-3 text-right">
                                                <h6><label class="label label-primary"><i class="fa fa-pencil"></i> 增加</label></h6>
                                            </div>
                                            <div class="col-xs-12">
                                                <textarea class="form-control" rows="8">
                                                </textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-9">
                                                <div class="form-inline" data-toggle="cxselect" data-selects="province,city,area">
                                                    <select class="province form-control" name="province" data-url="ajax/area">
                                                        <option value="1964" selected>广东省</option>
                                                    </select>
                                                    <select class="city form-control" name="city" data-url="ajax/area">
                                                        <option value="1988" selected>深圳市</option>
                                                    </select>
                                                    <select class="area form-control" name="area" data-url="ajax/area">
                                                        <option value="1991" selected>南山区</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-3 text-right">
                                                <h6><label class="label label-success"><i class="fa fa-edit"></i> 修改</label></h6>
                                            </div>
                                            <div class="col-xs-12">
                                                <textarea class="form-control" rows="8">
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><b>类别联动</b>(Ajax读取数据)</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-9">
                                                <div class="form-inline" data-toggle="cxselect" data-selects="first,second">
                                                    <select class="first form-control" name="first" data-url="ajax/category?type=page&pid=5"></select>
                                                    <select class="second form-control" name="second" data-url="ajax/category" data-query-name="pid"></select>
                                                </div>
                                            </div>
                                            <div class="col-xs-3 text-right">
                                                <h6><label class="label label-primary"><i class="fa fa-pencil"></i> 增加</label></h6>
                                            </div>
                                            <div class="col-xs-12">
                                                <textarea class="form-control" rows="8">
                                                </textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-9">
                                                <div class="form-inline" data-toggle="cxselect" data-selects="first,second">
                                                    <select class="first form-control" name="first" data-url="ajax/category?type=page&pid=5">
                                                        <option value="6" selected>网站建站</option>
                                                    </select>
                                                    <select class="second form-control" name="second" data-url="ajax/category" data-query-name="pid">
                                                        <option value="9" selected>移动端</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-3 text-right">
                                                <h6><label class="label label-success"><i class="fa fa-edit"></i> 修改</label></h6>
                                            </div>
                                            <div class="col-xs-12">
                                                <textarea class="form-control" rows="8">
                                                </textarea>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><b>省市区联动</b>(通过JSON渲染数据)</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-9">
                                                <!--由于在初始化中修改了默认值,所以这里需要修改-jsonSpace/jsonValue/jsonName的值-->
                                                <div class="form-inline" data-toggle="cxselect" data-url="/assets/libs/fastadmin-cxselect/js/cityData.min.json"
                                                     data-selects="province,city,area" data-json-space="" data-json-name="n" data-json-value="">
                                                    <select class="province form-control" name="province"></select>
                                                    <select class="city form-control" name="city"></select>
                                                    <select class="area form-control" name="area"></select>
                                                </div>
                                            </div>
                                            <div class="col-xs-3 text-right">
                                                <h6><label class="label label-primary"><i class="fa fa-pencil"></i> 增加</label></h6>
                                            </div>
                                            <div class="col-xs-12">
                                                <textarea class="form-control" rows="8">
                                                </textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-9">
                                                <!--由于在初始化中修改了默认值,所以这里需要修改-jsonSpace/jsonValue/jsonName的值-->
                                                <div class="form-inline" data-toggle="cxselect" data-url="/assets/libs/fastadmin-cxselect/js/cityData.min.json"
                                                     data-selects="province,city,area" data-json-space="" data-json-name="n" data-json-value="">
                                                    <select class="province form-control" data-first-title="选择省">
                                                        <option value="">请选择</option>
                                                        <option value="浙江省" selected>浙江省</option>
                                                    </select>
                                                    <select class="city form-control" data-first-title="选择市">
                                                        <option value="">请选择</option>
                                                        <option value="杭州市" selected>杭州市</option>
                                                    </select>
                                                    <select class="area form-control" data-first-title="选择地区">
                                                        <option value="">请选择</option>
                                                        <option value="西湖区" selected>西湖区</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-3 text-right">
                                                <h6><label class="label label-success"><i class="fa fa-edit"></i> 修改</label></h6>
                                            </div>
                                            <div class="col-xs-12">
                                                <textarea class="form-control" rows="8">
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
