<?php


namespace Hanson\LaravelAdminWechat\Http\Controllers\Admin;


use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Hanson\LaravelAdminWechat\Facades\ConfigService;
use Hanson\LaravelAdminWechat\Models\WechatConfig;
use Illuminate\Support\Facades\Cache;

class BaseController extends AdminController
{
    protected $config;

    protected $appId;

    public function index(Content $content)
    {
        Admin::disablePjax();

        if ($appId = request('app_id')) {
            Cache::forever(config('admin.extensions.wechat.admin_current_key', 'wechat.admin.current'), $appId);
        }

        $current = ConfigService::getCurrent();

        if (!$current) {
            admin_warning('出现错误', '请先添加微信配置');

            return redirect('admin/wechat/configs');
        }

        $this->config = $current;
        $this->appId = $current->app_id;

        Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) use ($current) {
            $configs = WechatConfig::query()->get(['app_id', 'name']);

            $navbar->left(view('wechat::dropdown', compact('configs', 'current')));
        });

        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($this->grid());
    }
}
