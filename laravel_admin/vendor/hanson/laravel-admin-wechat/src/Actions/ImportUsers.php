<?php

namespace Hanson\LaravelAdminWechat\Actions;

use Encore\Admin\Actions\Action;
use Hanson\LaravelAdminWechat\Jobs\ImportUsers as ImportUsersJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ImportUsers extends Action
{
    public $name = 'import users';

    protected $selector = '.import-users';

    public function handle(Request $request)
    {
        $key = config('admin.extensions.wechat.admin_current_key', 'wechat.admin.current');

        $appId = Cache::get($key);

        ImportUsersJob::dispatch($appId);

        return $this->response()->success('后台同步用户中，请耐心等待')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-success import-users"><i class="fa fa-refresh"></i> 同步用户</a>
HTML;
    }
}
