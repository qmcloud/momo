<?php

namespace App\Admin\Forms\Configpris;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Live extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '直播设置';

    public $option_name = 'configpris_live';

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
        $this->switch('live_on', '直播总开关')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ])->help('关掉后app不再有直播功能');

        $this->switch('auth_islimit', '认证限制')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ])->help('主播开播是否需要身份认证');

        $this->switch('level_islimit', '直播等级控制')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ])->help('直播等级控制是否开启');

        $this->number('level_limit', "直播限制等级")->min(0)->help('直播等级限制开启时，最低开播等级（用户等级）');

        $this->number('speak_limit', "发言等级限制")->min(0)->help('0表示无限制');

        $this->number('barrage_limit', "发言等级限制")->min(0)->help('0表示无限制');

        $this->number('barrage_fee', "弹幕费用")->min(0)->help('每条弹幕的价格（整数）');

        $this->number('userlist_time', "用户列表请求间隔")->min(0)->help('s 直播间用户列表刷新间隔时间 注：不小于5s');

        $this->number('mic_limit', "连麦等级限制")->min(0)->help('0表示无限制');

        $this->text('chatserver', "聊天服务器带端口")->help('格式：http://域名(:端口) 或者 http://IP(:端口)');

        $this->radio('live_sdk', "直播模式选择")->options([1 => "直播模式 ", 2 => "直播+连麦模式"]);

        $this->radio('cdn_switch', "CDN")->options([1 => "阿里云 ", 2 => "腾讯云", 3 => "七牛云", 4 => "网宿", 5 => "网易云", 6 => "奥点云", 7 => "本地流服务器"])
            ->when(1, function (Form $form) {

                $form->text('push_url', '推流服务器地址')->help('格式：域名(:端口) 或者 IP(:端口)');
                $form->text('auth_key_push', '推流鉴权KEY')->help('推流鉴权KEY 留空表示不启用');
                $form->text('auth_length_push', "推流鉴权有效时长")->help('推流鉴权有效时长（秒）');
                $form->text('pull_url', "播流服务器地址")->help('格式：域名(:端口) 或者 IP(:端口)');
                $form->text('auth_key_pull', "播流鉴权KEY")->help('播流鉴权KEY 留空表示不启用');
                $form->number('auth_length_pull', "播流鉴权有效时长")->help('播流鉴权有效时长（秒）');
                $form->text('aliy_key_id', '阿里云AccessKey ID');
                $form->text('aliy_key_secret', '阿里云AccessKey Secret');

            })->when(2, function (Form $form) {

                $form->text('tx_appid', '直播appid');
                $form->text('tx_bizid', '直播bizid');
                $form->text('tx_push_key', '直播推流防盗链Key');
                $form->text('tx_api_key', '直播API鉴权key');
                $form->text('tx_push', '直播推流域名');
                $form->text('tx_pull', '直播播流域名');

            })->when(7, function (Form $form) {

                $form->text('bd_appid', '本地直播appid');
                $form->text('bd_bizid', '本地直播bizid');
                $form->text('bd_push_key', '本地直播推流防盗链Key');
                $form->text('bd_api_key', '本地直播API鉴权key');
                $form->text('bd_push', '本地直播推流域名');
                $form->text('bd_pull', '本地直播播流域名');

            });


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
