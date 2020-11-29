<?php

namespace Hanson\LaravelAdminWechat\Actions;

use Encore\Admin\Actions\Action;
use Hanson\LaravelAdminWechat\Facades\ConfigService;
use Hanson\LaravelAdminWechat\Jobs\ImportCards as ImportCardsJob;
use Illuminate\Http\Request;

class ImportCards extends Action
{
    public $name = 'import cards';

    protected $selector = '.import-cards';

    public function handle(Request $request)
    {
        ImportCardsJob::dispatch(ConfigService::getCurrent()->app_id);

        return $this->response()->success('后台同步卡券中，请耐心等待')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-success import-cards"><i class="fa fa-refresh"></i> 同步卡券</a>
HTML;
    }
}
