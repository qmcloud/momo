<?php

namespace App\Admin\Forms\Settings;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Upload extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '上传';

    public $option_name = 'settings_upload';
    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        //dump($request->all());
        $this->BaseValidator($request->all())->validate();

        $res = \App\Models\Option::updateOrCreate(
            ['option_name' => $this->option_name], ['option_value' => json_encode($request->all(), JSON_UNESCAPED_UNICODE)]
        );

        if ($res) {
            admin_success('操作成功.');
        } else {
            admin_error('操作失败.');
        }

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->number('file_size', '文件上传大小限制')->min(0)->help('0为不限制大小，单位：M')->rules('required');
        $this->tags('file_ext', '允许上传的文件后缀')->help('多个后缀用逗号隔开，不填写则不限制类型')->rules('required');
        $this->number('image_size', '图片上传大小限制')->min(0)->help('0为不限制大小，单位：M')->rules('required');
        $this->tags('image_ext', '允许上传的图片后缀')->help('多个后缀用逗号隔开，不填写则不限制类型')->rules('required');
        $this->number('thumbnail_size', '缩略图尺寸')->min(0);
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        $res = \App\Models\Option::where(['option_name'=>$this->option_name])->get()->toArray();

        return empty($res)?[
            'file_size'       => 100,
            'file_ext'      => ['doc', 'docx', 'xls', 'ppt', 'pptx', 'pdf', 'wps', 'txt', 'rar', 'zip', 'gz', 'bz2', '7z'],
            'image_size'       => 100,
            'image_ext'      => ['gif', 'bmp', 'jpeg', 'png'],
        ]:json_decode($res[0]['option_value'],true);
    }

    /**
     * Get a validator for an incoming login request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function BaseValidator(array $data)
    {
        return Validator::make($data, [

        ]);
    }
}
