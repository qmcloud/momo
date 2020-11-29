<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Settings;
use App\Admin\Forms\Configpris;
use App\Admin\Forms\Steps;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\MultipleSteps;
use Encore\Admin\Widgets;

class FormController extends Controller
{

    public function settings(Content $content)
    {
        return $content
            ->title('网站设置')
            ->body(Widgets\Tab::forms([
                'basic'    => Settings\Basic::class,
                'seo'     => Settings\Seo::class,
                'app'     => Settings\App::class,
                'login'     => Settings\Login::class,
                'share'     => Settings\Share::class,
                'upload'   => Settings\Upload::class,
                'database' => Settings\Database::class,
                'develop'  => Settings\Develop::class,
            ]));
    }

    public function configpris(Content $content)
    {
        return $content
            ->title('私密设置')
            ->body(Widgets\Tab::forms([
                'login'     => Configpris\Login::class,
                'live'     => Configpris\Live::class,//直播
                'video'  => Configpris\Video::class,
                'cash'     => Configpris\Cash::class, //提现
                'push'     => Configpris\Push::class,
                'pay'   => Configpris\Pay::class,
                'invite' => Configpris\Invite::class,
                'statistics'  => Configpris\Statistics::class,
                'privates'  => Configpris\Privates::class,
                'dynamic'  => Configpris\Dynamic::class,
                'basic'    => Configpris\Basic::class,
                'game'  => Configpris\Game::class,
            ]));
    }


    public function register(Content $content)
    {
        return $content
            ->title('Multiple step form')
            ->body(MultipleSteps::make([
                'info'     => Steps\Info::class,
                'profile'  => Steps\Profile::class,
                'password' => Steps\Password::class,
            ]));
    }

    protected function dumpRequest(Content $content)
    {
        $parameters = request()->except(['_pjax', '_token']);

        if (!empty($parameters)) {

            ob_start();

            dump($parameters);

            $contents = ob_get_contents();

            ob_end_clean();

            $content->row(new Widgets\Box('Form parameters', $contents));
        }
    }
}
