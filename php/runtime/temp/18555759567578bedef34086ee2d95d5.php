<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:80:"/www/wwwroot/live/public/../application/admin/view/example/customform/index.html";i:1684603773;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
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
    .upload-image {
        background: url('/assets/addons/example/img/plus.png') no-repeat center center;
        background-size: 30px 30px;
        height: 30px;
        width: 30px;
        border: 1px solid #ccc;
    }
</style>
<div class="row">
    <div class="col-md-6">
        <div class="box box-success">
            <div class="panel-heading">
                <?php echo __('自定义图片描述'); ?>
            </div>
            <div class="panel-body">
                <div class="alert alert-success-light">
                    <b>温馨提示</b><br>
                    默认我们的多图是没有图片描述的，如果我们需要自定义描述，可以使用以下的自定义功能<br>
                    特别注意的是图片的url和描述是分开储存的，也就是说图片一个字段，描述一个字段，你在前台使用时需要自己匹配映射关系<br>
                    <b>下面的演示textarea为了便于调试，设置为可见的，实际使用中应该添加个hidden的class进行隐藏</b>
                </div>
                <form id="first-form" role="form" data-toggle="validator" method="POST" action="">
                    <div class="form-group row">
                        <label class="control-label col-xs-12"><?php echo __('一维数组示例'); ?>:</label>
                        <div class="col-xs-12">
                            <div class="input-group">
                                <input id="c-files" data-rule="required" class="form-control" size="50" name="row[files]" type="text" value="https://cdn.fastadmin.net/uploads/addons/blog.png,https://cdn.fastadmin.net/uploads/addons/cms.png,https://cdn.fastadmin.net/uploads/addons/vote.png">
                                <div class="input-group-addon no-border no-padding">
                                    <span><button type="button" id="plupload-files" class="btn btn-danger plupload" data-input-id="c-files" data-mimetype="*" data-multiple="true" data-preview-id="p-files"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                    <span><button type="button" id="fachoose-files" class="btn btn-primary fachoose" data-input-id="c-files" data-mimetype="*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                                </div>
                                <span class="msg-box n-right" for="c-files"></span>
                            </div>

                            <!--ul需要添加 data-template和data-name属性，并一一对应且唯一 -->
                            <ul class="row list-inline plupload-preview" id="p-files" data-template="introtpl" data-name="row[intro]"></ul>

                            <!--请注意 ul和textarea间不能存在其它任何元素，实际开发中textarea应该添加个hidden进行隐藏-->
                            <textarea name="row[intro]" class="form-control" style="margin-top:5px;">["简洁响应式博客","CMS内容管理系统","在线投票系统"]</textarea>

                            <!--这里自定义图片预览的模板 开始-->
                            <script type="text/html" id="introtpl">
                                <li class="col-xs-3">
                                    <a href="<%=fullurl%>" data-url="<%=url%>" target="_blank" class="thumbnail">
                                        <img src="<%=fullurl%>" class="img-responsive">
                                    </a>
                                    <input type="text" name="row[intro][<%=index%>]" class="form-control mb-1" placeholder="请输入文件描述" value="<%=value?value:''%>"/>
                                    <a href="javascript:;" class="btn btn-danger btn-xs btn-trash"><i class="fa fa-trash"></i></a>
                                </li>
                            </script>
                            <!--这里自定义图片预览的模板 结束-->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12"><?php echo __('二维数组示例'); ?>:</label>
                        <div class="col-xs-12">
                            <div class="input-group">
                                <input id="c-images" data-rule="required" class="form-control" size="50" name="row[images]" type="text" value="https://cdn.fastadmin.net/uploads/addons/example.png,https://cdn.fastadmin.net/uploads/addons/upyun.png,https://cdn.fastadmin.net/uploads/addons/alioss.png">
                                <div class="input-group-addon no-border no-padding">
                                    <span><button type="button" id="plupload-images" class="btn btn-danger plupload" data-input-id="c-images" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp,image/webp" data-multiple="true" data-preview-id="p-images"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                    <span><button type="button" id="fachoose-images" class="btn btn-primary fachoose" data-input-id="c-images" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                                </div>
                                <span class="msg-box n-right" for="c-images"></span>
                            </div>

                            <!--ul需要添加 data-template和data-name属性，并一一对应且唯一 -->
                            <ul class="row list-inline plupload-preview" id="p-images" data-template="desctpl" data-name="row[desc]"></ul>

                            <!--请注意 ul和textarea间不能存在其它任何元素，实际开发中textarea应该添加个hidden进行隐藏-->
                            <textarea name="row[desc]" class="form-control" style="margin-top:5px;">[{"info":"开发者示例插件","size":"1M"},{"info":"又拍云储存整合","size":"2M"},{"info":"阿里OSS云储存","size":"1M"}]</textarea>

                            <!--这里自定义图片预览的模板 开始-->
                            <script type="text/html" id="desctpl">
                                <li class="col-xs-3">
                                    <a href="<%=fullurl%>" data-url="<%=url%>" target="_blank" class="thumbnail">
                                        <img src="<%=fullurl%>" class="img-responsive">
                                    </a>
                                    <input type="text" name="row[desc][<%=index%>][info]" class="form-control mb-1" placeholder="请输入插件描述" value="<%=value?value['info']:''%>"/>
                                    <input type="text" name="row[desc][<%=index%>][size]" class="form-control mb-1" placeholder="请输入插件大小" value="<%=value?value['size']:''%>"/>
                                    <a href="javascript:;" class="btn btn-danger btn-xs btn-trash"><i class="fa fa-trash"></i></a>
                                </li>
                            </script>
                            <!--这里自定义图片预览的模板 结束-->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12"></label>
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-success btn-embossed"><?php echo __('OK'); ?></button>
                            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
    <div class="col-md-6">
        <div class="box box-info">
            <div class="panel-heading">
                <?php echo __('自定义Fieldlist示例'); ?>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger-light">
                    <b>温馨提示</b><br>
                    默认我们的fieldlist只有一维数组，为键值形式，如果需要二维数组，可使用下面的自定义模板来实现<br>
                    默认追加的元素是没有进行事件绑定的，我们需要监听btn-append这个按钮的fa.event.appendfieldlist事件<br>
                    <b>下面的演示textarea为了便于调试，设置为可见的，实际使用中应该添加个hidden的class进行隐藏</b>
                </div>
                <form id="second-form" role="form" data-toggle="validator" method="POST" action="">

                    <div class="form-group row">
                        <label class="control-label col-xs-12"><?php echo __('Fieldlist示例'); ?>:</label>
                        <div class="col-xs-12">
                            <table class="table fieldlist" data-template="basictpl" data-name="row[basic]" id="first-table">
                                <tr>
                                    <td><?php echo __('标题'); ?></td>
                                    <td><?php echo __('介绍'); ?></td>
                                    <td><?php echo __('大小'); ?></td>
                                    <td><?php echo __('状态'); ?></td>
                                    <td width="100"></td>
                                </tr>
                                <tr>
                                    <td colspan="5"><a href="javascript:;" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a></td>
                                </tr>
                            </table>

                            <!--请注意实际开发中textarea应该添加个hidden进行隐藏-->
                            <textarea name="row[basic]" class="form-control" cols="30" rows="5">[{"title":"开发者示例插件","intro":"开发者必备","size":"1M","state":1},{"title":"又拍云储存整合","intro":"一款云储存","size":"2M","state":0},{"title":"阿里OSS云储存","intro":"一款云储存","size":"1M","state":1}]</textarea>
                            <script id="basictpl" type="text/html">
                                <tr class="form-inline">
                                    <td><input type="text" name="<%=name%>[<%=index%>][title]" class="form-control" size="15" value="<%=row.title%>" placeholder="标题"/></td>
                                    <td><input type="text" name="<%=name%>[<%=index%>][intro]" class="form-control" size="15" value="<%=row.intro%>" placeholder="介绍"/></td>
                                    <td><input type="text" name="<%=name%>[<%=index%>][size]" class="form-control" style="width:50px" value="<%=row.size%>" placeholder="大小"/></td>
                                    <td>
                                        <input type="hidden" name="<%=name%>[<%=index%>][state]" id="c-state-<%=index%>" class="form-control" style="width:50px" value="<%=row.state%>" placeholder="状态"/>
                                        <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-state-<%=index%>" data-yes="1" data-no="0" >
                                            <i class="fa fa-toggle-on text-success <%if(row.state==0){%>fa-flip-horizontal text-gray<%}%> fa-2x"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <!--下面的两个按钮务必保留-->
                                        <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>
                                        <span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span>
                                    </td>
                                </tr>
                            </script>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12"><?php echo __('元素事件'); ?>:</label>
                        <div class="col-xs-12">
                            <table class="table fieldlist" data-template="eventtpl" data-name="row[event]" id="second-table">
                                <tr>
                                    <td><?php echo __('管理员'); ?></td>
                                    <td><?php echo __('图片'); ?></td>
                                    <td><?php echo __('登录时间'); ?></td>
                                    <td width="100"></td>
                                </tr>
                                <tr>
                                    <td colspan="4"><a href="javascript:;" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a></td>
                                </tr>
                            </table>

                            <!--请注意实际开发中textarea应该添加个hidden进行隐藏-->
                            <textarea name="row[event]" class="form-control" cols="30" rows="5">[{"id":"1","image":"/assets/addons/example/img/200x200.png","time":"2019-06-28 12:05:03"}]</textarea>
                            <script id="eventtpl" type="text/html">
                                <tr class="form-inline">
                                    <td><input type="text" name="<%=name%>[<%=index%>][id]" class="form-control selectpage" data-source="auth/admin/selectpage" data-field="username" value="<%=row.id%>" placeholder="管理员"/></td>
                                    <td>
                                        <input type="hidden" name="<%=name%>[<%=index%>][image]" id="c-image-<%=index%>" value="<%=row.image%>">
                                        <!--@formatter:off-->
                                        <button type="button" id="faupload-image" class="btn btn-danger faupload upload-image" data-input-id="c-image-<%=index%>" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp,image/webp" data-multiple="false" <%if(row.image){%>style="background-image: url('<%=Fast.api.cdnurl(row.image)%>')"<%}%>></button>
                                        <!--@formatter:on-->
                                    </td>
                                    <td><input type="text" name="<%=name%>[<%=index%>][time]" class="form-control datetimepicker" style="width:120px" value="<%=row.time%>" placeholder="时间"/></td>
                                    <td>
                                        <!--下面的两个按钮务必保留-->
                                        <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>
                                        <span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span>
                                    </td>
                                </tr>
                            </script>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12"></label>
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-success btn-embossed"><?php echo __('OK'); ?></button>
                            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
    <div class="col-md-6">
        <div class="box box-warning">
            <div class="panel-heading">
                自动完成+标签输入示例，<font color="red">只支持FastAdmin1.3.0+</font> <?php if(version_compare(\think\Config::get('fastadmin.version'), '1.3.0')<0): ?><span class="label label-danger">你当前FastAdmin版本不支持</span><?php endif; ?>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger-light">
                    <b>温馨提示</b><br>
                </div>
                <form id="third-form" role="form" data-toggle="validator" method="POST" action="">

                    <div class="form-group row">
                        <label class="control-label col-xs-12">自动完成</label>
                        <div class="col-xs-12">
                            <input type="text" class="form-control" data-role="autocomplete" data-autocomplete-options='{"url":"example/customform/get_title_list", "minChars":1}' />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12">标签输入 <span class="text-muted small">输入后<code>回车</code>或<code>,</code>确认</span></label>
                        <div class="col-xs-12">
                            <input type="text" class="form-control" data-role="tagsinput" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12">自动完成+标签输入</label>
                        <div class="col-xs-12">
                            <input type="text" class="form-control" data-role="tagsinput" data-tagsinput-options='{"minChars":1, "autocomplete":{"url":"example/customform/get_title_list"}}' />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-danger">
            <div class="panel-heading">
                动态显示，<font color="red">只支持FastAdmin1.3.3+</font> <?php if(version_compare(\think\Config::get('fastadmin.version'), '1.3.3')<0): ?><span class="label label-danger">你当前FastAdmin版本不支持</span><?php endif; ?>
            </div>
            <div class="panel-body">
                <form id="fourth-form" role="form" data-toggle="validator" method="POST" action="">

                    <div class="form-group row">
                        <label class="control-label col-xs-12">常规使用</label>
                        <div class="col-xs-12">
                                <input type="radio" name="row[type]" value="value1" checked /> 类型1
                                <input type="radio" name="row[type]" value="value2" /> 类型2
                                <div data-favisible="type=value1" class="p-3">显示内容1</div>
                                <div data-favisible="type=value2" class="p-3">显示内容2</div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label class="control-label col-xs-12">使用开关组件</label>
                        <div class="col-xs-12">

                            <input  id="c-switch" name="row[switch]" type="hidden" value="0">
                            <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-switch" data-yes="1" data-no="0" >
                                <i class="fa fa-toggle-on text-success fa-flip-horizontal text-gray fa-2x"></i>
                            </a>

                            <div data-favisible="switch=1" class="p-3">显示内容隐藏的内容</div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label class="control-label col-xs-12">组件嵌套</label>
                        <div class="col-xs-12">
                            <input type="radio" name="row[mode]" value="value1" /> 模式1
                            <input type="radio" name="row[mode]" value="value2" /> 模式2
                            <div data-favisible="mode=value1" class="p-3">
                                <h4>显示内容1</h4>
                                <input  id="c-switch1" name="row[switch1]" type="hidden" value="0">
                                <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-switch1" data-yes="1" data-no="0" >
                                    <i class="fa fa-toggle-on text-success fa-flip-horizontal text-gray fa-2x"></i>
                                </a>

                                <div data-favisible="switch1=1" class="p-3">显示内容隐藏的内容1</div>
                            </div>
                            <div data-favisible="mode=value2" class="p-3">
                                <h4>显示内容2</h4>
                                <input  id="c-switch2" name="row[switch2]" type="hidden" value="0">
                                <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-switch2" data-yes="1" data-no="0" >
                                    <i class="fa fa-toggle-on text-success fa-flip-horizontal text-gray fa-2x"></i>
                                </a>

                                <div data-favisible="switch2=1" class="p-3">显示内容隐藏的内容2</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label class="control-label col-xs-12">使用内容判断 <span class="text-muted small">只有输入指定的内容才显示</span></label>
                        <div class="col-xs-12">
                            <input type="text" class="form-control" name="row[title]" placeholder="请输入abc三个字母">
                            <div data-favisible="title=abc" class="p-3">显示内容隐藏的内容</div>
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
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
