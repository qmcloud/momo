<?php

namespace App\Admin\Forms\Settings;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Seo extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'SEO设置';

    public $option_name = 'settings_seo';
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
        $this->text('site_seo_title','SEO标题');
        $this->text('site_seo_keywords','SEO关键字');
        $this->text('site_seo_description','SEO描述');
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
