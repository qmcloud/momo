#基于PhalApi的图片上传拓展

### 1.安装和配置

#### 1.1 扩展包下载
从 PhalApi-Library 扩展库中下载获取 UCloud 扩展包，如使用：

git clone https://git.oschina.net/dogstar/PhalApi-Library.git
然后把 UCloud 目录复制到 ./PhalApi/Library/ 下，即：

cp ./PhalApi-Library/UCloud/ ./PhalApi/Library/ -R
到处安装完毕！接下是插件的配置。

#### 1.2 扩展包配置
我们需要在 ./Config/app.php 配置文件中追加以下配置：
##### 1.2.1 本地上传配置
```
    /**
     * 云上传引擎,支持local,oss,upyun
     */
    'UCloudEngine' => 'local',

    /**
     * 本地存储相关配置（UCloudEngine为local时的配置）
     */
    'UCloud' => array(
        //对应的文件路径
        'host' => 'http://localhost/PhalApi/Public/upload' 
    ),
```
对应的文件路径也可以独立绑定一个二级域名，然后绑定至upload目录，调用的图片将显示为http://image.xxx.com/项目名/文件名.jpg

再提一句，可能有些同学搞不懂对应文件路径，这里的upload路径会在Public中自动生成

##### 1.2.2 云图片上传配置
```
    /**
     * 云上传引擎,支持local,oss,upyun
     */
    'UCloudEngine' => 'oss',

    /**
     * 云上传对应引擎相关配置
     * 如果UCloudEngine不为local,则需要按以下配置
     */
    'UCloud' => array(
    	//上传的API地址,不带http://,以下api为阿里云OSS杭州节点
    	'api' => 'oss-cn-hangzhou.aliyuncs.com',

    	//统一的key
    	'accessKey' => '',
    	'secretKey' => '',

    	//自定义配置的空间
        'bucket' => '',
        'host' => 'http://image.xxx.com', //必带http:// 末尾不带/

        'timeout' => 90
    ),
```
### 2.入门使用
#### 2.1 入口注册
```
$loader->addDirs('Library');

//其他代码...

//云存储
DI()->ucloud = new UCloud_Lite();
```

### 3.示例：图片上传
先简单写个测试文件：
```
<html>
    <form method="POST" action="./?service=Upload.upload" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit">
    </form>
</html>
```
#### 3.1 文件上传接口
```
<?php
/*
 * +----------------------------------------------------------------------
 * | 上传接口
 * +----------------------------------------------------------------------
 * | Copyright (c) 2015 summer All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: summer <aer_c@qq.com> <qq7579476>
 * +----------------------------------------------------------------------
 * | This is not a free software, unauthorized no use and dissemination.
 * +----------------------------------------------------------------------
 * | Date
 * +----------------------------------------------------------------------
 */


class Api_Upload extends PhalApi_Api {

    /**
     * 获取参数
     * @return array 参数信息
     */
    public function getRules() {
        return array(
            'upload' => array(
                'file' => array(
                    'name' => 'file', 
                    'type' => 'file', 
                    'min' => 0, 
                    'max' => 1024 * 1024, 
                    'range' => array('image/jpg', 'image/jpeg', 'image/png'), 
                    'ext' => array('jpg', 'jpeg', 'png')
                ),
            ),
        );
    }

    /**
     * 上传文件
     * @return string $url 绝对路径
     * @return string $file 相对路径，用于保存至数据库，按项目情况自己决定吧
     */
    public function upload() {

        //设置上传路径 设置方法参考3.2
        DI()->ucloud->set('save_path',date('Y/m/d'));

        //新增修改文件名设置上传的文件名称
        DI()->ucloud->set('file_name', 'avatar');

        //上传表单名
        $res = DI()->ucloud->upfile($this->file);

        return $rs;
    }
}
?>
```
#### 3.2 设置上传路径
按照以上设置，将会自动生成4层目录(demo/2015/13/7/aaa.jpg)，demo其实为项目名称，你可以在每个项目入口设置一个常量等于项目名称，然后打开拓展library/UCloud/Lite.php找到$default_path，将该值设置为你设定的常量，或者为空（不是NULL），为空后你可以在设置上传路径里面设置（项目名/2015/12/07）也是可以的!