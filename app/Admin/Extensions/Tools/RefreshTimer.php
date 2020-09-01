<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class RefreshTimer extends AbstractTool
{
    protected $timeout;

    public function __construct($timeout)
    {
        $this->timeout = $timeout;
    }
    
    public function render()
    {
        $state = Request::get('_timer', 0);

        $icon = $state ? 'pause' : 'play';

        Admin::script($this->script());

        return <<<EOT

<div class="btn-group">
    <a class="btn btn-sm btn-primary grid-refresh-start" data-state="$state">
        <i class="fa fa-$icon"></i>
    </a>
</div>

EOT;
    }

    protected function script()
    {
        $message = trans('admin::lang.refresh_succeeded');

        $url = Request::fullUrlWithQuery(['_timer' => 1]);

        return <<<EOT

(function () {
    var start = function () {

        console.log('refresh starting...');

        $('.grid-refresh-start').data('state', 1);

        setTimeout(function() {
            if ($('.grid-refresh-start').data('state') == 1) {
                $.pjax({container:'#pjax-container', url:"$url"});
                toastr.success('{$message}', null, {timeOut: 2000});
            }

        }, {$this->timeout});
    }

    var pause = function () {
        console.log('refresh pausing...');
        $('.grid-refresh-start').data('state', 0);
    }

    if ($('.grid-refresh-start').data('state') == 1) {
        start();
    }

    $('.grid-refresh-start').on('click', function () {

        if ($(this).data('state') == 0) {
            start();
        } else {
            pause();
        }

        $("i", this).toggleClass("fa-play fa-pause");
    });
})();

EOT;

    }
}
