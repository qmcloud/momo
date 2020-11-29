<?php

namespace App\Admin\Forms\Configpris;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Cash extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '提现设置';

    public $option_name = 'configpris_cash';

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
        $this->number('cash_rate', "提现比例")->min(0)->help('提现一元人民币需要的票数');

        $this->number('cash_min', "提现最低额度（元）")->min(0)->help('可提现的最小额度，低于该额度无法提现');

        $this->number('cash_max_times', "每月提现次数")->min(0)->help('每月可提现最大次数，0表示不限制');

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
            'cash_rate' => 'min:0',
            'cash_min' => 'min:0',
            'cash_max_times' => 'min:0',
        ]);
    }
}
