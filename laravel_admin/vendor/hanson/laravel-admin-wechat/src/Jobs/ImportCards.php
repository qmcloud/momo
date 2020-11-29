<?php

namespace Hanson\LaravelAdminWechat\Jobs;

use Carbon\Carbon;
use Hanson\LaravelAdminWechat\Exceptions\WechatException;
use Hanson\LaravelAdminWechat\Facades\ConfigService;
use Hanson\LaravelAdminWechat\Models\WechatCard;
use Hanson\LaravelAdminWechat\Models\WechatUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportCards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var string
     */
    private $appId;

    /**
     * Create a new job instance.
     *
     * @param string $appId
     */
    public function __construct(string $appId)
    {
        $this->appId = $appId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $app = ConfigService::getInstanceByAppId($this->appId);

        $offset = 0;

        while (true) {
            $list = $app->card->list($offset, 20, null);

            $offset+= 20;

            if ($list['errcode'] != 0) {
                throw new WechatException($list['errmsg']);
            }

            logger(count($list['card_id_list']));

            if ($list['card_id_list']) {
                foreach ($list['card_id_list'] as $id) {
                    $card = $app->card->get($id)['card'];

                    $type = strtolower($card['card_type']);

                    $baseInfo = $card[$type]['base_info'];

                    WechatCard::query()->updateOrCreate([
                        'card_id' => $baseInfo['id'],
                        'app_id' => $this->appId,
                    ], [
                        'card_type' => $card['card_type'],
                        'logo_url' => $baseInfo['logo_url'],
                        'code_type' => $baseInfo['code_type'],
                        'brand_name' => $baseInfo['brand_name'],
                        'title' => $baseInfo['title'],
                        'color' => $baseInfo['color'],
                        'notice' => $baseInfo['notice'],
                        'description' => $baseInfo['description'],
                        'sku' => $baseInfo['sku'],
                        'date_info' => $baseInfo['date_info'],
                    ]);
                }
            } else {
                return;
            }
        }
    }
}
