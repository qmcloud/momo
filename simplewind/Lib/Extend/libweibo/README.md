
=================

用于新浪微博开放平台的PHP SDK. 内含能直接使用的DEMO.

SAE(Sina App Engine, http://sae.sina.com.cn )已内置SDK，不需要下载，使用前需要手工调用 require_once('saetv2.ex.class.php');

composer
-----
composer.phar require xiaosier/libweibo:dev-master

更新
-----

+ 2013年2月20日 修改V2版一处notice
+ 2011年12月16日 修改V2版两处手误
+ 2011年10月21日 发布V2版PHP SDK，基于 http://open.weibo.com/wiki/API%E6%96%87%E6%A1%A3_V2 中的最新接口封装。
+ 2011年6月16日 发布OAuth2版PHP SDK，删除Basic认证的SDK（微博开放平台已不支持Basic认证）
+ 2010年11月17日 修改了demo程序默认callback url在某些访问方式下不正确的问题.
+ 2010年6月29日Basic认证版本&OAuth版本添加verify_credentials函数,用于获取当前用户信息,OAuth添加update_avatar更新头像
+ 2010年5月5日 Basic 认证版本添加图片发布支持
+ 2010年5月12日 OAuth 认证版本添加图片支持

说明
-----
**Demo演示地址**

+ V2版Demo: http://weibosdk.sinaapp.com/
+ 站内应用Demo: http://apps.weibo.com/weibosdk
+ OAuth版Demo: http://saettest.sinaapp.com/

**如何申请API Key**

你需要有一个新浪微博开放平台的API Key.这里申请: http://open.weibo.com

**关于Class的功能**

+ 基于OAuth认证.
+ 完成至2011年10月21日止，全部接口的封装

**新浪微博V2版PHPSDK Demo使用教程**

+ 1.在open.weibo.com创建应用，得到API KEY，设置“授权设置”中的“应用回调页”地址为"http://host/callback.php"，其中host为网站域名。
+ 2.下载Demo,然后解压,修改config.php中的WB_AKEY为App Key，WB_SKEY为App Secret，WB_CALLBACK_URL为刚才填入的回调页地址。
+ 3.上传到PHP空间即可

**新浪微博站内应用Demo使用教程**
+ 1.在open.weibo.com创建站内应用，得到API KEY
+ 2.编辑应用属性，设置"应用页面"中的“站内应用地址”
+ 3.下载，解压，修改config.php中的WB_AKEY为App Key，WB_SKEY为App Secret，CANVAS_PAGE为“应用页面”中设置的”站内应用地址“
+ 4.上传代码到PHP空间
+ 5.编辑应用属性，设置"应用页面"中的"应用实际地址"为刚刚上传代码的apps.php的地址，比如："http://xxxxx.sinaapp.com/apps.php"，设置“Iframe高度“为2000px。
+ 6.访问刚刚设置的“站内应用地址”即可。

**OAuth版Demo使用教程**
+ 1.在open.weibo.com创建应用，得到API KEY
+ 2.下载,然后解压,修改config.php中的WB_AKEY为App Key，WB_SKEY为App Secret。
+ 3.上传到PHP空间即可


Bug tracker
-----------

Have a bug? Please create an issue here on GitHub!

https://github.com/xiaosier/libweibo/issues


Authors
-------

+ http://weibo.com/lazypeople
+ http://lazy.changes.com.cn


License
---------------------

Copyright 2011 SINA, Inc.
Copyright 2011 SAE

Licensed under the Apache License, Version 2.0: http://www.apache.org/licenses/LICENSE-2.0