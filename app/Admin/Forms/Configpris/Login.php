<?php

namespace App\Admin\Forms\Configpris;

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
    public $title = '登录设置';

    public $option_name = 'configpris_login';
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
        $this->switch('bonus_switch', '登录奖励开关')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);

        $this->number('reg_reward',"登录奖励")->min(0)->help('新用户注册奖励（整数）');

        $this->text('login_wx_appid',"微信公众号Appid")->help('微信公众号appid（微信开放平台网页应用 APPID）');

        $this->text('login_wx_appsecret',"微信公众号Appsecret")->help('微信公众号appsecret（微信开放平台网页应用 AppSecret）');

        $this->text('login_wx_xcx_appid',"微信小程序Appid")->help('微信小程序Appid（微信开放平台网页应用 APPID）');

        $this->text('login_wx_xcx_appsecret',"微信小程序Appsecret")->help('微信小程序Appsecret（微信开放平台网页应用 Appsecret）');

        $this->switch('sendcode_switch',"短信验证码开关")->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ])->help('短信验证码开关,关闭后不再发送真实验证码，采用默认验证码123456');

        $this->text('ccp_sid',"容联云ACCOUNT SID");

        $this->text('ccp_token',"容联云AUTH TOKEN");

        $this->text('ccp_appid',"容联云应用APPID");

        $this->text('ccp_tempid',"容联云短信模板ID");

        $this->text('orderccp_tempid',"容联云订单短信模板ID");

        $this->switch('iplimit_switch',"短信验证码IP限制开关")->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);

        $this->number('iplimit_times', '短信验证码IP限制次数')->min(0);

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
