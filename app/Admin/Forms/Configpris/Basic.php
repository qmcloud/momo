<?php

namespace App\Admin\Forms\Configpris;

use App\Models\Option;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use think\Model;


class Basic extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '客服设置';

    public $option_name = 'configpris_base';

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
            ['option_name'=>$this->option_name],['option_value' => json_encode($request->all(),JSON_UNESCAPED_UNICODE)]
        );

        if($res){
            admin_success('操作成功.');
        }else{
            admin_error('操作失败.');
        }

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        //$this->method("POST");
        $this->switch('service_switch', '客服')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ]);
        $this->text('service_url', '客服链接')->help("注册链接如：http://www.xxxx.com/reg/index?yx_from=210260");
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
             'service_url'          => 'required',
         ]);
    }

}
