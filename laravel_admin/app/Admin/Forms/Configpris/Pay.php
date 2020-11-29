<?php

namespace App\Admin\Forms\Configpris;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Pay extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '支付设置';

    public $option_name = 'configpris_pay';

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
        $this->switch('aliapp_switch', '支付宝APP')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ])->help('支付宝APP支付是否开启');

        $this->text('aliapp_partner',"支付宝合作者身份ID");

        $this->text('aliapp_seller_id',"支付宝登录账号");

        $this->textarea('aliapp_key_android',"支付宝安卓密钥");

        $this->text('aliapp_key_ios',"支付宝苹果密钥");

        $this->text('login_wx_xcx_appsecret',"微信小程序Appsecret");

        $this->switch('aliapp_pc',"支付宝PC")->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);

        $this->text('aliapp_check',"支付宝校验码");

        $this->switch('ios_switch',"苹果支付开关")->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);

        $this->switch('wx_switch_pc',"微信支付PC")->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);

        $this->switch('wx_switch',"微信支付")->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);

        $this->text('wx_appid',"微信开放平台移动应用AppID");

        $this->text('wx_appsecret',"微信开放平台移动应用appsecret");

        $this->text('wx_mchid',"微信商户号mchid");

        $this->text('wx_key',"微信密钥key");

        $this->switch('aliscan_switch',"支付宝-当面付-扫码付开关")->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);

        $this->text('wx_appsecret',"微信开放平台移动应用appsecret");

        $this->text('aliscan_appid', '支付宝-当面付-扫码付 应用ID')->help('账户中心->密钥管理->开放平台密钥');

        $this->textarea('aliscan_rsakey', '支付宝-当面付-扫码付 商户私钥')->help('填写对应签名算法类型(RSA2)的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310');

        $this->textarea('aliscan_pubkey', '支付宝-当面付-扫码付 支付宝公钥')->help('账户中心->密钥管理->开放平台密钥，找到添加了支付功能的应用，根据你的加密类型，查看支付宝公钥');


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
