<?php

namespace App\Admin\Forms\Settings;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class App extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'App版本管理';

    public $option_name = 'settings_video';
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
        $this->switch('isup','强制更新')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);
        $this->text('apk_ver','APP版本号')->help('安卓APP最新的版本号，请勿随意修改');
        $this->text('apk_url','APP下载链接')->help('安卓最新版APK下载链接');
        $this->text('apk_des','APK更新说明')->help('APK更新说明（200字以内）');
        $this->text('ipa_ver','IPA版本号')->help('IOS APP最新的版本号，请勿随意修改');
        $this->text('ios_shelves','IPA上架版本号')->help('IOS上架审核中版本的版本号(用于上架期间隐藏上架版本部分功能,不要和IPA版本号相同)');
        $this->text('ipa_url','IPA下载链接')->help('IOS最新版IPA下载链接');
        $this->text('ipa_des','IPA更新说明')->help('IPA更新说明（200字以内）');
        $this->image('qr_url','二维码下载链接');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        $res = \App\Models\Option::where(['option_name'=>$this->option_name])->get()->toArray();

        return empty($res)?[]:json_decode($res[0]['option_value'],true);
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
