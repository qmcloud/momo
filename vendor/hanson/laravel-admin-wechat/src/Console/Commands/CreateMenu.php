<?php

namespace Hanson\LaravelAdminWechat\Console\Commands;

use Encore\Admin\Auth\Database\Menu;
use Illuminate\Console\Command;

class CreateMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wechat:menu';

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
        $menu = Menu::query()->firstOrCreate([
            'title' => '微信管理',
        ],[
            'parent_id' => 0,
            'order' => 80,
            'icon' => 'fa-wechat',
        ]);

        Menu::query()->firstOrCreate([
            'title' => '微信配置',
        ],[
            'parent_id' => $menu->id,
            'order' => 80,
            'icon' => 'fa-cog',
            'uri' => 'wechat/configs'
        ]);

        $officialAccountMenu = Menu::query()->firstOrCreate([
            'title' => '公众号',
        ],[
            'parent_id' => $menu->id,
            'order' => 80,
            'icon' => '',
        ]);

        $miniProgramMenu = Menu::query()->firstOrCreate([
            'title' => '小程序',
        ],[
            'parent_id' => $menu->id,
            'order' => 80,
            'icon' => '',
        ]);

        $paymentMenu = Menu::query()->firstOrCreate([
            'title' => '微信支付',
        ],[
            'parent_id' => $menu->id,
            'order' => 80,
            'icon' => '',
        ]);

        //  公众号
        Menu::query()->firstOrCreate([
            'title' => '用户',
            'parent_id' => $officialAccountMenu->id,
        ],[
            'order' => 80,
            'icon' => 'fa-user',
            'uri' => 'wechat/official-account/users'
        ]);

        Menu::query()->firstOrCreate([
            'title' => '菜单',
            'parent_id' => $officialAccountMenu->id,
        ],[
            'order' => 80,
            'icon' => 'fa-bars',
            'uri' => 'wechat/official-account/menu'
        ]);

        Menu::query()->firstOrCreate([
            'title' => '卡券',
            'parent_id' => $officialAccountMenu->id,
        ],[
            'order' => 80,
            'icon' => 'fa-credit-card-alt',
            'uri' => 'wechat/official-account/cards'
        ]);

        // 小程序
        Menu::query()->firstOrCreate([
            'title' => '用户',
            'parent_id' => $miniProgramMenu->id,
        ],[
            'order' => 80,
            'icon' => 'fa-user',
            'uri' => 'wechat/official-account/users'
        ]);

        // 微信支付
        Menu::query()->firstOrCreate([
            'title' => '商户号',
            'parent_id' => $paymentMenu->id,
        ],[
            'order' => 80,
            'icon' => 'fa-user',
            'uri' => 'wechat/payment/merchants'
        ]);


        $this->info('菜单生成完毕');
    }
}
