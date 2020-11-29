<?php

namespace Encore\Cropper;

use Encore\Admin\Form\Field\ImageField;
use Encore\Admin\Form\Field\File;
use Encore\Admin\Admin;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Crop extends File
{   
    use ImageField;

    private $ratioW = 100;

    private $ratioH = 100;

    protected $view = 'laravel-admin-cropper::cropper';

    protected static $css = [
        '/vendor/laravel-admin-ext/cropper/cropper.min.css',
    ];

    protected static $js = [
        '/vendor/laravel-admin-ext/cropper/cropper.min.js',
        '/vendor/laravel-admin-ext/cropper/layer/layer.js'
    ];

    protected function preview()
    {
        if(!is_null($this->value()))
            return $this->objectUrl($this->value());
    }

    /**
     * [将Base64图片转换为本地图片并保存]
     * @E-mial wuliqiang_aa@163.com
     * @TIME   2017-04-07
     * @WEB    http://blog.iinu.com.cn
     * @param  [Base64] $base64_image_content [要保存的Base64]
     * @param  [目录] $path [要保存的路径]
     */
    private function base64_image_content($base64_image_content, $path)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            if (!file_exists($path)) {
                //检查是否有该文件夹，如果没有就创建，并给予755权限
                mkdir($path, 0755, true);
            }
            $filename = md5(microtime()) . ".{$type}";
            $all_path = $path . '/' . $filename;
            $content = base64_decode(str_replace($result[1], '', $base64_image_content));
            if (file_put_contents($all_path, $content)) {
                return ['path'=>$all_path, 'filename'=>$filename];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function prepare($base64)
    {
        if (empty($base64)) {
            $this->destroy();
            return $base64;
        } else if (preg_match('/data:image\/.*?;base64/is',$base64)) {
            //检查是否是base64编码
            //base64转图片缓存 返回的是绝对路径
            $image = $this->base64_image_content($base64,storage_path('app/public/images/base64img_cache'));
            if ($image !== false) {
                $image = new UploadedFile($image['path'],$image['filename']);
                $this->name = $this->getStoreName($image);
                $this->callInterventionMethods($image->getRealPath());
                $path = $this->uploadAndDeleteOriginal($image);
                return $path;
            } else {
                return 'lost';
            }
        } else {
            // 不是base64编码
            return $base64;
        }
    }

    public function cRatio($width,$height)
    {
        if (!empty($width) and is_numeric($width)) {
            $this->attributes['data-w'] = $width;
        } else {
            $this->attributes['data-w'] = $this->ratioW;
        }
        if (!empty($height) and is_numeric($height)) {
            $this->attributes['data-h'] = $height;
        } else {
            $this->attributes['data-h'] = $this->ratioH;
        }
        return $this;
    }

    public function render()
    {
        $this->name = $this->formatName($this->column);
        $cTitle = trans("admin_cropper.title");
        $cDone = trans("admin_cropper.done");
        $cOrigin = trans("admin_cropper.origin");
        $cClear = trans("admin_cropper.clear");
        $script = <<<EOT

        //图片类型预存

        function getMIME(url)
        {
            var preg = new RegExp('data:(.*);base64','i');
            var result = preg.exec(url);
            console.log(result)
            if (result != null) {
                return result[1];
            } else {
                var ext = url.substring(url.lastIndexOf(".") + 1).toLowerCase();
                return 'image/' + ext
            }
        }

        function cropper(imgSrc,cropperFileE)
        {
            var w = $(cropperFileE).attr('data-w');
            var h = $(cropperFileE).attr('data-h');
            var cropperImg = '<div id="cropping-div"><img id="cropping-img" src="'+imgSrc+'"><\/div>';
            //生成弹层模块
            layer.open({
                zIndex: 3000,
                type: 1,
                skin: 'layui-layer-demo', //样式类名
                area: ['800px', '600px'],
                closeBtn: 2, //第二种关闭按钮
                anim: 2,
                resize: false,
                shadeClose: false, //关闭遮罩关闭
                title: '$cTitle',
                content: cropperImg,
                btn: ['$cDone','$cOrigin','$cClear'],
                btn1: function(){
                    var cas = cropper.getCroppedCanvas({
                        width: w,
                        height: h
                    });
                    //剪裁数据转换base64
                    console.log(imgSrc)
                    var base64url = cas.toDataURL(getMIME(imgSrc));
                    //替换预览图
                    cropperFileE.nextAll('.cropper-img').attr('src',base64url);
                    //替换提交数据
                    cropperFileE.nextAll('.cropper-input').val(base64url);
                    //销毁剪裁器实例
                    cropper.destroy();
                    layer.closeAll('page');
                },
                btn2:function(){
                    //默认关闭框
                    cropperFileE.nextAll('.cropper-img').attr('src',imgSrc);
                    //替换提交数据
                    cropperFileE.nextAll('.cropper-input').val(imgSrc);
                    //销毁剪裁器实例
                    cropper.destroy();
                },
                btn3:function(){
                    //清空表单和选项
                    //销毁剪裁器实例
                    cropper.destroy();
                    layer.closeAll('page');
                    //清空预览图
                    cropperFileE.nextAll('.cropper-img').removeAttr('src');
                    //清空提交数据
                    cropperFileE.nextAll('.cropper-input').val('');
                    //清空文件选择器
                    cropperFileE.val('');
                }
            });

            var image = document.getElementById('cropping-img');
            var cropper = new Cropper(image, {
                aspectRatio: w / h,
                viewMode: 2,
            });
        }

        //选择按钮
        $('form div').on('click','.cropper-btn',function(){
            $(this).nextAll('.cropper-file').click()
            return false;
        });

        //在input file内容改变的时候触发事件
        $('form').on('change','.cropper-file',function(fileE){
            //获取input file的files文件数组;
            //这边默认只能选一个，但是存放形式仍然是数组，所以取第一个元素使用[0];
            var file = $(this)[0].files[0];
            //创建用来读取此文件的对象
            var reader = new FileReader();
            //使用该对象读取file文件
            reader.readAsDataURL(file);
            //读取文件成功后执行的方法函数
            reader.onload = function(e){
                //选择所要显示图片的img，要赋值给img的src就是e中target下result里面的base64编码格式的地址
                $(this).nextAll('.cropper-img').attr('src',e.target.result);
                //调取剪切函数（内部包含了一个弹出框）
                cropper(e.target.result,$(fileE.target));
                //向后两轮定位到隐藏起来的输入框体
                $(this).nextAll('.cropper-input').val(e.target.result);
            };
            return false;
        });

        //点击图片触发弹层
        $('form').on('click','.cropper-img',function(){
            cropper($(this).attr('src'),$(this).prevAll('.cropper-file'));
            return false;
        });

EOT;

        if (!$this->display) {
            return '';
        }

        Admin::script($script);
        return view($this->getView(), $this->variables(),['preview'=>$this->preview()]);
    }

}
