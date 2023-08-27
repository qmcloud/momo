<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:67:"/www/wwwroot/live/public/../application/admin/view/command/add.html";i:1684603783;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
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
                                <style>
    .relation-item {margin-top:10px;}
    legend {padding-bottom:5px;font-size:14px;font-weight:600;}
    label {font-weight:normal;}
    .form-control{padding:6px 8px;}
    #extend-zone .col-xs-2 {margin-top:10px;padding-right:0;}
    #extend-zone .col-xs-2:nth-child(6n+0) {padding-right:15px;}
</style>
<div class="panel panel-default panel-intro">
    <div class="panel-heading">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#crud" data-toggle="tab"><?php echo __('一键生成CRUD'); ?></a></li>
            <li><a href="#menu" data-toggle="tab"><?php echo __('一键生成菜单'); ?></a></li>
            <li><a href="#min" data-toggle="tab"><?php echo __('一键压缩打包'); ?></a></li>
            <li><a href="#api" data-toggle="tab"><?php echo __('一键生成API文档'); ?></a></li>
        </ul>
    </div>
    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="crud">
                <div class="row">
                    <div class="col-xs-12">
                        <form role="form">
                            <input type="hidden" name="commandtype" value="crud" />
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <input checked="" name="isrelation" type="hidden" value="0">
                                        <label class="control-label" data-toggle="tooltip" title="当前只支持生成1对1关联模型,选中后请配置关联表和字段">
                                            <input name="isrelation" type="checkbox" value="1">
                                            关联模型
                                        </label>
                                    </div>
                                    <div class="col-xs-3">
                                        <input checked="" name="local" type="hidden" value="1">
                                        <label class="control-label" data-toggle="tooltip" title="默认模型生成在application/admin/model目录下,选中后将生成在application/common/model目录下">
                                            <input name="local" type="checkbox" value="0"> 全局模型类
                                        </label>
                                    </div>
                                    <div class="col-xs-3">
                                        <input checked="" name="delete" type="hidden" value="0">
                                        <label class="control-label" data-toggle="tooltip" title="删除CRUD生成的相关文件">
                                            <input name="delete" type="checkbox" value="1"> 删除模式
                                        </label>
                                    </div>
                                    <div class="col-xs-3">
                                        <input checked="" name="force" type="hidden" value="0">
                                        <label class="control-label" data-toggle="tooltip" title="选中后,如果已经存在同名文件将被覆盖。如果是删除将不再提醒">
                                            <input name="force" type="checkbox" value="1">
                                            强制覆盖模式
                                        </label>
                                    </div>
                                    <!--
                                    <div class="col-xs-3">
                                        <input checked="" name="menu" type="hidden" value="0">
                                        <label class="control-label" data-toggle="tooltip" title="选中后,将同时生成后台菜单规则">
                                            <input name="menu" type="checkbox" value="1">
                                            生成菜单
                                        </label>
                                    </div>
                                    -->
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>主表设置</legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>请选择主表</label>
                                        <?php echo build_select('table',$tableList,null,['class'=>'form-control selectpicker', 'data-live-search'=>'true']);; ?>
                                    </div>
                                    <div class="col-xs-3">
                                        <label>自定义控制器名</label>
                                        <input type="text" class="form-control" name="controller" data-toggle="tooltip" title="默认根据表名自动生成,如果需要放在二级目录请手动填写" placeholder="支持目录层级,以/分隔">
                                    </div>
                                    <div class="col-xs-3">
                                        <label>自定义模型名</label>
                                        <input type="text" class="form-control" name="model" data-toggle="tooltip" title="默认根据表名自动生成" placeholder="不支持目录层级">
                                    </div>
                                    <div class="col-xs-3">
                                        <label>显示字段(默认全部)</label>
                                        <select name="fields[]" id="fields" multiple style="height:30px;" class="form-control selectpicker"></select>
                                    </div>

                                </div>

                            </div>

                            <div class="form-group hide" id="relation-zone">
                                <legend>关联表设置</legend>

                                <div class="row" style="margin-top:15px;">
                                    <div class="col-xs-12">
                                        <a href="javascript:;" class="btn btn-primary btn-sm btn-newrelation" data-index="1">追加关联模型</a>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="form-group" id="extend-zone">
                                <legend>字段识别设置 <span style="font-size:12px;font-weight: normal;">(与之匹配的字段都将生成相应组件)</span></legend>
                                <div class="row">
                                    <div class="col-xs-2">
                                        <label>复选框后缀</label>
                                        <input type="text" class="form-control" name="setcheckboxsuffix" placeholder="默认为set类型" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>单选框后缀</label>
                                        <input type="text" class="form-control" name="enumradiosuffix" placeholder="默认为enum类型" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>图片类型后缀</label>
                                        <input type="text" class="form-control" name="imagefield" placeholder="默认为image,images,avatar,avatars" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>文件类型后缀</label>
                                        <input type="text" class="form-control" name="filefield" placeholder="默认为file,files" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>日期时间后缀</label>
                                        <input type="text" class="form-control" name="intdatesuffix" placeholder="默认为time" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>开关后缀</label>
                                        <input type="text" class="form-control" name="switchsuffix" placeholder="默认为switch" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>城市选择后缀</label>
                                        <input type="text" class="form-control" name="citysuffix" placeholder="默认为city" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>动态下拉后缀(单)</label>
                                        <input type="text" class="form-control" name="selectpagesuffix" placeholder="默认为_id" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>动态下拉后缀(多)</label>
                                        <input type="text" class="form-control" name="selectpagessuffix" placeholder="默认为_ids" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>忽略的字段</label>
                                        <input type="text" class="form-control" name="ignorefields" placeholder="默认无" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>排序字段</label>
                                        <input type="text" class="form-control" name="sortfield" placeholder="默认为weigh" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>富文本编辑器</label>
                                        <input type="text" class="form-control" name="editorsuffix" placeholder="默认为content" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>选项卡过滤字段</label>
                                        <input type="text" class="form-control" name="headingfilterfield" placeholder="默认为status" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>标签后缀 <i class="fa fa-info-circle" data-toggle="tooltip" data-title="只支持1.3.0+版本"></i></label>
                                        <input type="text" class="form-control" name="tagsuffix" placeholder="默认为tag,tags" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>JSON后缀 <i class="fa fa-info-circle" data-toggle="tooltip" data-title="只支持1.3.0+版本"></i></label>
                                        <input type="text" class="form-control" name="jsonsuffix" placeholder="默认为json" />
                                    </div>
                                    <div class="col-xs-2">
                                        <label>固定列数量 <i class="fa fa-info-circle" data-toggle="tooltip" data-title="只支持1.3.0+版本，大于0时为右侧固定列数量，小于0时为左侧固定列数量"></i></label>
                                        <input type="text" class="form-control" name="fixedcolumns" placeholder="默认不启用" />
                                    </div>

                                </div>

                            </div>

                            <div class="form-group">
                                <legend>生成命令行</legend>
                                <textarea class="form-control" data-toggle="tooltip" title="如果在线执行命令失败,可以将命令复制到命令行进行执行" rel="command" rows="1" placeholder="请点击生成命令行"></textarea>
                            </div>

                            <div class="form-group">
                                <legend>返回结果</legend>
                                <textarea class="form-control" rel="result" rows="5" placeholder="请点击立即执行"></textarea>
                            </div>

                            <div class="form-group">
                                    <button type="button" class="btn btn-info btn-embossed btn-command"><?php echo __('生成命令行'); ?></button>
                                    <button type="button" class="btn btn-success btn-embossed btn-execute"><?php echo __('立即执行'); ?></button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="menu">
                <div class="row">
                    <div class="col-xs-12">
                        <form role="form">
                            <input type="hidden" name="commandtype" value="menu" />
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <input checked="" name="allcontroller" type="hidden" value="0">
                                        <label class="control-label" data-toggle="tooltip" title="将删除全部的菜单规则，重新按控制器进行生成，请做好备份，谨慎选择">
                                            <input name="allcontroller" data-toggle="collapse" data-target="#controller" type="checkbox" value="1"> 一键生成全部控制器
                                        </label>
                                    </div>
                                    <div class="col-xs-3">
                                        <input checked="" name="delete" type="hidden" value="0">
                                        <label class="control-label" data-toggle="tooltip" title="删除控制器菜单规则">
                                            <input name="delete" type="checkbox" value="1"> 删除模式
                                        </label>
                                    </div>
                                    <div class="col-xs-3">
                                        <input checked="" name="force" type="hidden" value="0">
                                        <label class="control-label" data-toggle="tooltip" title="如果菜单规则已经存在则覆盖">
                                            <input name="force" type="checkbox" value="1"> 强制覆盖模式
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group in" id="controller">
                                <legend>控制器设置</legend>

                                <div class="row" style="margin-top:15px;">
                                    <div class="col-xs-12">
                                        <input type="text" name="controllerfile" class="form-control selectpage" style="width:720px;" data-source="command/get_controller_list" data-multiple="true" name="controller" placeholder="请选择控制器" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <legend>生成命令行</legend>
                                <textarea class="form-control" rel="command" rows="1" placeholder="请点击生成命令行"></textarea>
                            </div>

                            <div class="form-group">
                                <legend>返回结果</legend>
                                <textarea class="form-control" rel="result" rows="5" placeholder="请点击立即执行"></textarea>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command"><?php echo __('生成命令行'); ?></button>
                                <button type="button" class="btn btn-success btn-embossed btn-execute"><?php echo __('立即执行'); ?></button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="min">
                <div class="row">
                    <div class="col-xs-12">
                        <form role="form">
                            <input type="hidden" name="commandtype" value="min" />
                            <div class="form-group">
                                <legend>基础设置</legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>请选择压缩模块</label>
                                        <select name="module" class="form-control selectpicker">
                                            <option value="all" selected>全部</option>
                                            <option value="backend">后台Backend</option>
                                            <option value="frontend">前台Frontend</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-3">
                                        <label>请选择压缩资源</label>
                                        <select name="resource" class="form-control selectpicker">
                                            <option value="all" selected>全部</option>
                                            <option value="js">JS</option>
                                            <option value="css">CSS</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-3">
                                        <label>请选择压缩模式</label>
                                        <select name="optimize" class="form-control selectpicker">
                                            <option value="">无</option>
                                            <option value="uglify">uglify</option>
                                            <option value="closure">closure</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group in">
                                <legend>控制器设置</legend>

                                <div class="row" style="margin-top:15px;">
                                    <div class="col-xs-12">

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <legend>生成命令行</legend>
                                <textarea class="form-control" rel="command" rows="1" placeholder="请点击生成命令行"></textarea>
                            </div>

                            <div class="form-group">
                                <legend>返回结果</legend>
                                <textarea class="form-control" rel="result" rows="5" placeholder="请点击立即执行"></textarea>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command"><?php echo __('生成命令行'); ?></button>
                                <button type="button" class="btn btn-success btn-embossed btn-execute"><?php echo __('立即执行'); ?></button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="api">
                <div class="row">
                    <div class="col-xs-12">
                        <form role="form">
                            <input type="hidden" name="commandtype" value="api" />
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <input checked="" name="force" type="hidden" value="0">
                                        <label class="control-label" data-toggle="tooltip" title="如果已经存在则覆盖">
                                            <input name="force" type="checkbox" value="1">
                                            覆盖模式
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <legend>文档设置</legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>请输入接口URL</label>
                                        <input type="text" name="url" class="form-control" placeholder="API URL,可留空" />
                                    </div>
                                    <div class="col-xs-3">
                                        <label>接口生成文件</label>
                                        <input type="text" name="output" class="form-control" placeholder="留空则使用api.html" />
                                    </div>
                                    <div class="col-xs-3">
                                        <label>模板文件</label>
                                        <input type="text" name="template" class="form-control" placeholder="如果不清楚请留空" />
                                    </div>
                                    <div class="col-xs-3">
                                        <label>文档语言</label>
                                        <select name="language" class="form-control">
                                            <option value="" selected>请选择语言</option>
                                            <option value="zh-cn">中文</option>
                                            <option value="en">英文</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:10px;">
                                    <div class="col-xs-3">
                                        <label>文档标题</label>
                                        <input type="text" name="title" class="form-control" placeholder="默认为<?php echo $site['name']; ?>" />
                                    </div>
                                    <div class="col-xs-3">
                                        <label>文档作者</label>
                                        <input type="text" name="author" class="form-control" placeholder="默认为<?php echo $site['name']; ?>" />
                                    </div>
                                    <div class="col-xs-3">
                                        <label>生成模块</label>
                                        <select name="module" class="form-control selectpicker">
                                            <option value="" selected>请选择模块</option>
                                            <option value="api">API</option>
                                            <option value="backend">后台</option>
                                            <option value="frontend">前台</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-3">
                                        <label>生成插件文档</label>
                                        <select name="addon" class="form-control selectpicker" data-live-search="true">
                                            <option value="" selected>请选择插件</option>
                                            <?php $_646905c3c47b0=get_addon_list(); if(is_array($_646905c3c47b0) || $_646905c3c47b0 instanceof \think\Collection || $_646905c3c47b0 instanceof \think\Paginator): if( count($_646905c3c47b0)==0 ) : echo "" ;else: foreach($_646905c3c47b0 as $key=>$item): ?>
                                            <option value="<?php echo $item['name']; ?>"><?php echo $item['title']; ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <legend>生成命令行</legend>
                                <textarea class="form-control" rel="command" rows="1" placeholder="请点击生成命令行"></textarea>
                            </div>

                            <div class="form-group">
                                <legend>返回结果</legend>
                                <textarea class="form-control" rel="result" rows="5" placeholder="请点击立即执行"></textarea>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command"><?php echo __('生成命令行'); ?></button>
                                <button type="button" class="btn btn-success btn-embossed btn-execute"><?php echo __('立即执行'); ?></button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script id="relationtpl" type="text/html">
    <div class="row relation-item">
        <div class="col-xs-2">
            <label>请选择关联表</label>
            <select name="relation[<%=index%>][relation]" class="form-control relationtable" data-live-search="true"></select>
        </div>
        <div class="col-xs-2">
            <label>请选择关联类型</label>
            <select name="relation[<%=index%>][relationmode]" class="form-control relationmode"></select>
        </div>
        <div class="col-xs-2">
            <label>关联外键</label>
            <select name="relation[<%=index%>][relationforeignkey]" class="form-control relationforeignkey"></select>
        </div>
        <div class="col-xs-2">
            <label>关联主键</label>
            <select name="relation[<%=index%>][relationprimarykey]" class="form-control relationprimarykey"></select>
        </div>
        <div class="col-xs-2">
            <label>请选择显示字段</label>
            <select name="relation[<%=index%>][relationfields][]" multiple class="form-control relationfields"></select>
        </div>
        <div class="col-xs-2">
            <label>&nbsp;</label>
            <a href="javascript:;" class="btn btn-danger btn-block btn-removerelation">移除</a>
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
