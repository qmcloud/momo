<?php


namespace Hanson\LaravelAdminWechat\Http\Controllers\Admin\MiniProgram;


use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Hanson\LaravelAdminWechat\Actions\ImportUsers;
use Hanson\LaravelAdminWechat\Http\Controllers\Admin\BaseController;
use Hanson\LaravelAdminWechat\Models\WechatConfig;
use Hanson\LaravelAdminWechat\Models\WechatUser;
use Illuminate\Support\Facades\Cache;

class UserController extends BaseController
{
    protected $title = '小程序用户';

    protected function grid()
    {
        $grid = new Grid(new WechatUser);

        $grid->model()->where('app_id', $this->appId);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('app_id', 'App Id');
        $grid->column('openid', 'Openid');
        $grid->column('nickname', '昵称');
        $grid->column('avatar', '头像')->image('', 100, 100);
        $grid->column('gender_readable', '性别');
        $grid->column('country', '国家');
        $grid->column('province', '省份');
        $grid->column('city', '城市');

        $grid->disableCreateButton();
        $grid->disableActions();

        return $grid;
    }
}
