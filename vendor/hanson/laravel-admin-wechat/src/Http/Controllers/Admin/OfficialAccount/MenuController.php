<?php


namespace Hanson\LaravelAdminWechat\Http\Controllers\Admin\OfficialAccount;


use Encore\Admin\Form;
use Hanson\LaravelAdminWechat\Facades\ConfigService;
use Hanson\LaravelAdminWechat\Http\Controllers\Admin\BaseController;
use Hanson\LaravelAdminWechat\Models\WechatConfig;

class MenuController extends BaseController
{
    protected $title = '菜单';

    protected function grid(bool $show = true)
    {
        if (!$show) {
            $app = ConfigService::getInstanceByAppId(request('app_id'));

            return $app->menu->create(json_decode(request('menu'), true)['menu']['button']);
        }

        $config = ConfigService::getCurrent();

        $app = ConfigService::getAdminCurrentApp();

        $menu = $app->menu->current();

        $form = new Form(new WechatConfig());

        $form->setAction('/admin/wechat/official-account/menu');

        $form->wechatMenu('menu', $config->name)->default(isset($menu['selfmenu_info']) ? $menu['selfmenu_info'] : null);
        $form->hidden('app_id')->default($config->app_id);

        $form->disableViewCheck()->disableEditingCheck()->disableCreatingCheck()->disableReset();

        return $form;
    }

    public function store()
    {
        $result = $this->grid(false);

        if ($result['errcode'] == 0) {
            admin_toastr('修改成功', 'success');
        } else {
            admin_toastr($result['errmsg'], 'error');
        }

        return redirect()->route('admin.wechat.menu');
    }
}
