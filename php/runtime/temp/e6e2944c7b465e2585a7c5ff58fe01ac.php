<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:76:"/www/wwwroot/live/public/../application/admin/view/general/config/index.html";i:1671020443;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
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
    @media (max-width: 375px) {
        .edit-form tr td input {
            width: 100%;
        }

        .edit-form tr th:first-child, .edit-form tr td:first-child {
            width: 20%;
        }

        .edit-form tr th:nth-last-of-type(-n+2), .edit-form tr td:nth-last-of-type(-n+2) {
            display: none;
        }
    }

    .edit-form table > tbody > tr td a.btn-delcfg {
        visibility: hidden;
    }

    .edit-form table > tbody > tr:hover td a.btn-delcfg {
        visibility: visible;
    }

    @media (max-width: 767px) {
        .edit-form table tr th:nth-last-child(-n + 2), .edit-form table tr td:nth-last-child(-n + 2) {
            display: none;
        }

        .edit-form table tr td .msg-box {
            display: none;
        }
    }
</style>
<div class="panel panel-default panel-intro">
    <div class="panel-heading">
        <?php echo build_heading(null, false); ?>
        <ul class="nav nav-tabs">
            <?php foreach($siteList as $index=>$vo): ?>
            <li class="<?php echo !empty($vo['active'])?'active':''; ?>"><a href="#tab-<?php echo $vo['name']; ?>" data-toggle="tab"><?php echo __($vo['title']); ?></a></li>
            <?php endforeach; if(\think\Config::get('app_debug')): ?>
            <li data-toggle="tooltip" title="<?php echo __('Add new config'); ?>">
                <a href="#addcfg" data-toggle="tab"><i class="fa fa-plus"></i></a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <!--@formatter:off-->
            <?php foreach($siteList as $index=>$vo): ?>
            <div class="tab-pane fade <?php echo !empty($vo['active'])?'active in' : ''; ?>" id="tab-<?php echo $vo['name']; ?>">
                <div class="widget-body no-padding">
                    <form id="<?php echo $vo['name']; ?>-form" class="edit-form form-horizontal" role="form" data-toggle="validator" method="POST" action="<?php echo url('general.config/edit'); ?>">
                        <?php echo token(); ?>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%"><?php echo __('Title'); ?></th>
                                <th width="68%"><?php echo __('Value'); ?></th>
                                <?php if(\think\Config::get('app_debug')): ?>
                                <th width="15%"><?php echo __('Name'); ?></th>
                                <th width="2%"></th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($vo['list'] as $item): ?>
                            <tr data-favisible="<?php echo htmlentities((isset($item['visible']) && ($item['visible'] !== '')?$item['visible']:'')); ?>" data-name="<?php echo $item['name']; ?>" class="<?php if($item['visible']??''): ?>hidden<?php endif; ?>">
                                <td><?php echo $item['title']; ?></td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php switch($item['type']): case "string": ?>
                                            <input <?php echo $item['extend_html']; ?> type="text" name="row[<?php echo $item['name']; ?>]" value="<?php echo htmlentities($item['value']); ?>" class="form-control" data-rule="<?php echo $item['rule']; ?>" data-tip="<?php echo $item['tip']; ?>"/>
                                            <?php break; case "password": ?>
                                            <input <?php echo $item['extend_html']; ?> type="password" name="row[<?php echo $item['name']; ?>]" value="<?php echo htmlentities($item['value']); ?>" class="form-control" data-rule="<?php echo $item['rule']; ?>" data-tip="<?php echo $item['tip']; ?>"/>
                                            <?php break; case "text": ?>
                                            <textarea <?php echo $item['extend_html']; ?> name="row[<?php echo $item['name']; ?>]" class="form-control" data-rule="<?php echo $item['rule']; ?>" rows="5" data-tip="<?php echo $item['tip']; ?>"><?php echo htmlentities($item['value']); ?></textarea>
                                            <?php break; case "editor": ?>
                                            <textarea <?php echo $item['extend_html']; ?> name="row[<?php echo $item['name']; ?>]" id="editor-<?php echo $item['name']; ?>" class="form-control editor" data-rule="<?php echo $item['rule']; ?>" rows="5" data-tip="<?php echo $item['tip']; ?>"><?php echo htmlentities($item['value']); ?></textarea>
                                            <?php break; case "array": ?>
                                            <dl <?php echo $item['extend_html']; ?> class="fieldlist" data-name="row[<?php echo $item['name']; ?>]">
                                                <dd>
                                                    <ins><?php echo isset($item["setting"]["key"])&&$item["setting"]["key"]?$item["setting"]["key"]:__('Array key'); ?></ins>
                                                    <ins><?php echo isset($item["setting"]["value"])&&$item["setting"]["value"]?$item["setting"]["value"]:__('Array value'); ?></ins>
                                                </dd>
                                                <dd><a href="javascript:;" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a></dd>
                                                <textarea name="row[<?php echo $item['name']; ?>]" class="form-control hide" cols="30" rows="5"><?php echo htmlentities($item['value']); ?></textarea>
                                            </dl>
                                            <?php break; case "date": ?>
                                            <input <?php echo $item['extend_html']; ?> type="text" name="row[<?php echo $item['name']; ?>]" value="<?php echo htmlentities($item['value']); ?>" class="form-control datetimepicker" data-date-format="YYYY-MM-DD" data-tip="<?php echo $item['tip']; ?>" data-rule="<?php echo $item['rule']; ?>"/>
                                            <?php break; case "time": ?>
                                            <input <?php echo $item['extend_html']; ?> type="text" name="row[<?php echo $item['name']; ?>]" value="<?php echo htmlentities($item['value']); ?>" class="form-control datetimepicker" data-date-format="HH:mm:ss" data-tip="<?php echo $item['tip']; ?>" data-rule="<?php echo $item['rule']; ?>"/>
                                            <?php break; case "datetime": ?>
                                            <input <?php echo $item['extend_html']; ?> type="text" name="row[<?php echo $item['name']; ?>]" value="<?php echo htmlentities($item['value']); ?>" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-tip="<?php echo $item['tip']; ?>" data-rule="<?php echo $item['rule']; ?>"/>
                                            <?php break; case "datetimerange": ?>
                                            <input <?php echo $item['extend_html']; ?> type="text" name="row[<?php echo $item['name']; ?>]" value="<?php echo htmlentities($item['value']); ?>" class="form-control datetimerange" data-tip="<?php echo $item['tip']; ?>" data-rule="<?php echo $item['rule']; ?>"/>
                                            <?php break; case "number": ?>
                                            <input <?php echo $item['extend_html']; ?> type="number" name="row[<?php echo $item['name']; ?>]" value="<?php echo htmlentities($item['value']); ?>" class="form-control" data-tip="<?php echo $item['tip']; ?>" data-rule="<?php echo $item['rule']; ?>"/>
                                            <?php break; case "checkbox": ?>
                                            <div class="checkbox">
                                            <?php if(is_array($item['content']) || $item['content'] instanceof \think\Collection || $item['content'] instanceof \think\Paginator): if( count($item['content'])==0 ) : echo "" ;else: foreach($item['content'] as $key=>$vo): ?>
                                            <label for="row[<?php echo $item['name']; ?>][]-<?php echo $key; ?>"><input id="row[<?php echo $item['name']; ?>][]-<?php echo $key; ?>" name="row[<?php echo $item['name']; ?>][]" type="checkbox" value="<?php echo $key; ?>" data-tip="<?php echo $item['tip']; ?>" <?php if(in_array(($key), is_array($item['value'])?$item['value']:explode(',',$item['value']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </div>
                                            <?php break; case "radio": ?>
                                            <div class="radio">
                                            <?php if(is_array($item['content']) || $item['content'] instanceof \think\Collection || $item['content'] instanceof \think\Paginator): if( count($item['content'])==0 ) : echo "" ;else: foreach($item['content'] as $key=>$vo): ?>
                                            <label for="row[<?php echo $item['name']; ?>]-<?php echo $key; ?>"><input id="row[<?php echo $item['name']; ?>]-<?php echo $key; ?>" name="row[<?php echo $item['name']; ?>]" type="radio" value="<?php echo $key; ?>" data-tip="<?php echo $item['tip']; ?>" <?php if(in_array(($key), is_array($item['value'])?$item['value']:explode(',',$item['value']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </div>
                                            <?php break; case "select": case "selects": ?>
                                            <select <?php echo $item['extend_html']; ?> name="row[<?php echo $item['name']; ?>]<?php echo $item['type']=='selects'?'[]':''; ?>" class="form-control selectpicker" data-tip="<?php echo $item['tip']; ?>" <?php echo $item['type']=='selects'?'multiple':''; ?>>
                                                <?php if(is_array($item['content']) || $item['content'] instanceof \think\Collection || $item['content'] instanceof \think\Paginator): if( count($item['content'])==0 ) : echo "" ;else: foreach($item['content'] as $key=>$vo): ?>
                                                <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($item['value'])?$item['value']:explode(',',$item['value']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                            <?php break; case "image": case "images": ?>
                                            <div class="form-inline">
                                                <input id="c-<?php echo $item['name']; ?>" class="form-control" size="50" name="row[<?php echo $item['name']; ?>]" type="text" value="<?php echo htmlentities($item['value']); ?>" data-tip="<?php echo $item['tip']; ?>">
                                                <span><button type="button" id="faupload-<?php echo $item['name']; ?>" class="btn btn-danger faupload" data-input-id="c-<?php echo $item['name']; ?>" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp,image/webp" data-multiple="<?php echo $item['type']=='image'?'false':'true'; ?>" data-preview-id="p-<?php echo $item['name']; ?>"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                                <span><button type="button" id="fachoose-<?php echo $item['name']; ?>" class="btn btn-primary fachoose" data-input-id="c-<?php echo $item['name']; ?>" data-mimetype="image/*" data-multiple="<?php echo $item['type']=='image'?'false':'true'; ?>"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                                                <span class="msg-box n-right" for="c-<?php echo $item['name']; ?>"></span>
                                                <ul class="row list-inline faupload-preview" id="p-<?php echo $item['name']; ?>"></ul>
                                            </div>
                                            <?php break; case "file": case "files": ?>
                                            <div class="form-inline">
                                                <input id="c-<?php echo $item['name']; ?>" class="form-control" size="50" name="row[<?php echo $item['name']; ?>]" type="text" value="<?php echo htmlentities($item['value']); ?>" data-tip="<?php echo $item['tip']; ?>">
                                                <span><button type="button" id="faupload-<?php echo $item['name']; ?>" class="btn btn-danger faupload" data-input-id="c-<?php echo $item['name']; ?>" data-multiple="<?php echo $item['type']=='file'?'false':'true'; ?>"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                                <span><button type="button" id="fachoose-<?php echo $item['name']; ?>" class="btn btn-primary fachoose" data-input-id="c-<?php echo $item['name']; ?>" data-multiple="<?php echo $item['type']=='file'?'false':'true'; ?>"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                                                <span class="msg-box n-right" for="c-<?php echo $item['name']; ?>"></span>
                                            </div>
                                            <?php break; case "switch": ?>
                                            <input id="c-<?php echo $item['name']; ?>" name="row[<?php echo $item['name']; ?>]" type="hidden" value="<?php echo $item['value']?1:0; ?>">
                                            <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-<?php echo $item['name']; ?>" data-yes="1" data-no="0">
                                                <i class="fa fa-toggle-on text-success <?php if(!$item['value']): ?>fa-flip-horizontal text-gray<?php endif; ?> fa-2x"></i>
                                            </a>
                                            <?php break; case "bool": ?>
                                            <label for="row[<?php echo $item['name']; ?>]-yes"><input id="row[<?php echo $item['name']; ?>]-yes" name="row[<?php echo $item['name']; ?>]" type="radio" value="1" <?php echo !empty($item['value'])?'checked':''; ?> data-tip="<?php echo $item['tip']; ?>" /> <?php echo __('Yes'); ?></label>
                                            <label for="row[<?php echo $item['name']; ?>]-no"><input id="row[<?php echo $item['name']; ?>]-no" name="row[<?php echo $item['name']; ?>]" type="radio" value="0" <?php echo !empty($item['value'])?'':'checked'; ?> data-tip="<?php echo $item['tip']; ?>" /> <?php echo __('No'); ?></label>
                                            <?php break; case "city": ?>
                                            <div style="position:relative">
                                            <input <?php echo $item['extend_html']; ?> type="text" name="row[<?php echo $item['name']; ?>]" id="c-<?php echo $item['name']; ?>" value="<?php echo htmlentities($item['value']); ?>" class="form-control" data-toggle="city-picker" data-tip="<?php echo $item['tip']; ?>" data-rule="<?php echo $item['rule']; ?>" />
                                            </div>
                                            <?php break; case "selectpage": case "selectpages": ?>
                                            <input <?php echo $item['extend_html']; ?> type="text" name="row[<?php echo $item['name']; ?>]" id="c-<?php echo $item['name']; ?>" value="<?php echo htmlentities($item['value']); ?>" class="form-control selectpage" data-source="<?php echo url('general.config/selectpage'); ?>?id=<?php echo $item['id']; ?>" data-primary-key="<?php echo $item['setting']['primarykey']; ?>" data-field="<?php echo $item['setting']['field']; ?>" data-multiple="<?php echo $item['type']=='selectpage'?'false':'true'; ?>" data-tip="<?php echo $item['tip']; ?>" data-rule="<?php echo $item['rule']; ?>" />
                                            <?php break; case "custom": ?>
                                            <?php echo $item['extend_html']; break; endswitch; ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>

                                </td>
                                <?php if(\think\Config::get('app_debug')): ?>
                                <td><?php echo "{\$site.". $item['name'] . "}"; ?></td>
                                <td><?php if($item['id']>18): ?><a href="javascript:;" class="btn-delcfg text-muted" data-name="<?php echo $item['name']; ?>"><i class="fa fa-times"></i></a><?php endif; ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td></td>
                                <td>
                                    <div class="layer-footer">
                                        <button type="submit" class="btn btn-primary btn-embossed disabled"><?php echo __('OK'); ?></button>
                                        <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                                    </div>
                                </td>
                                <?php if(\think\Config::get('app_debug')): ?>
                                <td></td>
                                <td></td>
                                <?php endif; ?>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="tab-pane fade" id="addcfg">
                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="<?php echo url('general.config/add'); ?>">
                    <?php echo token(); ?>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Group'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <select name="row[group]" class="form-control selectpicker">
                                <?php if(is_array($groupList) || $groupList instanceof \think\Collection || $groupList instanceof \think\Paginator): if( count($groupList)==0 ) : echo "" ;else: foreach($groupList as $key=>$vo): ?>
                                <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"basic"))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Type'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <select name="row[type]" id="c-type" class="form-control selectpicker">
                                <?php if(is_array($typeList) || $typeList instanceof \think\Collection || $typeList instanceof \think\Paginator): if( count($typeList)==0 ) : echo "" ;else: foreach($typeList as $key=>$vo): ?>
                                <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"string"))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label col-xs-12 col-sm-2"><?php echo __('Name'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" class="form-control" id="name" name="row[name]" value="" data-rule="required; length(3~30); remote(general/config/check)"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="control-label col-xs-12 col-sm-2"><?php echo __('Title'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" class="form-control" id="title" name="row[title]" value="" data-rule="required"/>
                        </div>
                    </div>
                    <div class="form-group hidden tf tf-selectpage tf-selectpages">
                        <label for="c-selectpage-table" class="control-label col-xs-12 col-sm-2"><?php echo __('Selectpage table'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <select id="c-selectpage-table" name="row[setting][table]" class="form-control selectpicker" data-live-search="true">
                                <option value=""><?php echo __('Please select table'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group hidden tf tf-selectpage tf-selectpages">
                        <label for="c-selectpage-primarykey" class="control-label col-xs-12 col-sm-2"><?php echo __('Selectpage primarykey'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <select name="row[setting][primarykey]" class="form-control selectpicker" id="c-selectpage-primarykey"></select>
                        </div>
                    </div>
                    <div class="form-group hidden tf tf-selectpage tf-selectpages">
                        <label for="c-selectpage-field" class="control-label col-xs-12 col-sm-2"><?php echo __('Selectpage field'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <select name="row[setting][field]" class="form-control selectpicker" id="c-selectpage-field"></select>
                        </div>
                    </div>
                    <div class="form-group hidden tf tf-selectpage tf-selectpages">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Selectpage conditions'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <dl class="fieldlist" data-name="row[setting][conditions]">
                                <dd>
                                    <ins><?php echo __('Field title'); ?></ins>
                                    <ins><?php echo __('Field value'); ?></ins>
                                </dd>

                                <dd><a href="javascript:;" class="append btn btn-sm btn-success"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a></dd>
                                <textarea name="row[setting][conditions]" class="form-control hide" cols="30" rows="5"></textarea>
                            </dl>
                        </div>
                    </div>
                    <div class="form-group hidden tf tf-array">
                        <label for="c-array-key" class="control-label col-xs-12 col-sm-2"><?php echo __('Array key'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" name="row[setting][key]" class="form-control" id="c-array-key">
                        </div>
                    </div>
                    <div class="form-group hidden tf tf-array">
                        <label for="c-array-value" class="control-label col-xs-12 col-sm-2"><?php echo __('Array value'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" name="row[setting][value]" class="form-control" id="c-array-value">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="value" class="control-label col-xs-12 col-sm-2"><?php echo __('Value'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" class="form-control" id="value" name="row[value]" value="" data-rule=""/>
                        </div>
                    </div>
                    <div class="form-group hide" id="add-content-container">
                        <label for="content" class="control-label col-xs-12 col-sm-2"><?php echo __('Content'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <textarea name="row[content]" id="content" cols="30" rows="5" class="form-control" data-rule="required(content)">value1|title1
value2|title2</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tip" class="control-label col-xs-12 col-sm-2"><?php echo __('Tip'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" class="form-control" id="tip" name="row[tip]" value="" data-rule=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rule" class="control-label col-xs-12 col-sm-2"><?php echo __('Rule'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <div class="input-group pull-left">
                                <input type="text" class="form-control" id="rule" name="row[rule]" value="" data-tip="<?php echo __('Rule tips'); ?>"/>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" type="button"><?php echo __('Choose'); ?></button>
                                    <ul class="dropdown-menu pull-right rulelist">
                                        <?php if(is_array($ruleList) || $ruleList instanceof \think\Collection || $ruleList instanceof \think\Paginator): $i = 0; $__LIST__ = $ruleList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                                        <li><a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $item; ?><span class="text-muted">(<?php echo $key; ?>)</span></a></li>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </ul>
                                </span>
                            </div>
                            <span class="msg-box n-right" for="rule"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="visible" class="control-label col-xs-12 col-sm-2"><?php echo __('Visible condition'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" class="form-control" id="visible" name="row[visible]" value="" data-rule=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="extend" class="control-label col-xs-12 col-sm-2"><?php echo __('Extend'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <textarea name="row[extend]" id="extend" cols="30" rows="5" class="form-control" data-tip="<?php echo __('Extend tips'); ?>" data-rule="required(extend)" data-msg-extend="当类型为自定义时，扩展属性不能为空"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"></label>
                        <div class="col-xs-12 col-sm-4">
                            <?php if(!\think\Config::get('app_debug')): ?>
                            <button type="button" class="btn btn-primary disabled"><?php echo __('Only work at development environment'); ?></button>
                            <?php else: ?>
                            <button type="submit" class="btn btn-primary btn-embossed"><?php echo __('OK'); ?></button>
                            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                            <?php endif; ?>
                        </div>
                    </div>

                </form>

            </div>
            <!--@formatter:on-->
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
