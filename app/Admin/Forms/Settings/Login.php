<?php

namespace App\Admin\Forms\Settings;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Login extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '登录开关';

    public $option_name = 'settings_login';
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
        $this->switch('login_switch',"第三方登录开关")->help('关闭表示无第三方登录')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);
        $this->radio('login_type',"登录方式")->options(['0' => 'QQ', 'wx' => '微信','xl'=>'新浪']);
        $this->switch('share_switch',"分享开关")->help('关闭表示无分享')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);
        $this->radio('share_type',"分享方式")->options(['qq' => 'QQ','qzone'=>'QQ空间', 'wx' => '微信','wxpyq'=>'微信朋友圈','xlwb'=>'新浪微博'])->help('不选则表示无第三方分享');

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
