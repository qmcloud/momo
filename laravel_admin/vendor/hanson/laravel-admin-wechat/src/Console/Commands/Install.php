<?php

namespace Hanson\LaravelAdminWechat\Console\Commands;

use Encore\Admin\Auth\Database\Menu;
use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wechat:install {--m|migrate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成所需的菜单数据记录';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('vendor:publish', ['--tag' => 'laravel-admin-wechat', '--force' => true]);

        $this->call('wechat:menu');

        if ($this->option('migrate')) {
            $this->call('migrate', ['--force' => true]);
        }

        $this->info('wechat 扩展安装完毕');
    }
}
