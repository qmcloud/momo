<?php


namespace Hanson\LaravelAdminWechat\Http\Controllers\Admin;


use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Hanson\LaravelAdminWechat\Models\WechatConfig;
use Illuminate\Support\Facades\Cache;

class ConfigController extends AdminController
{
    protected $title = '微信配置';

    protected $description = [
        'index' => '公众号/小程序 配置',
    ];

    protected function grid()
    {
        $grid = new Grid(new WechatConfig);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('name', '名称');
        $grid->column('type_readable', '类型');
        $grid->column('app_id', 'APP ID');
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new WechatConfig());

        $form->text('name', '名称')->required();
        $form->radio('type', '类型')->default(1)->options([1 => '公众号', 2 => '小程序']);
        $form->text('app_id', 'App id')->required();
        $form->text('secret', '秘钥')->required();
        $form->text('token', 'Token')->help('公众号才需填写');
        $form->text('aes_key', 'Aes Key')->help('公众号才需填写');

        $form->saved(function (Form $form) {
            Cache::forever('wechat.config.app_id.'.$form->model()->app_id, ['app_id' => $form->model()->app_id, 'secret' => $form->model()->secret, 'type' => $form->model()->type]);
        });

        return $form;
    }
}
