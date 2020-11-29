<?php

namespace App\Admin\Extensions\Nav;

use Encore\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

class AutoRefresh implements Renderable
{
    public function render()
    {
        $message = __('admin.refresh_succeeded');

        $script = <<<SCRIPT
$('.auto-refresh-trigger').off('click').on('click', function () {

    if (typeof $.admin.autoRefresh !== 'undefined') {
        window.clearInterval($.admin.autoRefresh);
        delete($.admin.autoRefresh)
        
        $(".auto-refresh .interval-text").html('');
    }
    
    $.admin.autoRefresh = window.setInterval(function(){
        $.admin.reload();
        $.admin.toastr.success('{$message}', '', {positionClass:"toast-bottom-right"});
    }, $(this).data('interval') * 1000);
    
    $(".auto-refresh .interval-text").html($(this).html());
    
    $(".auto-refresh i").removeClass("fa-play").addClass("fa-pause refresh-pause");
});

$('.auto-refresh').off('click').on('click', '.refresh-pause', function (e) {
    e.preventDefault();

    $(".auto-refresh i").addClass("fa-play").removeClass("fa-pause refresh-pause");
    
    if (typeof $.admin.autoRefresh !== 'undefined') {
        window.clearInterval($.admin.autoRefresh);
        delete($.admin.autoRefresh)
        
        $(".auto-refresh .interval-text").html('');
    }
});

SCRIPT;

        Admin::script($script);

        return <<<HTML
<li class="dropdown">
    <a href="#" class="dropdown-toggle auto-refresh" data-toggle="dropdown" title="Auto refresh">
        <i class="fa fa-play"></i>&nbsp;&nbsp;
        <span class="interval-text"></span>
    </a>
    <ul class="dropdown-menu" style="width: 30px !important;">
        <li><a href="#" class="auto-refresh-trigger" data-interval=2>2秒钟</a></li>
        <li><a href="#" class="auto-refresh-trigger" data-interval=5>5秒钟</a></li>
        <li><a href="#" class="auto-refresh-trigger" data-interval=10>10秒钟</a></li>
        <li><a href="#" class="auto-refresh-trigger" data-interval=30>30秒钟</a></li>
        <li><a href="#" class="auto-refresh-trigger" data-interval=60>1分钟</a></li>
    </ul>
</li>

HTML;

    }
}