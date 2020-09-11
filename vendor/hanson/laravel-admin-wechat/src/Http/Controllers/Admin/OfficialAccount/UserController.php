<?php


namespace Hanson\LaravelAdminWechat\Http\Controllers\Admin\OfficialAccount;


use Encore\Admin\Grid;
use Hanson\LaravelAdminWechat\Actions\ImportUsers;
use Hanson\LaravelAdminWechat\Http\Controllers\Admin\BaseController;
use Hanson\LaravelAdminWechat\Models\WechatUser;

class UserController extends BaseController
{
    protected $title = '公众号用户';

    protected function grid()
    {
        $grid = new Grid(new WechatUser);

        $grid->model()->where('app_id', $this->appId);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('avatar', '头像')->image('', 64, 64);
        $grid->column('app_id', 'App Id');
        $grid->column('openid', 'Openid');
        $grid->column('nickname', '昵称');
        $grid->column('gender_readable', '性别');
        $grid->column('country', '国家');
        $grid->column('province', '省份');
        $grid->column('city', '城市');
        $grid->column('subscribed_at', '关注时间');

        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ImportUsers());
        });

        return $grid;
    }
}
