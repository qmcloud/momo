<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:67:"/www/wwwroot/live/public/../application/admin/view/bdtts/index.html";i:1684603903;s:60:"/www/wwwroot/live/application/admin/view/layout/default.html";i:1671020443;s:57:"/www/wwwroot/live/application/admin/view/common/meta.html";i:1671020443;s:59:"/www/wwwroot/live/application/admin/view/common/script.html";i:1671020443;}*/ ?>
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
                                <div class="tab-content">
    <div class="tab-pane active" id="info19">
        <div class="well">
            百度语音合成集成api
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    使用须知
                </strong>
            </div>
            <div class="panel-body">
                注册百度开发者中心后创建语音应用
                价格标准参照<a href="https://ai.baidu.com/ai-doc/SPEECH/Nk38y8pjq">百度</a>
            </div>
        </div>
        <!-- 内容 -->
            <div class="row">
    <form class="form-horizontal form-ajax" role="form" data-toggle="validator" method="POST" id="form"  action="<?php echo addon_url('bdtts/index/index');; ?>">
        <div class="col-xs-9">
            <div class="form-inline" data-toggle="cxselect">
                <label class="control-label">语速</label>
                <select class="form-control " data-style="btn-primary" data-live-search="true"  name="spd"  style="min-width: 120px;">
                    <option value="5">自动</option>                  
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                </select>
                <label class="control-label">语调</label>
                <select class=" form-control" name="pit"  style="min-width: 120px;">
                    <option value="5">自动</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>

                </select>
                <label class="control-label">音量</label>
                <select class=" form-control" name="vol"  style="min-width: 120px;">
                    <option value="5">自动</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                </select>

                <label class="control-label">发音人</label>
                <select class=" form-control" name="per"  style="min-width: 120px;">
                    <option value="0">女生</option>
                    <option value="1">男生</option>
                    <option value="3">度逍遥</option>
                    <option value="4">度丫丫</option>                   
                </select>

                
            </div>
        </div>

        <div class="col-xs-3 text-right">
            <h6><a class="btn btn-success" href="javascript:;" onclick="$('#form').submit();"><i class="fa fa-play"></i> 启动</a></h6>
        </div>
        <div class="col-xs-12">
            <textarea class="form-control" rows="8" name="tex" placeholder="为保证语音合成质量，请将单次请求长度控制在 1000 bytes以内。（汉字约为300个）" data-rule="required" ></textarea>
        </div>
        </form>
    </div>

    <div class="row" style="">
            <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    结果
                </strong>
            </div>
            <div class="panel-body">
                <audio loop="" controls="" id="testingaudio">
                    <source src="" type="audio/wav">
                    您的浏览器不支持 audio 元素。
                </audio>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    API接口请求地址
                </strong>
            </div>
            <div class="panel-body">                
                <a href="<?php echo addon_url('bdtts/index/index',[],true,true);; ?>">
                    <?php echo addon_url('bdtts/index/index',[],true,true);; ?>
                </a>
                <br/>
                <span>
                    http://你的域名<?php echo addon_url('bdtts/index/index',[],true,false);; ?>
                </span>
                <br/>
                请求方式 <span class="label label-success">POST</span>
            </div>
    </div>


        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    参数
                </strong>
            </div>
            <div class="panel-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                名称
                            </th>
                            <th>
                                类型
                            </th>
                            <th>
                                必选
                            </th>
                            <th>
                                描述
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                tex
                            </td>
                            <td>
                                string
                            </td>
                            <td>
                                是
                            </td>
                            <td>
                                需要合成的文本字符串 （单次不要超过300个汉字）
                            </td>
                        </tr>

                        <tr>
                            <td>
                                spd
                            </td>
                            <td>
                                int
                            </td>
                            <td>
                                否
                            </td>
                            <td>
                                语速 取值范围：(1-9) 默认5
                            </td>
                        </tr>
                        <tr>
                            <td>
                                pit
                            </td>
                            <td>
                                int
                            </td>
                            <td>
                                否
                            </td>
                            <td>
                                语调 取值范围(1-9) 默认5
                            </td>
                        </tr>
                        <tr>
                            <td>
                                vol
                            </td>
                            <td>
                                int
                            </td>
                            <td>
                                否
                            </td>
                            <td>
                                音量 取值范围(1-15) 默认5
                            </td>
                        </tr>
                        <tr>
                            <td>
                                per
                            </td>
                            <td>
                                int
                            </td>
                            <td>
                                否
                            </td>
                            <td>
                                发音人选择, 0为女声，1为男声，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>
                    返回值
                </strong>
            </div>
            <div class="panel-body">
<pre>
{
    "code": 1,
    "msg": "成功",
    "data": {
        "realpath": "文件的硬盘地址:C:/www/202011063122424.wav",
        "filename": "网络访问地址:http://www.tetss.com/202011063122424.wav"
    },
    "url": "",
    "wait": 3
}
</pre>
失败返回
<pre>
{
    "code": 0,
    "msg": "失败",
    "data": {},
    "url": "",
    "wait": 3
}
</pre>



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
