<?php

namespace App\Admin\Forms\Settings;

use Encore\Admin\Grid;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Config;
class Basic extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '基本';

    public $option_name = 'settings_base';
    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $this->BaseValidator($request->all())->validate();
        $data=$request->all();

        $website_logo="website_logo";
        empty($this->uploadimgs($request,$website_logo))?$data[$website_logo]=$this->uploadimgs($request,$website_logo):[];

        $website_text_logo="website_text_logo";
        empty($this->uploadimgs($request,$website_text_logo))?$data[$website_text_logo]=$this->uploadimgs($request,$website_text_logo):[];

        $res = \App\Models\Option::updateOrCreate(
            ['option_name' => $this->option_name], ['option_value' => json_encode($data, JSON_UNESCAPED_UNICODE)]
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
        $this->switch('website_enable', '站点开关')->states([
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
        ])->help('站点关闭后将不能访问，后台可正常登录');
        $this->text('website_title', '站点标题');
        $this->text('website_slogan', '站点标语')->help('请上传图片格式');
        $this->text('website_logo', '站点LOGO地址');
        //$this->image('website_logo', '站点LOGO')->help('请上传图片格式');
        $this->qiniuImages('website_logo', '商品图')->sortable(); // 普通用法
        $this->text('website_text_logo', '站点LOGO文字地址');
        $this->image('website_text_logo', '站点LOGO文字');

        $this->textarea('website_desc', '站点描述')->help('网站描述，有利于搜索引擎抓取相关信息');
        $this->text('website_keywords', '站点关键词')->help('网站搜索引擎关键字');
        $this->text('website_copyright', '版权信息')->help("调用方式：config('website_copyright')");
        $this->text('website_icp', '备案信息')->help("调用方式：config('website_icp')");
        $this->textarea('website_statistics', '网站统计代码')->help("网站统计代码，支持百度、Google、cnzz等，调用方式：config('website_statistics')");

        $this->saving(function (\Encore\Admin\Form $form) {
            $paths = \Hanson\LaravelAdminQiniu\Qiniu::getPaths(request('qiniu_website_logo')); // 需要 qiniu_ 作为前缀的字段
            echo $paths;
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

    protected function uploadimgs($request,$name){
        if(!empty($request->file($name))){ //"website_text_logo"
            $text_logopath= \App\Models\Image::upload_img($request->file($name));
            if($text_logopath){
                return $text_logopath;
            }else{
                return "";
            }

        }else{
            return "";
        }
    }
}
