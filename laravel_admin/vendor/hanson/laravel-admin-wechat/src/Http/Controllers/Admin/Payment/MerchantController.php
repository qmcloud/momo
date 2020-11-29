<?php


namespace Hanson\LaravelAdminWechat\Http\Controllers\Admin\Payment;


use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Hanson\LaravelAdminWechat\Actions\ImportUsers;
use Hanson\LaravelAdminWechat\Http\Controllers\Admin\BaseController;
use Hanson\LaravelAdminWechat\Models\WechatConfig;
use Hanson\LaravelAdminWechat\Models\WechatMerchant;
use Hanson\LaravelAdminWechat\Models\WechatUser;
use Illuminate\Support\Facades\Cache;

class MerchantController extends AdminController
{
    protected $title = '商户号';

    protected function grid()
    {
        $grid = new Grid(new WechatMerchant);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('name', '名称');
        $grid->column('type_readable', '类型');
        $grid->column('mch_id', '商户号');
        $grid->column('app_id', 'App Id');
        $grid->column('key', '秘钥');
        $grid->column('notify_url', '回调地址');

        $grid->disableActions();

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new WechatMerchant);

        $form->radio('type', '类型')->default(1)->options([1 => '普通商户号', 2 => '服务商']);
        $form->text('name', '名称')->required();
        $form->text('mch_id', '商户号')->required();
        $form->text('app_id', 'App Id')->required();
        $form->text('key', '秘钥')->required();
        $form->text('notify_url', '回调地址');

        $form->saved(function (Form $form) {
            Cache::forever('wechat.merchant.mch_id.'.$form->model()->mch_id, ['app_id' => $form->model()->app_id, 'mch_id' => $form->model()->mch_id, 'key' => $form->model()->key, 'notify_url' => $form->model()->notify_url]);
        });

        return $form;
    }
}
