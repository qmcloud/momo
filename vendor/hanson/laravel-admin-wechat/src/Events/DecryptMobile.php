<?php

namespace Hanson\LaravelAdminWechat\Events;

use Hanson\LaravelAdminWechat\Models\WechatUser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DecryptMobile
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var array
     */
    public $decryptedData;

    /**
     * @var WechatUser
     */
    public $wechatUser;

    /**
     * Create a new event instance.
     *
     * @param array $decryptedData
     * @param WechatUser $wechatUser
     */
    public function __construct(array $decryptedData, WechatUser $wechatUser)
    {
        $this->decryptedData = $decryptedData;
        $this->wechatUser = $wechatUser;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
