<?php

namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field;

class WangEditor extends Field
{
    protected $view = 'admin.wang-editor';

    protected static $css = [
        '/vendor/wangEditor-3.1.1/release/wangEditor.css',
    ];

    protected static $js = [
        '/vendor/wangEditor-3.1.1/release/wangEditor.js',
    ];

    public function render()
    {
        $name = $this->formatName($this->column);
        $uploadUrl = route('admin-up');
        $this->script = <<<EOT

var E = window.wangEditor
var editor = new E('#{$this->id}');
editor.customConfig.uploadFileName = 'mypic[]';
editor.customConfig.uploadImgHeaders = {
    'X-CSRF-TOKEN': $('input[name="_token"]').val()
}
editor.customConfig.zIndex = 0;
// 上传路径
editor.customConfig.uploadImgServer = '{$uploadUrl}';
editor.customConfig.onchange = function (html) {
    $('input[name=$name]').val(html);
}
editor.customConfig.uploadImgHooks = {
    customInsert: function (insertImg, result, editor) {
        if (typeof(result.length) != "undefined") {
            for (var i = 0; i <= result.length - 1; i++) {
                var j = i;
                var url = result[i].newFileName;
                insertImg(url);
            }
            toastr.success(result[j]['info']);
        }

        switch (result['ResultData']) {
            case 6:
                toastr.error("最多可以上传4张图片");
                break;
            case 5:
                toastr.error("请选择一个文件");
                break;
            case 4:
                toastr.error("上传失败");
                break;
            case 3:
                toastr.error(result['info']);
                break;
            case 2:
                toastr.error("文件类型不合法");
                break;
            case 1:
                toastr.error(result['info']);
                break;
        }
    }
}
editor.create()

EOT;
        return parent::render();
    }
}
